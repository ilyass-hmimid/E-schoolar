<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Paiment;
use App\Models\Salaires;
use App\Models\Pack;
use App\Models\Inscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RapportController extends Controller
{
    /**
     * Affiche la page de génération de rapports
     */
    public function index()
    {
        $this->authorize('viewAny', self::class);
        
        return Inertia::render('Rapports/Index', [
            'matieres' => Matiere::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
            'classes' => Classe::select('id', 'Libelle')
                ->orderBy('Libelle')
                ->get(),
            'types_rapport' => [
                ['id' => 'absences', 'libelle' => 'Rapport des absences'],
                ['id' => 'paiements', 'libelle' => 'Rapport des paiements'],
                ['id' => 'salaires', 'libelle' => 'Rapport des salaires'],
                ['id' => 'inscriptions', 'libelle' => 'Rapport des inscriptions'],
                ['id' => 'resultats', 'libelle' => 'Rapport des résultats'],
                ['id' => 'effectifs', 'libelle' => 'Effectifs par classe'],
            ],
            'formats_export' => [
                ['id' => 'csv', 'libelle' => 'CSV (Excel)'],
                ['id' => 'pdf', 'libelle' => 'PDF'],
                ['id' => 'excel', 'libelle' => 'Excel (XLSX)'],
            ],
        ]);
    }
    
    /**
     * Génère un rapport en fonction des critères sélectionnés
     */
    public function genererRapport(Request $request)
    {
        $this->authorize('generate', self::class);
        
        $validated = $request->validate([
            'type_rapport' => 'required|in:absences,paiements,salaires,inscriptions,resultats,effectifs',
            'format_export' => 'required|in:csv,pdf,excel',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'matiere_id' => 'nullable|exists:Matiere,id',
            'classe_id' => 'nullable|exists:Classe,id',
        ]);
        
        $dateDebut = !empty($validated['date_debut']) 
            ? Carbon::parse($validated['date_debut'])
            : Carbon::now()->startOfMonth();
            
        $dateFin = !empty($validated['date_fin'])
            ? Carbon::parse($validated['date_fin'])
            : Carbon::now();
        
        $donnees = [];
        $nomFichier = '';
        $entetes = [];
        
        switch ($validated['type_rapport']) {
            case 'absences':
                list($donnees, $entetes, $nomFichier) = $this->genererRapportAbsences(
                    $dateDebut, 
                    $dateFin,
                    $validated['classe_id'] ?? null,
                    $validated['matiere_id'] ?? null
                );
                break;
                
            case 'paiements':
                list($donnees, $entetes, $nomFichier) = $this->genererRapportPaiements(
                    $dateDebut,
                    $dateFin,
                    $validated['classe_id'] ?? null
                );
                break;
                
            case 'salaires':
                list($donnees, $entetes, $nomFichier) = $this->genererRapportSalaires(
                    $dateDebut,
                    $dateFin
                );
                break;
                
            case 'inscriptions':
                list($donnees, $entetes, $nomFichier) = $this->genererRapportInscriptions(
                    $dateDebut,
                    $dateFin,
                    $validated['classe_id'] ?? null
                );
                break;
                
            case 'resultats':
                list($donnees, $entetes, $nomFichier) = $this->genererRapportResultats(
                    $dateDebut,
                    $dateFin,
                    $validated['classe_id'] ?? null,
                    $validated['matiere_id'] ?? null
                );
                break;
                
            case 'effectifs':
                list($donnees, $entetes, $nomFichier) = $this->genererRapportEffectifs();
                break;
        }
        
        $nomFichier .= '_' . now()->format('Y-m-d_His') . '.' . $validated['format_export'];
        
        // Pour l'instant, on ne gère que le format CSV
        // Dans une version ultérieure, on pourrait ajouter la génération de PDF et Excel
        if ($validated['format_export'] === 'csv') {
            return $this->exporterEnCSV($donnees, $entetes, $nomFichier);
        }
        
        // Pour les autres formats, on retourne les données en JSON pour l'instant
        return response()->json([
            'message' => 'Export en ' . strtoupper($validated['format_export']) . ' non implémenté pour le moment',
            'donnees' => $donnees,
            'entetes' => $entetes,
            'nom_fichier' => $nomFichier,
        ]);
    }
    
    /**
     * Génère un rapport des absences
     */
    private function genererRapportAbsences($dateDebut, $dateFin, $classeId = null, $matiereId = null)
    {
        $query = Absence::with([
                'etudiant:id,Nom,Prenom',
                'seance.matiere:id,Libelle',
                'seance.professeur:id,Nom,Prenom'
            ])
            ->whereBetween('date_absence', [$dateDebut, $dateFin]);
            
        if ($classeId) {
            $query->whereHas('etudiant', function($q) use ($classeId) {
                $q->where('IdClasse', $classeId);
            });
        }
        
        if ($matiereId) {
            $query->whereHas('seance', function($q) use ($matiereId) {
                $q->where('IdMat', $matiereId);
            });
        }
        
        $absences = $query->orderBy('date_absence')
            ->get()
            ->map(function($absence) {
                return [
                    'date' => $absence->date_absence->format('d/m/Y H:i'),
                    'etudiant' => $absence->etudiant ? $absence->etudiant->Nom . ' ' . $absence->etudiant->Prenom : 'Inconnu',
                    'matiere' => $absence->seance && $absence->seance->matiere ? $absence->seance->matiere->Libelle : 'Inconnue',
                    'professeur' => $absence->seance && $absence->seance->professeur 
                        ? $absence->seance->professeur->Nom . ' ' . $absence->seance->professeur->Prenom 
                        : 'Inconnu',
                    'justifiee' => !empty($absence->justificatif) ? 'Oui' : 'Non',
                    'commentaire' => $absence->commentaire ?? '',
                ];
            });
            
        $entetes = [
            'Date et heure',
            'Étudiant',
            'Matière',
            'Professeur',
            'Justifiée',
            'Commentaire'
        ];
        
        $nomFichier = 'rapport_absences';
        $nomFichier .= $classeId ? '_classe_' . $classeId : '';
        $nomFichier .= $matiereId ? '_matiere_' . $matiereId : '';
        
        return [$absences, $entetes, $nomFichier];
    }
    
    /**
     * Génère un rapport des paiements
     */
    private function genererRapportPaiements($dateDebut, $dateFin, $classeId = null)
    {
        $query = Paiment::with([
                'etudiant:id,Nom,Prenom',
                'inscription.pack:id,Libelle'
            ])
            ->whereBetween('Date_Paiment', [$dateDebut, $dateFin]);
            
        if ($classeId) {
            $query->whereHas('etudiant', function($q) use ($classeId) {
                $q->where('IdClasse', $classeId);
            });
        }
        
        $paiements = $query->orderBy('Date_Paiment')
            ->get()
            ->map(function($paiement) {
                return [
                    'date' => $paiement->Date_Paiment->format('d/m/Y'),
                    'etudiant' => $paiement->etudiant ? $paiement->etudiant->Nom . ' ' . $paiement->etudiant->Prenom : 'Inconnu',
                    'pack' => $paiement->inscription && $paiement->inscription->pack 
                        ? $paiement->inscription->pack->Libelle 
                        : 'Inconnu',
                    'montant' => number_format($paiement->Montant, 2, ',', ' '),
                    'somme_payee' => number_format($paiement->SommeApaye, 2, ',', ' '),
                    'reste_a_payer' => number_format($paiement->ResteAPayer, 2, ',', ' '),
                    'mode_paiement' => $paiement->Mode_Paiment,
                ];
            });
            
        $entetes = [
            'Date',
            'Étudiant',
            'Pack',
            'Montant total',
            'Somme payée',
            'Reste à payer',
            'Mode de paiement'
        ];
        
        $nomFichier = 'rapport_paiements';
        $nomFichier .= $classeId ? '_classe_' . $classeId : '';
        
        return [$paiements, $entetes, $nomFichier];
    }
    
    /**
     * Génère un rapport des salaires
     */
    private function genererRapportSalaires($dateDebut, $dateFin)
    {
        $salaires = Salaires::with(['professeur:id,Nom,Prenom'])
            ->whereBetween('Date_Salaire', [$dateDebut, $dateFin])
            ->orderBy('Date_Salaire')
            ->get()
            ->map(function($salaire) {
                return [
                    'date' => $salaire->Date_Salaire->format('m/Y'),
                    'professeur' => $salaire->professeur 
                        ? $salaire->professeur->Nom . ' ' . $salaire->professeur->Prenom 
                        : 'Inconnu',
                    'montant_total' => number_format($salaire->Montant_actuel, 2, ',', ' '),
                    'montant_paye' => number_format($salaire->Montant_paye, 2, ',', ' '),
                    'reste_a_payer' => number_format($salaire->Reste_a_payer, 2, ',', ' '),
                    'pourcentage' => $salaire->Pourcentage . '%',
                    'etat' => $salaire->Etat,
                ];
            });
            
        $entetes = [
            'Période',
            'Professeur',
            'Montant total',
            'Montant payé',
            'Reste à payer',
            'Pourcentage',
            'État'
        ];
        
        $nomFichier = 'rapport_salaires';
        
        return [$salaires, $entetes, $nomFichier];
    }
    
    /**
     * Génère un rapport des inscriptions
     */
    private function genererRapportInscriptions($dateDebut, $dateFin, $classeId = null)
    {
        $query = Inscription::with([
                'etudiant:id,Nom,Prenom',
                'pack:id,Libelle',
                'classe:id,Libelle'
            ])
            ->whereBetween('dateInscription', [$dateDebut, $dateFin]);
            
        if ($classeId) {
            $query->where('IdClasse', $classeId);
        }
        
        $inscriptions = $query->orderBy('dateInscription')
            ->get()
            ->map(function($inscription) {
                return [
                    'date' => $inscription->dateInscription->format('d/m/Y'),
                    'etudiant' => $inscription->etudiant 
                        ? $inscription->etudiant->Nom . ' ' . $inscription->etudiant->Prenom 
                        : 'Inconnu',
                    'classe' => $inscription->classe ? $inscription->classe->Libelle : 'Inconnue',
                    'pack' => $inscription->pack ? $inscription->pack->Libelle : 'Aucun',
                    'heures_restantes' => $inscription->heures_restantes ?? 0,
                    'date_expiration' => $inscription->date_expiration 
                        ? $inscription->date_expiration->format('d/m/Y') 
                        : 'Illimitée',
                    'statut' => $inscription->statut,
                ];
            });
            
        $entetes = [
            'Date d\'inscription',
            'Étudiant',
            'Classe',
            'Pack',
            'Heures restantes',
            'Date d\'expiration',
            'Statut'
        ];
        
        $nomFichier = 'rapport_inscriptions';
        $nomFichier .= $classeId ? '_classe_' . $classeId : '';
        
        return [$inscriptions, $entetes, $nomFichier];
    }
    
    /**
     * Génère un rapport des résultats
     */
    private function genererRapportResultats($dateDebut, $dateFin, $classeId = null, $matiereId = null)
    {
        $query = DB::table('Note')
            ->join('Etudiant', 'Note.IdEtu', '=', 'Etudiant.id')
            ->join('Matiere', 'Note.IdMat', '=', 'Matiere.id')
            ->join('Type_Evaluation', 'Note.IdTypeEval', '=', 'Type_Evaluation.id')
            ->leftJoin('Classe', 'Etudiant.IdClasse', '=', 'Classe.id')
            ->select(
                'Etudiant.Nom',
                'Etudiant.Prenom',
                'Classe.Libelle as Classe',
                'Matiere.Libelle as Matiere',
                'Type_Evaluation.Libelle as TypeEvaluation',
                'Type_Evaluation.Coefficient',
                'Type_Evaluation.NoteMaximale',
                'Note.Note',
                'Note.Date_Eval',
                DB::raw('(Note.Note / Type_Evaluation.NoteMaximale * 20) as NoteSur20')
            )
            ->whereBetween('Note.Date_Eval', [$dateDebut, $dateFin]);
            
        if ($classeId) {
            $query->where('Etudiant.IdClasse', $classeId);
        }
        
        if ($matiereId) {
            $query->where('Matiere.id', $matiereId);
        }
        
        $resultats = $query->orderBy('Etudiant.Nom')
            ->orderBy('Etudiant.Prenom')
            ->orderBy('Matiere.Libelle')
            ->orderBy('Note.Date_Eval')
            ->get()
            ->map(function($resultat) {
                return [
                    'etudiant' => $resultat->Nom . ' ' . $resultat->Prenom,
                    'classe' => $resultat->Classe ?? 'Inconnue',
                    'matiere' => $resultat->Matiere,
                    'type_evaluation' => $resultat->TypeEvaluation,
                    'coefficient' => $resultat->Coefficient,
                    'note' => number_format($resultat->Note, 2, ',', ' '),
                    'note_sur_20' => number_format($resultat->NoteSur20, 2, ',', ' '),
                    'date_evaluation' => (new Carbon($resultat->Date_Eval))->format('d/m/Y'),
                ];
            });
            
        $entetes = [
            'Étudiant',
            'Classe',
            'Matière',
            'Type d\'évaluation',
            'Coefficient',
            'Note',
            'Note sur 20',
            'Date d\'évaluation'
        ];
        
        $nomFichier = 'rapport_resultats';
        $nomFichier .= $classeId ? '_classe_' . $classeId : '';
        $nomFichier .= $matiereId ? '_matiere_' . $matiereId : '';
        
        return [$resultats, $entetes, $nomFichier];
    }
    
    /**
     * Génère un rapport des effectifs par classe
     */
    private function genererRapportEffectifs()
    {
        $effectifs = Classe::withCount('etudiants')
            ->orderBy('Libelle')
            ->get()
            ->map(function($classe) {
                return [
                    'classe' => $classe->Libelle,
                    'effectif' => $classe->etudiants_count,
                ];
            });
            
        $entetes = [
            'Classe',
            'Effectif'
        ];
        
        $nomFichier = 'rapport_effectifs';
        
        return [$effectifs, $entetes, $nomFichier];
    }
    
    /**
     * Exporte les données au format CSV
     */
    private function exporterEnCSV($donnees, $entetes, $nomFichier)
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$nomFichier\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($donnees, $entetes) {
            $file = fopen('php://output', 'w');
            
            // Ajout de l'encodage UTF-8 BOM pour un bon affichage des caractères spéciaux dans Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-tête du CSV
            fputcsv($file, $entetes, ';');
            
            // Données
            foreach ($donnees as $ligne) {
                // Convertir les valeurs en chaînes de caractères et gérer l'encodage
                $ligneEncodée = array_map(function($valeur) {
                    // Si la valeur est un tableau ou un objet, la convertir en JSON
                    if (is_array($valeur) || is_object($valeur)) {
                        return json_encode($valeur, JSON_UNESCAPED_UNICODE);
                    }
                    // Sinon, retourner la valeur telle quelle
                    return $valeur;
                }, (array)$ligne);
                
                fputcsv($file, $ligneEncodée, ';');
            }
            
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
    
    /**
     * Affiche la page de statistiques
     */
    public function statistiques()
    {
        $this->authorize('viewStats', self::class);
        
        // Statistiques générales
        $stats = [
            'total_etudiants' => Etudiant::count(),
            'total_professeurs' => Professeur::count(),
            'total_matieres' => Matiere::count(),
            'total_classes' => Classe::count(),
            'total_paiements_mois' => Paiment::whereMonth('Date_Paiment', now()->month)
                ->whereYear('Date_Paiment', now()->year)
                ->sum('SommeApaye'),
            'total_salaires_mois' => Salaires::whereMonth('Date_Salaire', now()->month)
                ->whereYear('Date_Salaire', now()->year)
                ->sum('Montant_actuel'),
            'taux_absenteisme' => $this->calculerTauxAbsenteismeMois(),
        ];
        
        // Évolution des inscriptions sur 12 mois
        $inscriptionsParMois = $this->getInscriptionsParMois(12);
        
        // Répartition des étudiants par classe
        $repartitionClasses = $this->getRepartitionParClasse();
        
        // Taux de réussite par matière (exemple avec les 5 premières matières)
        $tauxReussiteParMatiere = $this->getTauxReussiteParMatiere(5);
        
        return Inertia::render('Rapports/Statistiques', [
            'stats' => $stats,
            'inscriptionsParMois' => $inscriptionsParMois,
            'repartitionClasses' => $repartitionClasses,
            'tauxReussiteParMatiere' => $tauxReussiteParMatiere,
        ]);
    }
    
    /**
     * Calcule le taux d'absentéisme du mois en cours
     */
    private function calculerTauxAbsenteismeMois(): float
    {
        $dateDebut = now()->startOfMonth();
        $dateFin = now()->endOfMonth();
        
        $totalSeances = DB::table('Seance')
            ->whereBetween('Date_Seance', [$dateDebut, $dateFin])
            ->count();
            
        if ($totalSeances === 0) {
            return 0;
        }
        
        $totalAbsences = Absence::whereBetween('date_absence', [$dateDebut, $dateFin])
            ->count();
            
        return round(($totalAbsences / $totalSeances) * 100, 2);
    }
    
    /**
     * Récupère le nombre d'inscriptions par mois sur une période donnée
     */
    private function getInscriptionsParMois(int $nbMois)
    {
        $dateDebut = now()->subMonths($nbMois - 1)->startOfMonth();
        
        $inscriptions = DB::table('Inscription')
            ->select(
                DB::raw('YEAR(dateInscription) as annee'),
                DB::raw('MONTH(dateInscription) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->where('dateInscription', '>=', $dateDebut)
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        // Créer un tableau avec tous les mois de la période
        $periodes = collect();
        $dateCourante = $dateDebut->copy();
        
        while ($dateCourante <= now()) {
            $periodes->push([
                'annee' => $dateCourante->year,
                'mois' => $dateCourante->month,
                'libelle' => $dateCourante->translatedFormat('M Y'),
                'total' => 0
            ]);
            
            $dateCourante->addMonth();
        }
        
        // Mettre à jour les totaux pour les mois avec des inscriptions
        foreach ($inscriptions as $inscription) {
            $index = $periodes->search(function($item) use ($inscription) {
                return $item['annee'] == $inscription->annee && $item['mois'] == $inscription->mois;
            });
            
            if ($index !== false) {
                $periodes[$index]['total'] = $inscription->total;
            }
        }
        
        return $periodes->values();
    }
    
    /**
     * Récupère la répartition des étudiants par classe
     */
    private function getRepartitionParClasse()
    {
        return Classe::withCount('etudiants')
            ->orderBy('Libelle')
            ->get()
            ->map(function($classe) {
                return [
                    'classe' => $classe->Libelle,
                    'effectif' => $classe->etudiants_count,
                ];
            });
    }
    
    /**
     * Calcule le taux de réussite par matière
     */
    private function getTauxReussiteParMatiere($limit = null)
    {
        $query = DB::table('Note')
            ->join('Matiere', 'Note.IdMat', '=', 'Matiere.id')
            ->join('Type_Evaluation', 'Note.IdTypeEval', '=', 'Type_Evaluation.id')
            ->select(
                'Matiere.id',
                'Matiere.Libelle',
                DB::raw('COUNT(*) as total_notes'),
                DB::raw('SUM(CASE WHEN (Note.Note / Type_Evaluation.NoteMaximale * 20) >= 10 THEN 1 ELSE 0 END) as notes_reussies')
            )
            ->groupBy('Matiere.id', 'Matiere.Libelle')
            ->having('total_notes', '>', 0)
            ->orderBy('Matiere.Libelle');
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get()
            ->map(function($item) {
                $tauxReussite = $item->total_notes > 0 
                    ? round(($item->notes_reussies / $item->total_notes) * 100, 2)
                    : 0;
                    
                return [
                    'matiere' => $item->Libelle,
                    'total_notes' => $item->total_notes,
                    'notes_reussies' => $item->notes_reussies,
                    'taux_reussite' => $tauxReussite,
                ];
            });
    }
}
