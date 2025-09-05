<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Paiement;
use App\Models\Absence;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord administrateur
     *
     * @return \Illuminate\View\View
     */
    /**
     * Afficher le tableau de bord administrateur
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = $this->getDashboardData();
        return view('admin.dashboard', $data);
    }
    
    /**
     * Récupère les données du tableau de bord avec mise en cache
     * 
     * @return array
     */
    protected function getDashboardData()
    {
        // Utilisation du cache pour les données du tableau de bord (durée : 5 minutes)
        $cacheKey = 'admin_dashboard_' . auth()->id();
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            // Périodes pour les requêtes
            $currentMonthStart = now()->startOfMonth();
            $currentMonthEnd = now()->endOfMonth();
            $lastMonthStart = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();
            $todayStart = now()->startOfDay();
            $todayEnd = now()->endOfDay();
            
            // Récupérer les statistiques essentielles avec des requêtes optimisées
            $stats = [
                // Statistiques des élèves
                'eleves_count' => Eleve::count(),
                'eleves_nouveaux' => Eleve::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'eleves_trend' => 0, // À calculer plus tard
                
                // Statistiques des absences
                'absences_count' => Absence::whereBetween('date_absence', [$currentMonthStart, $currentMonthEnd])->count(),
                'absences_last_month' => Absence::whereBetween('date_absence', [$lastMonthStart, $lastMonthEnd])->count(),
                'absences_trend' => 0, // À calculer plus tard
                'absences_mois' => Absence::whereBetween('date_absence', [$currentMonthStart, $currentMonthEnd])->count(),
                'nouvelles_absences' => Absence::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                
                // Statistiques des paiements
                'paiements_mois' => Paiement::whereBetween('date_paiement', [$currentMonthStart, $currentMonthEnd])->sum('montant'),
                'paiements_mois_dernier' => Paiement::whereBetween('date_paiement', [$lastMonthStart, $lastMonthEnd])->sum('montant'),
                'paiements_trend' => 0, // À calculer plus tard
                
                // Statistiques des professeurs
                'professeurs_count' => User::role('professeur')->count(),
                'professeurs_nouveaux' => User::role('professeur')
                    ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                    ->count(),
                
                // Statistiques des classes
                'classes_count' => \App\Models\Classe::count(),
                'eleves_par_classe' => \App\Models\Classe::withCount('eleves')
                    ->get()
                    ->avg('eleves_count') ?? 0,
                
                // Statistiques des cours et événements
                'cours_aujourdhui' => \App\Models\Cours::whereDate('date_cours', now()->toDateString())->count(),
                'evenements_aujourdhui' => Event::whereDate('start_time', now()->toDateString())->count(),
                
                // Autres statistiques
                'messages_non_lus' => 0, // À implémenter si nécessaire
                'taches_en_attente' => 0, // À implémenter si nécessaire
                'alertes' => 0, // À implémenter si nécessaire
            ];

            // Calculer les évolutions
            $stats['paiements_trend'] = $stats['paiements_mois_dernier'] > 0 
                ? round((($stats['paiements_mois'] - $stats['paiements_mois_dernier']) / $stats['paiements_mois_dernier']) * 100, 1)
                : ($stats['paiements_mois'] > 0 ? 100 : 0);
                
            $stats['absences_trend'] = $stats['absences_last_month'] > 0
                ? round((($stats['absences_count'] - $stats['absences_last_month']) / $stats['absences_last_month']) * 100, 1)
                : ($stats['absences_count'] > 0 ? 100 : 0);
                
            $stats['eleves_trend'] = 5; // Valeur par défaut, à calculer si nécessaire

            // Données pour le graphique des absences (30 derniers jours)
            $absencesData = Absence::select(
                    DB::raw('DATE(date_absence) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('date_absence', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            // Préparer les données pour le graphique
            $absencesChartData = [
                'categories' => [],
                'data' => []
            ];
            
            // Remplir les 30 derniers jours
            for ($i = 30; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $absencesChartData['categories'][] = now()->subDays($i)->format('d M');
                $absencesChartData['data'][] = $absencesData->get($date, (object)['count' => 0])->count;
            }

            // Dernières absences avec chargement des relations
            $recentAbsences = Absence::with([
                    'eleve' => function($query) {
                        $query->select('id', 'name', 'prenom', 'classe_id', 'photo_url');
                    },
                    'eleve.classe' => function($query) {
                        $query->select('id', 'nom');
                    }
                ])
                ->orderBy('date_absence', 'desc')
                ->take(5)
                ->get();

            // Prochains événements (à venir dans les 7 prochains jours)
            $upcomingEvents = Event::with(['createdBy' => function($query) {
                    $query->select('id', 'name');
                }])
                ->where('start_time', '>=', now())
                ->where('start_time', '<=', now()->addDays(7))
                ->orderBy('start_time')
                ->take(5)
                ->get();

            return [
                'stats' => $stats,
                'recentAbsences' => $recentAbsences,
                'absencesChartData' => $absencesChartData,
                'upcomingEvents' => $upcomingEvents
            ];
        });
    }
}
