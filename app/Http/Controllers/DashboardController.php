<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cours;
use App\Models\Classe;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\Professeur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Appliquer les middlewares auth et admin à toutes les méthodes
        $this->middleware('auth');
        $this->middleware('admin');
        
        // Désactiver le middleware pour les méthodes spécifiques si nécessaire
        // $this->middleware('admin')->except(['method1', 'method2']);
    }

    /**
     * Affiche le tableau de bord
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Vérification redondante (déjà fait par le middleware, mais c'est une sécurité supplémentaire)
            if (!auth()->check() || !auth()->user()->is_admin) {
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté en tant qu\'administrateur pour accéder à cette page.');
            }

            // Récupérer l'utilisateur connecté
            $user = auth()->user();
            
            // Initialiser les statistiques avec des valeurs par défaut sécurisées
            $stats = [
                'total_eleves' => 0,
                'total_professeurs' => 0,
                'total_classes' => 0,
                'total_cours' => 0,
                'taux_absences' => 0
            ];

            // Initialiser les statistiques de paiement
            $paiementsStats = [
                'total_mois' => 0,
                'total_annee' => 0,
                'impayes' => 0,
            ];

            // Initialiser les statistiques d'absences
            $absencesStats = [
                'total_mois' => 0,
                'non_justifiees' => 0,
            ];

            // Initialiser les tableaux vides pour éviter les erreurs
            $recentAbsences = collect([]); // Utilisation d'une collection vide
            $recentPaiements = collect([]); // Utilisation d'une collection vide
            $absencesChartData = array_fill(0, 12, 0);

            // Si vous souhaitez charger des données réelles, décommentez et adaptez ce code :
            /*
            try {
                $recentAbsences = \App\Models\Absence::with(['eleve', 'seance'])
                    ->latest('date')
                    ->take(5)
                    ->get();

                $recentPaiements = \App\Models\Paiement::with(['eleve'])
                    ->latest('date_paiement')
                    ->take(5)
                    ->get();

                // Mise à jour des statistiques avec des données réelles
                $stats['total_eleves'] = \App\Models\Eleve::count();
                $stats['total_professeurs'] = \App\Models\Professeur::count();
                $stats['total_classes'] = \App\Models\Classe::count();
                $stats['total_cours'] = \App\Models\Cours::count();
                
                // Calculer le taux d'absences (exemple)
                $totalEleves = $stats['total_eleves'] > 0 ? $stats['total_eleves'] : 1;
                $totalAbsences = \App\Models\Absence::count();
                $stats['taux_absences'] = ($totalAbsences / ($totalEleves * 30)) * 100; // Exemple de calcul

            } catch (\Exception $e) {
                // En cas d'erreur, on garde les valeurs par défaut
                \Log::error('Erreur lors du chargement des données du tableau de bord: ' . $e->getMessage());
            }
            */

            // Charger la vue du tableau de bord avec les données
            return view('admin.dashboard', [
                'user' => $user,
                'stats' => (object)$stats, // Conversion en objet pour éviter les erreurs dans la vue
                'paiementsStats' => (object)$paiementsStats,
                'absencesStats' => (object)$absencesStats,
                'recentAbsences' => $recentAbsences,
                'recentPaiements' => $recentPaiements,
                'absencesChartData' => $absencesChartData,
                'currentYear' => now()->year,
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, afficher l'erreur directement pour le débogage
            return response()->view('errors.minimal', [
                'title' => 'Erreur',
                'code' => 500,
                'message' => 'Erreur lors du chargement du tableau de bord: ' . $e->getMessage(),
            ], 500);
        }
    }
}