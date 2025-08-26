<?php

namespace App\Services;

use App\Models\Salaire;
use App\Models\ConfigurationSalaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GestionSalaireService
{
    /**
     * Calcule les salaires des professeurs pour un mois donné
     */
    public function calculerSalaires(string $moisPeriode): array
    {
        // Parse the month and set date range
        $dateDebut = Carbon::createFromFormat('Y-m', $moisPeriode)->startOfMonth();
        $dateFin = $dateDebut->copy()->endOfMonth();
        
        // Initialize result array
        $result = [
            'success' => false,
            'message' => '',
            'count' => 0,
        ];

        // Vérifier si un calcul existe déjà pour ce mois
        $existingCalculation = Salaire::where('mois_periode', $moisPeriode)->exists();
        
        if ($existingCalculation) {
            $result['message'] = 'Un calcul existe déjà pour le mois de ' . $this->formatMonth($moisPeriode);
            return $result;
        }

        // Récupérer les configurations de salaires actives
        $configurations = ConfigurationSalaire::with('matiere')
            ->where('est_actif', true)
            ->get();

        if ($configurations->isEmpty()) {
            $result['message'] = 'Aucune configuration de salaire active trouvée';
            return $result;
        }

        // Récupérer les professeurs actifs en utilisant le scope
        $professeurs = User::professeurs()
            ->where('is_active', true)
            ->get();

        if ($professeurs->isEmpty()) {
            $result['message'] = 'Aucun professeur actif trouvé';
            return $result;
        }

        \Log::info('Début du calcul des salaires', [
            'professeurs_count' => $professeurs->count(),
            'configurations_count' => $configurations->count(),
            'date_debut' => $dateDebut->toDateString(),
            'date_fin' => $dateFin->toDateString()
        ]);

        // Debug: Log all enseignements
        $enseignements = DB::table('enseignements')->get();
        \Log::info('Tous les enseignements', ['enseignements' => $enseignements]);

        $salairesCrees = 0;

        DB::beginTransaction();

        try {
            foreach ($professeurs as $professeur) {
                // Debug: Log the current professor
                \Log::info('Traitement du professeur', ['professeur_id' => $professeur->id]);

                // Récupérer les matières enseignées par le professeur
                $matieresEnseignees = $professeur->matieresEnseignees()
                    ->wherePivot('date_debut', '<=', $dateFin)
                    ->where(function ($query) use ($dateDebut) {
                        $query->whereNull('date_fin')
                            ->orWhere('date_fin', '>=', $dateDebut);
                    })
                    ->get();

                \Log::info('Matières enseignées par le professeur', [
                    'professeur_id' => $professeur->id,
                    'matieres_count' => $matieresEnseignees->count(),
                    'matieres' => $matieresEnseignees->pluck('id')->toArray()
                ]);

                foreach ($matieresEnseignees as $matiere) {
                    $configuration = $configurations->firstWhere('matiere_id', $matiere->id);
                    
                    if (!$configuration) {
                        \Log::info('Aucune configuration de salaire trouvée pour la matière', [
                            'matiere_id' => $matiere->id,
                            'professeur_id' => $professeur->id
                        ]);
                        continue; // Pas de configuration pour cette matière
                    }

                    // Compter le nombre d'élèves pour cette matière et ce professeur
                    $nombreEleves = $this->compterElevesParMatiere($professeur->id, $matiere->id, $dateDebut, $dateFin);
                    \Log::info('Nombre d\'élèves pour la matière', [
                        'matiere_id' => $matiere->id,
                        'professeur_id' => $professeur->id,
                        'nombre_eleves' => $nombreEleves
                    ]);
                    
                    if ($nombreEleves === 0) {
                        \Log::info('Aucun élève trouvé pour ce professeur et cette matière', [
                            'professeur_id' => $professeur->id,
                            'matiere_id' => $matiere->id
                        ]);
                        continue; // Aucun élève pour cette matière
                    }

                    // Calculer les montants
                    $montantBrut = $nombreEleves * $configuration->prix_unitaire;
                    $montantCommission = ($montantBrut * $configuration->commission_prof) / 100;
                    $montantNet = $montantBrut - $montantCommission;
                    
                    \Log::info('Calcul du salaire', [
                        'professeur_id' => $professeur->id,
                        'matiere_id' => $matiere->id,
                        'nombre_eleves' => $nombreEleves,
                        'prix_unitaire' => $configuration->prix_unitaire,
                        'commission_prof' => $configuration->commission_prof,
                        'montant_brut' => $montantBrut,
                        'montant_commission' => $montantCommission,
                        'montant_net' => $montantNet
                    ]);

                    // Debug: Log the values before saving
                    \Log::info('Creating salary record', [
                        'professeur_id' => $professeur->id,
                        'matiere_id' => $matiere->id,
                        'mois_periode' => $moisPeriode,
                        'nombre_eleves' => $nombreEleves,
                        'prix_unitaire' => $configuration->prix_unitaire,
                        'commission_prof' => $configuration->commission_prof,
                        'montant_brut' => $montantBrut,
                        'montant_commission' => $montantCommission,
                        'montant_net' => $montantNet,
                    ]);

                    // Créer le salaire
                    $salaire = Salaire::create([
                        'professeur_id' => $professeur->id,
                        'matiere_id' => $matiere->id,
                        'mois_periode' => $moisPeriode,
                        'nombre_eleves' => $nombreEleves,
                        'prix_unitaire' => $configuration->prix_unitaire,
                        'commission_prof' => $configuration->commission_prof,
                        'montant_brut' => $montantBrut,
                        'montant_commission' => $montantCommission,
                        'montant_net' => $montantNet,
                        'statut' => 'en_attente',
                    ]);
                    
                    // Debug: Log the actual saved values
                    \Log::info('Salary record created', [
                        'salaire_id' => $salaire->id,
                        'saved_montant_brut' => $salaire->montant_brut,
                        'saved_montant_commission' => $salaire->montant_commission,
                        'saved_montant_net' => $salaire->montant_net,
                    ]);

                    $salairesCrees++;
                }
            }

            DB::commit();

            $result['success'] = true;
            $result['message'] = "$salairesCrees salaires ont été calculés avec succès pour le mois de " . $this->formatMonth($moisPeriode);
            $result['count'] = $salairesCrees;

        } catch (\Exception $e) {
            DB::rollBack();
            $result['message'] = 'Une erreur est survenue lors du calcul des salaires : ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Valide le paiement d'un salaire
     */
    public function validerPaiement(Salaire $salaire, string $datePaiement = null, string $commentaires = null): array
    {
        if ($salaire->statut !== 'en_attente') {
            return [
                'success' => false,
                'message' => 'Le salaire ne peut pas être validé car il n\'est pas en attente de paiement.'
            ];
        }

        try {
            $salaire->update([
                'statut' => 'paye',
                'date_paiement' => $datePaiement ?: now(),
                'commentaires' => $commentaires ?: 'Paiement validé le ' . now()->format('d/m/Y'),
            ]);

            return [
                'success' => true,
                'message' => 'Le paiement a été validé avec succès.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la validation du paiement : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Annule un salaire
     */
    public function annulerSalaire(Salaire $salaire, string $raison = null): array
    {
        if ($salaire->statut === 'annule') {
            return [
                'success' => false,
                'message' => 'Ce salaire a déjà été annulé.'
            ];
        }

        try {
            $commentaires = 'Annulé' . ($raison ? ' - ' . $raison : '') . ' le ' . now()->format('d/m/Y');
            
            $salaire->update([
                'statut' => 'annule',
                'commentaires' => $commentaires,
            ]);

            return [
                'success' => true,
                'message' => 'Le salaire a été annulé avec succès.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'annulation du salaire : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Compte le nombre d'élèves pour une matière et un professeur donnés sur une période
     */
    protected function compterElevesParMatiere(int $professeurId, int $matiereId, Carbon $dateDebut, Carbon $dateFin): int
    {
        // Récupérer l'enseignement pour ce professeur et cette matière
        $enseignement = DB::table('enseignements')
            ->where('professeur_id', $professeurId)
            ->where('matiere_id', $matiereId)
            ->where('est_actif', true)
            ->where('date_debut', '<=', $dateFin)
            ->where(function($query) use ($dateDebut) {
                $query->whereNull('date_fin')
                    ->orWhere('date_fin', '>=', $dateDebut);
            })
            ->first();

        \Log::info('Enseignement trouvé', [
            'professeur_id' => $professeurId,
            'matiere_id' => $matiereId,
            'date_debut' => $dateDebut->toDateString(),
            'date_fin' => $dateFin->toDateString(),
            'enseignement' => $enseignement
        ]);

        if (!$enseignement) {
            return 0;
        }

        // Compter les étudiants inscrits dans la filière de cet enseignement
        $count = DB::table('inscriptions')
            ->where('IdFil', $enseignement->filiere_id)
            ->where('DateInsc', '<=', $dateFin)
            ->where(function($query) use ($dateDebut) {
                $query->whereNull('date_expiration')
                    ->orWhere('date_expiration', '>=', $dateDebut);
            })
            ->where('Statut', 'actif')
            ->count();

        \Log::info('Nombre d\'élèves trouvés', [
            'filiere_id' => $enseignement->filiere_id,
            'date_debut' => $dateDebut->toDateString(),
            'date_fin' => $dateFin->toDateString(),
            'count' => $count
        ]);

        return $count;
    }

    /**
     * Formate un mois (YYYY-MM) en texte (ex: Janvier 2023)
     */
    protected function formatMonth(string $month): string
    {
        return Carbon::parse($month . '-01')->translatedFormat('F Y');
    }
}
