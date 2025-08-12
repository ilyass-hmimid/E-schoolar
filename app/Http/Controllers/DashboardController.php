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
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur
     */
    public function admin()
    {
        $this->authorize('viewAdminDashboard', User::class);
        
        // Période pour les statistiques (30 derniers jours)
        $dateDebut = now()->subDays(30);
        $dateFin = now();
        
        // Statistiques générales
        $stats = [
            'total_etudiants' => Etudiant::count(),
            'total_professeurs' => Professeur::count(),
            'total_matieres' => Matiere::count(),
            'total_classes' => Classe::count(),
            'paiements_mois' => Paiment::whereBetween('Date_Paiment', [$dateDebut, $dateFin])
                ->sum('SommeApaye'),
            'salaires_mois' => Salaires::whereBetween('Date_Salaire', [$dateDebut, $dateFin])
                ->sum('Montant_actuel'),
            'packs_vendus' => DB::table('Inscription')
                ->whereNotNull('pack_id')
                ->whereBetween('dateInscription', [$dateDebut, $dateFin])
                ->count(),
            'taux_absenteisme' => $this->calculerTauxAbsenteisme($dateDebut, $dateFin),
        ];
        
        // Graphique des inscriptions par mois (6 derniers mois)
        $inscriptionsParMois = $this->getInscriptionsParMois(6);
        
        // Graphique des paiements par mois (6 derniers mois)
        $paiementsParMois = $this->getPaiementsParMois(6);
        
        // Dernières absences non justifiées
        $dernieresAbsences = Absence::with(['etudiant:id,Nom,Prenom', 'seance.matiere:id,Libelle'])
            ->whereNull('justificatif')
            ->orderBy('date_absence', 'desc')
            ->limit(5)
            ->get()
            ->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'etudiant' => $absence->etudiant ? $absence->etudiant->Nom . ' ' . $absence->etudiant->Prenom : 'Inconnu',
                    'matiere' => $absence->seance && $absence->seance->matiere ? $absence->seance->matiere->Libelle : 'Inconnue',
                    'date_absence' => $absence->date_absence->format('d/m/Y H:i'),
                ];
            });
        
        // Derniers paiements
        $derniersPaiements = Paiment::with(['etudiant:id,Nom,Prenom'])
            ->orderBy('Date_Paiment', 'desc')
            ->limit(5)
            ->get()
            ->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'etudiant' => $paiement->etudiant ? $paiement->etudiant->Nom . ' ' . $paiement->etudiant->Prenom : 'Inconnu',
                    'montant' => number_format($paiement->SommeApaye, 2, ',', ' '),
                    'date_paiement' => $paiement->Date_Paiment->format('d/m/Y'),
                ];
            });
        
        return Inertia::render('Dashboard/Admin', [
            'stats' => $stats,
            'inscriptionsParMois' => $inscriptionsParMois,
            'paiementsParMois' => $paiementsParMois,
            'dernieresAbsences' => $dernieresAbsences,
            'derniersPaiements' => $derniersPaiements,
        ]);
    }
    
    /**
     * Calcule le taux d'absentéisme global
     */
    private function calculerTauxAbsenteisme($dateDebut, $dateFin): float
    {
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
     * Récupère les inscriptions par mois pour un nombre de mois donné
     */
    private function getInscriptionsParMois(int $nbMois): array
    {
        $dateDebut = now()->subMonths($nbMois - 1)->startOfMonth();
        $dateFin = now()->endOfMonth();
        
        $inscriptions = DB::table('Inscription')
            ->select(
                DB::raw('YEAR(dateInscription) as annee'),
                DB::raw('MONTH(dateInscription) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('dateInscription', [$dateDebut, $dateFin])
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        return $this->formaterDonneesMensuelles($inscriptions, $dateDebut, $dateFin);
    }
    
    /**
     * Récupère les paiements par mois pour un nombre de mois donné
     */
    private function getPaiementsParMois(int $nbMois): array
    {
        $dateDebut = now()->subMonths($nbMois - 1)->startOfMonth();
        $dateFin = now()->endOfMonth();
        
        $paiements = DB::table('Paiment')
            ->select(
                DB::raw('YEAR(Date_Paiment) as annee'),
                DB::raw('MONTH(Date_Paiment) as mois'),
                DB::raw('SUM(SommeApaye) as total')
            )
            ->whereBetween('Date_Paiment', [$dateDebut, $dateFin])
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        return $this->formaterDonneesMensuelles($paiements, $dateDebut, $dateFin);
    }
    
    /**
     * Formate les données mensuelles pour les graphiques
     */
    private function formaterDonneesMensuelles($donnees, $dateDebut, $dateFin): array
    {
        $periodes = collect(CarbonPeriod::create($dateDebut, '1 month', $dateFin));
        
        $donneesParMois = $donnees->mapWithKeys(function($item) {
            return [
                $item->annee . '-' . str_pad($item->mois, 2, '0', STR_PAD_LEFT) => $item->total ?? 0
            ];
        });
        
        return $periodes->map(function($date) use ($donneesParMois) {
            $cle = $date->year . '-' . str_pad($date->month, 2, '0', STR_PAD_LEFT);
            return [
                'mois' => $date->translatedFormat('M Y'),
                'valeur' => $donneesParMois[$cle] ?? 0,
            ];
        })->toArray();
    }
    
    /**
     * Recherche avancée dans le système
     */
    public function rechercheAvancee(Request $request)
    {
        $this->authorize('viewAdminDashboard', User::class);
        
        $recherche = $request->input('q');
        
        if (empty($recherche)) {
            return response()->json(['message' => 'Veuillez entrer un terme de recherche'], 400);
        }
        
        $resultats = [
            'etudiants' => $this->rechercherEtudiants($recherche),
            'professeurs' => $this->rechercherProfesseurs($recherche),
            'matieres' => $this->rechercherMatieres($recherche),
            'paiements' => $this->rechercherPaiements($recherche),
            'absences' => $this->rechercherAbsences($recherche),
        ];
        
        return response()->json($resultats);
    }
    
    /**
     * Recherche d'étudiants
     */
    private function rechercherEtudiants(string $recherche)
    {
        return Etudiant::where('Nom', 'like', "%{$recherche}%")
            ->orWhere('Prenom', 'like', "%{$recherche}%")
            ->orWhere('Email', 'like', "%{$recherche}%")
            ->orWhere('Telephone', 'like', "%{$recherche}%")
            ->select('id', 'Nom', 'Prenom', 'Email', 'Telephone')
            ->limit(10)
            ->get()
            ->map(function($etudiant) {
                return [
                    'id' => $etudiant->id,
                    'nom_complet' => $etudiant->Nom . ' ' . $etudiant->Prenom,
                    'email' => $etudiant->Email,
                    'telephone' => $etudiant->Telephone,
                    'lien' => route('etudiants.show', $etudiant),
                ];
            });
    }
    
    /**
     * Recherche de professeurs
     */
    private function rechercherProfesseurs(string $recherche)
    {
        return Professeur::where('Nom', 'like', "%{$recherche}%")
            ->orWhere('Prenom', 'like', "%{$recherche}%")
            ->orWhere('Email', 'like', "%{$recherche}%")
            ->select('id', 'Nom', 'Prenom', 'Email', 'Telephone')
            ->limit(10)
            ->get()
            ->map(function($professeur) {
                return [
                    'id' => $professeur->id,
                    'nom_complet' => $professeur->Nom . ' ' . $professeur->Prenom,
                    'email' => $professeur->Email,
                    'telephone' => $professeur->Telephone,
                    'lien' => route('professeurs.show', $professeur),
                ];
            });
    }
    
    /**
     * Recherche de matières
     */
    private function rechercherMatieres(string $recherche)
    {
        return Matiere::where('Libelle', 'like', "%{$recherche}%")
            ->select('id', 'Libelle')
            ->limit(10)
            ->get()
            ->map(function($matiere) {
                return [
                    'id' => $matiere->id,
                    'libelle' => $matiere->Libelle,
                    'lien' => route('matieres.show', $matiere),
                ];
            });
    }
    
    /**
     * Recherche de paiements
     */
    private function rechercherPaiements(string $recherche)
    {
        return Paiment::with(['etudiant:id,Nom,Prenom'])
            ->where('id', 'like', "%{$recherche}%")
            ->orWhere('Montant', 'like', "%{$recherche}%")
            ->orWhere('SommeApaye', 'like', "%{$recherche}%")
            ->orWhereHas('etudiant', function($query) use ($recherche) {
                $query->where('Nom', 'like', "%{$recherche}%")
                    ->orWhere('Prenom', 'like', "%{$recherche}%");
            })
            ->select('id', 'IdEtu', 'Montant', 'SommeApaye', 'Date_Paiment')
            ->limit(10)
            ->get()
            ->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'etudiant' => $paiement->etudiant ? $paiement->etudiant->Nom . ' ' . $paiement->etudiant->Prenom : 'Inconnu',
                    'montant' => number_format($paiement->Montant, 2, ',', ' '),
                    'somme_payee' => number_format($paiement->SommeApaye, 2, ',', ' '),
                    'date_paiement' => $paiement->Date_Paiment->format('d/m/Y'),
                    'lien' => route('paiements.show', $paiement),
                ];
            });
    }
    
    /**
     * Recherche d'absences
     */
    private function rechercherAbsences(string $recherche)
    {
        return Absence::with(['etudiant:id,Nom,Prenom', 'seance.matiere:id,Libelle'])
            ->where('justificatif', 'like', "%{$recherche}%")
            ->orWhere('commentaire', 'like', "%{$recherche}%")
            ->orWhereHas('etudiant', function($query) use ($recherche) {
                $query->where('Nom', 'like', "%{$recherche}%")
                    ->orWhere('Prenom', 'like', "%{$recherche}%");
            })
            ->orWhereHas('seance.matiere', function($query) use ($recherche) {
                $query->where('Libelle', 'like', "%{$recherche}%");
            })
            ->select('id', 'IdEtu', 'IdSeance', 'date_absence', 'justificatif', 'commentaire')
            ->limit(10)
            ->get()
            ->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'etudiant' => $absence->etudiant ? $absence->etudiant->Nom . ' ' . $absence->etudiant->Prenom : 'Inconnu',
                    'matiere' => $absence->seance && $absence->seance->matiere ? $absence->seance->matiere->Libelle : 'Inconnue',
                    'date_absence' => $absence->date_absence->format('d/m/Y H:i'),
                    'est_justifiee' => !is_null($absence->justificatif),
                    'lien' => route('absences.show', $absence),
                ];
            });
    }
}
