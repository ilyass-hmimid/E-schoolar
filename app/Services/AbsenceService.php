<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsenceService
{
    /**
     * Enregistre une absence pour un étudiant
     */
    public function enregistrerAbsence(int $etudiantId, int $seanceId, ?string $justificatif = null, ?string $commentaire = null): Absence
    {
        return DB::transaction(function () use ($etudiantId, $seanceId, $justificatif, $commentaire) {
            // Vérifier si l'absence existe déjà
            $absence = Absence::where('IdEtu', $etudiantId)
                ->where('IdSeance', $seanceId)
                ->first();

            if ($absence) {
                // Mettre à jour l'absence existante
                $absence->update([
                    'justificatif' => $justificatif,
                    'commentaire' => $commentaire,
                    'date_modification' => now(),
                ]);
                return $absence;
            }

            // Créer une nouvelle absence
            return Absence::create([
                'IdEtu' => $etudiantId,
                'IdSeance' => $seanceId,
                'date_absence' => now(),
                'justificatif' => $justificatif,
                'commentaire' => $commentaire,
                'date_creation' => now(),
                'date_modification' => now(),
            ]);
        });
    }

    /**
     * Marque un étudiant comme présent
     */
    public function marquerPresent(int $etudiantId, int $seanceId): bool
    {
        return Absence::where('IdEtu', $etudiantId)
            ->where('IdSeance', $seanceId)
            ->delete() > 0;
    }

    /**
     * Vérifie si un étudiant est absent à une séance donnée
     */
    public function estAbsent(int $etudiantId, int $seanceId): bool
    {
        return Absence::where('IdEtu', $etudiantId)
            ->where('IdSeance', $seanceId)
            ->exists();
    }

    /**
     * Récupère les statistiques d'absences pour un étudiant
     */
    public function getStatistiquesEtudiant(int $etudiantId, ?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $query = Absence::where('IdEtu', $etudiantId)
            ->with(['seance.matiere', 'seance.professeur']);

        if ($debut) {
            $query->whereDate('date_absence', '>=', $debut);
        }

        if ($fin) {
            $query->whereDate('date_absence', '<=', $fin);
        }

        $absences = $query->get();

        $parMatiere = $absences->groupBy('seance.IdMat')
            ->map(function ($absencesParMatiere) {
                $matiere = $absencesParMatiere->first()->seance->matiere;
                $professeur = $absencesParMatiere->first()->seance->professeur;
                
                return [
                    'matiere_id' => $matiere->id,
                    'matiere_libelle' => $matiere->Libelle,
                    'professeur_nom' => $professeur ? $professeur->Nom . ' ' . $professeur->Prenom : 'Inconnu',
                    'nombre_absences' => $absencesParMatiere->count(),
                    'absences' => $absencesParMatiere->map(function ($absence) {
                        return [
                            'id' => $absence->id,
                            'date_absence' => $absence->date_absence->format('d/m/Y H:i'),
                            'justificatif' => $absence->justificatif,
                            'commentaire' => $absence->commentaire,
                            'seance_id' => $absence->IdSeance,
                        ];
                    }),
                ];
            });

        return [
            'total_absences' => $absences->count(),
            'absences_justifiees' => $absences->whereNotNull('justificatif')->count(),
            'absences_non_justifiees' => $absences->whereNull('justificatif')->count(),
            'par_matiere' => $parMatiere,
        ];
    }

    /**
     * Récupère les statistiques d'absences pour une classe ou un groupe d'étudiants
     */
    public function getStatistiquesGroupe(array $etudiantsIds, ?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $query = Absence::whereIn('IdEtu', $etudiantsIds)
            ->with(['etudiant', 'seance.matiere']);

        if ($debut) {
            $query->whereDate('date_absence', '>=', $debut);
        }

        if ($fin) {
            $query->whereDate('date_absence', '<=', $fin);
        }

        $absences = $query->get();

        $parEtudiant = $absences->groupBy('IdEtu')
            ->map(function ($absencesParEtudiant) {
                $etudiant = $absencesParEtudiant->first()->etudiant;
                
                return [
                    'etudiant_id' => $etudiant->id,
                    'etudiant_nom' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                    'total_absences' => $absencesParEtudiant->count(),
                    'absences_justifiees' => $absencesParEtudiant->whereNotNull('justificatif')->count(),
                    'absences_non_justifiees' => $absencesParEtudiant->whereNull('justificatif')->count(),
                ];
            });

        return [
            'total_absences' => $absences->count(),
            'moyenne_absences_par_etudiant' => $parEtudiant->count() > 0 ? round($absences->count() / $parEtudiant->count(), 2) : 0,
            'par_etudiant' => $parEtudiant->sortBy('etudiant_nom')->values(),
        ];
    }

    /**
     * Génère un rapport d'absences pour une période donnée
     */
    public function genererRapportAbsences(Carbon $debut, Carbon $fin, ?int $matiereId = null, ?int $classeId = null): array
    {
        $query = Absence::with(['etudiant', 'seance.matiere', 'seance.professeur'])
            ->whereBetween('date_absence', [$debut, $fin]);

        if ($matiereId) {
            $query->whereHas('seance', function ($q) use ($matiereId) {
                $q->where('IdMat', $matiereId);
            });
        }

        if ($classeId) {
            $query->whereHas('etudiant', function ($q) use ($classeId) {
                $q->where('IdClasse', $classeId);
            });
        }

        $absences = $query->get();

        return [
            'periode' => [
                'debut' => $debut->format('d/m/Y'),
                'fin' => $fin->format('d/m/Y'),
            ],
            'total_absences' => $absences->count(),
            'absences_justifiees' => $absences->whereNotNull('justificatif')->count(),
            'absences_non_justifiees' => $absences->whereNull('justificatif')->count(),
            'details' => $absences->map(function ($absence) {
                return [
                    'id' => $absence->id,
                    'etudiant' => $absence->etudiant ? $absence->etudiant->Nom . ' ' . $absence->etudiant->Prenom : 'Inconnu',
                    'matiere' => $absence->seance && $absence->seance->matiere ? $absence->seance->matiere->Libelle : 'Inconnue',
                    'professeur' => $absence->seance && $absence->seance->professeur ? 
                        $absence->seance->professeur->Nom . ' ' . $absence->seance->professeur->Prenom : 'Inconnu',
                    'date_absence' => $absence->date_absence->format('d/m/Y H:i'),
                    'est_justifiee' => !is_null($absence->justificatif),
                    'commentaire' => $absence->commentaire,
                ];
            })->sortBy('date_absence')->values(),
        ];
    }

    /**
     * Envoie des notifications aux parents pour les absences non justifiées
     */
    public function notifierParentsAbsencesNonJustifiees(int $seuilJours = 3): array
    {
        $dateLimite = now()->subDays($seuilJours);
        
        $absencesANotifier = Absence::with(['etudiant.parent', 'seance.matiere'])
            ->whereNull('notifie_le')
            ->whereNull('justificatif')
            ->where('date_absence', '<=', $dateLimite)
            ->get()
            ->groupBy('etudiant.parent.id');
        
        $resultats = [];
        
        foreach ($absencesANotifier as $parentId => $absences) {
            $parent = $absences->first()->etudiant->parent;
            
            if (!$parent) {
                continue;
            }
            
            // Ici, vous pourriez implémenter l'envoi d'email ou de SMS
            // Par exemple :
            // Notification::send($parent, new AbsenceNonJustifieeNotification($absences));
            
            // Marquer les absences comme notifiées
            Absence::whereIn('id', $absences->pluck('id'))
                ->update(['notifie_le' => now()]);
            
            $resultats[] = [
                'parent_id' => $parentId,
                'parent_nom' => $parent->Nom . ' ' . $parent->Prenom,
                'email' => $parent->email,
                'telephone' => $parent->Telephone,
                'nb_absences' => $absences->count(),
                'etudiants' => $absences->groupBy('etudiant.id')->map(function ($absencesParEtudiant) {
                    $etudiant = $absencesParEtudiant->first()->etudiant;
                    return [
                        'id' => $etudiant->id,
                        'nom' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                        'absences' => $absencesParEtudiant->map(function ($absence) {
                            return [
                                'id' => $absence->id,
                                'date_absence' => $absence->date_absence->format('d/m/Y H:i'),
                                'matiere' => $absence->seance && $absence->seance->matiere ? 
                                    $absence->seance->matiere->Libelle : 'Inconnue',
                            ];
                        }),
                    ];
                })->values(),
            ];
        }
        
        return $resultats;
    }
}
