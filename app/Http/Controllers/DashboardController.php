<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Cours;
use App\Models\Classe;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\Professeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Calcule l'évolution d'une métrique par rapport au mois précédent
     */
    private function calculateEvolution($metric) {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        
        $currentCount = 0;
        $previousCount = 0;
        
        switch ($metric) {
            case 'eleves':
                $currentCount = Eleve::where('created_at', '<=', $currentMonth->copy()->endOfMonth())->count();
                $previousCount = Eleve::where('created_at', '<=', $previousMonth->copy()->endOfMonth())
                    ->where('created_at', '>=', $previousMonth->startOfMonth())
                    ->count();
                break;
            // Ajouter d'autres cas pour d'autres métriques si nécessaire
        }
        
        if ($previousCount === 0) {
            return 0;
        }
        
        return round((($currentCount - $previousCount) / $previousCount) * 100, 1);
    }

    /**
     * Affiche le tableau de bord
     */
    public function index(Request $request)
    {
        try {
            // Récupérer les statistiques de base
            $stats = [
                'total_eleves' => Eleve::count(),
                'total_professeurs' => Professeur::count(),
                'total_classes' => Classe::count(),
                'total_cours' => Cours::count(),
                'taux_absences' => 0,
                'total_paiements_mois' => 0,
                'evolution_eleves' => $this->calculateEvolution('eleves')
            ];
            
            // Récupérer les données pour les graphiques
            $absencesChartData = $this->getAbsencesChartData();
            $paiementsChartData = $this->getPaiementsChartData();
            
            // Récupérer les dernières absences et paiements
            $recentAbsences = Absence::with(['eleve', 'matiere'])
                ->with(['eleve' => function($query) {
                    $query->select('id', 'name', 'prenom');
                }])
                ->latest('date_absence')
                ->take(5)
                ->get();
                
            $recentPaiements = Paiement::with(['eleve'])
                ->where('statut', 'paye')
                ->latest('date_paiement')
                ->take(5)
                ->get();
            
            // Calculer les statistiques de paiement
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $previousMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
            $previousYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;
            
            $paiementsMois = Paiement::where('statut', 'paye')
                ->whereMonth('date_paiement', $currentMonth)
                ->whereYear('date_paiement', $currentYear)
                ->sum('montant');
                
            $paiementsMoisPrecedent = Paiement::where('statut', 'paye')
                ->whereMonth('date_paiement', $previousMonth)
                ->whereYear('date_paiement', $previousYear)
                ->sum('montant');
                
            $evolution = $paiementsMoisPrecedent > 0 
                ? round((($paiementsMois - $paiementsMoisPrecedent) / $paiementsMoisPrecedent) * 100, 1)
                : ($paiementsMois > 0 ? 100 : 0);
                
            $paiementsStats = [
                'total_mois' => $paiementsMois,
                'total_annee' => Paiement::where('statut', 'paye')
                    ->whereYear('date_paiement', $currentYear)
                    ->sum('montant'),
                'impayes' => User::where('role', 'eleve')->count() - 
                    Paiement::where('statut', 'paye')
                        ->whereMonth('date_paiement', $currentMonth)
                        ->whereYear('date_paiement', $currentYear)
                        ->distinct('eleve_id')
                        ->count('eleve_id'),
                'evolution' => $evolution
            ];
            
            // Calculer les statistiques d'absences
            $absencesMois = Absence::whereMonth('date_absence', $currentMonth)
                ->whereYear('date_absence', $currentYear)
                ->count();
                
            $absencesMoisPrecedent = Absence::whereMonth('date_absence', $previousMonth)
                ->whereYear('date_absence', $previousYear)
                ->count();
                
            $evolution = $absencesMoisPrecedent > 0 
                ? round((($absencesMois - $absencesMoisPrecedent) / $absencesMoisPrecedent) * 100, 1)
                : ($absencesMois > 0 ? 100 : 0);
                
            $absencesStats = [
                'total_mois' => $absencesMois,
                'non_justifiees' => Absence::where('statut', '!=', 'justifiee')
                    ->whereMonth('date_absence', $currentMonth)
                    ->whereYear('date_absence', $currentYear)
                    ->count(),
                'taux_justification' => $absencesMois > 0 ? 
                    round((Absence::where('statut', 'justifiee')
                        ->whereMonth('date_absence', $currentMonth)
                        ->whereYear('date_absence', $currentYear)
                        ->count() / $absencesMois) * 100) : 0,
                'evolution' => $evolution
            ];
            
            // Préparer les données pour les graphiques
            $absencesData = [];
            $paiementsData = [];
            $currentYear = now()->year;
            
            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::createFromDate($currentYear, $i, 1)->monthName;
                
                $absencesData[] = [
                    'month' => $monthName,
                    'count' => $absencesChartData[$i - 1] ?? 0
                ];
                
                $paiementsData[] = [
                    'month' => $monthName,
                    'amount' => $paiementsChartData[$i - 1] ?? 0
                ];
            }
            
            // Afficher la vue du tableau de bord avec les données
            return view('admin.dashboard', [
                'user' => auth()->user(),
                'stats' => (object)$stats,
                'paiementsStats' => (object)$paiementsStats,
                'absencesStats' => (object)$absencesStats,
                'recentAbsences' => $recentAbsences,
                'recentPaiements' => $recentPaiements,
                'absencesData' => $absencesData,
                'paiementsData' => $paiementsData,
                'currentYear' => $currentYear,
            ]);
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un message d'erreur détaillé
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du chargement du tableau de bord',
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    /**
     * Récupère les données pour le graphique des absences
     */
    private function getAbsencesChartData()
    {
        $data = [];
        $currentYear = now()->year;
        
        for ($i = 1; $i <= 12; $i++) {
            $data[] = Absence::whereMonth('date_absence', $i)
                ->whereYear('date_absence', $currentYear)
                ->count();
        }
        
        return $data;
    }
    
    /**
     * Récupère les données pour le graphique des paiements
     */
    private function getPaiementsChartData()
    {
        $data = [];
        $currentYear = now()->year;
        
        for ($i = 1; $i <= 12; $i++) {
            $data[] = Paiement::where('statut', 'paye')
                ->whereMonth('date_paiement', $i)
                ->whereYear('date_paiement', $currentYear)
                ->sum('montant');
        }
        
        return $data;
    }
    
    /**
     * Récupère les données pour le graphique des inscriptions
     */
    private function getInscriptionsChartData()
    {
        $data = [];
        $currentYear = now()->year;
        
        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => Carbon::createFromDate($currentYear, $i, 1)->monthName,
                'count' => User::where('role', 'eleve')
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', $currentYear)
                    ->count()
            ];
        }
        
        return $data;
    }
}