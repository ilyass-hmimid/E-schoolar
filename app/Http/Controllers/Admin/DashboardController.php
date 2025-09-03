<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Professeur;
use App\Models\Paiement;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord administrateur
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Optimiser les requêtes en utilisant des requêtes plus efficaces
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        
        // Récupérer toutes les statistiques en une seule requête
        $stats = [
            'eleves_count' => Eleve::count(),
            'eleves_nouveaux' => Eleve::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
            'professeurs_count' => Professeur::count(),
            'paiements_mois' => Paiement::whereBetween('date_paiement', [$currentMonthStart, $currentMonthEnd])
                ->sum('montant'),
            'paiements_mois_dernier' => Paiement::whereBetween('date_paiement', 
                [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->sum('montant'),
            'taux_presence' => $this->calculerTauxPresence(),
            'moyenne_notes' => $this->calculerMoyenneNotes(),
        ];

        // Calculer la variation des paiements par rapport au mois dernier
        $stats['variation_paiements'] = $stats['paiements_mois_dernier'] > 0 
            ? (($stats['paiements_mois'] - $stats['paiements_mois_dernier']) / $stats['paiements_mois_dernier']) * 100 
            : 100;

        // Paiements mensuels pour le graphique (6 derniers mois)
        $paiementsMensuels = Paiement::select(
                DB::raw('DATE_FORMAT(date_paiement, "%Y-%m") as mois'),
                DB::raw('COUNT(DISTINCT eleve_id) as nombre_eleves'),
                DB::raw('SUM(montant) as total')
            )
            ->where('date_paiement', '>=', $sixMonthsAgo)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->mois);
                $item->mois = $date->isoFormat('MMM YYYY');
                return $item;
            });

        // Statistiques des présences des 30 derniers jours
        $presences30Jours = Absence::select(
                DB::raw('DATE(date_absence) as date'),
                DB::raw('COUNT(CASE WHEN justifiee = 0 THEN 1 END) as absences_non_justifiees'),
                DB::raw('COUNT(CASE WHEN justifiee = 1 THEN 1 END) as absences_justifiees'),
                DB::raw('COUNT(*) as total_absences')
            )
            ->where('date_absence', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Dernières absences (10 dernières)
        $recentAbsences = Absence::with(['eleve.classe'])
            ->orderBy('date_absence', 'desc')
            ->take(10)
            ->get();

        // Événements à venir (exemple)
        $prochainsEvenements = collect([
            (object)[
                'titre' => 'Réunion des professeurs',
                'date' => now()->addDays(2),
                'description' => 'Réunion mensuelle avec tous les professeurs',
                'icone' => 'users',
                'couleur' => 'blue'
            ],
            (object)[
                'titre' => 'Échéance des paiements',
                'date' => now()->addDays(5),
                'description' => 'Dernier jour pour les paiements du mois',
                'icone' => 'credit-card',
                'couleur' => 'green'
            ],
            (object)[
                'titre' => 'Vacances scolaires',
                'date' => now()->addDays(15),
                'description' => 'Début des vacances de printemps',
                'icone' => 'umbrella-beach',
                'couleur' => 'yellow'
            ]
        ]);

        // Préparer les données pour les graphiques
        $chartData = [
            'labels' => $paiementsMensuels->pluck('mois'),
            'paiements' => $paiementsMensuels->pluck('total'),
            'eleves_payants' => $paiementsMensuels->pluck('nombre_eleves'),
            'dates_presence' => $presences30Jours->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d/m')),
            'absences_justifiees' => $presences30Jours->pluck('absences_justifiees'),
            'absences_non_justifiees' => $presences30Jours->pluck('absences_non_justifiees'),
        ];

        return view('admin.dashboard', compact(
            'stats', 
            'paiementsMensuels', 
            'recentAbsences', 
            'prochainsEvenements',
            'chartData'
        ));
    }

    /**
     * Calculer le taux de présence global
     *
     * @return float
     */
    protected function calculerTauxPresence()
    {
        $totalSeances = Absence::count();
        
        if ($totalSeances === 0) {
            return 100; // Éviter la division par zéro
        }
        
        $absencesNonJustifiees = Absence::where('justifiee', false)->count();
        $tauxPresence = (($totalSeances - $absencesNonJustifiees) / $totalSeances) * 100;
        
        return round($tauxPresence, 1);
    }

    /**
     * Calculer la moyenne des notes des élèves
     *
     * @return float
     */
    protected function calculerMoyenneNotes()
    {
        // Vérifier si la table des notes existe
        if (!\Schema::hasTable('notes')) {
            return 0;
        }
        
        $moyenne = DB::table('notes')
            ->select(DB::raw('AVG(note) as moyenne'))
            ->value('moyenne');
            
        return $moyenne ? round($moyenne, 1) : 0;
    }
}
