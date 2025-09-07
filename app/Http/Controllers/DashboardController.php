<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cours;
use App\Models\Classe;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Affiche le tableau de bord
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }
        
        try {
            // Récupérer l'utilisateur connecté
            $user = auth()->user();
            
            // Vérifier si l'utilisateur est administrateur
            if (!$user->is_admin) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Accès non autorisé. Seul un administrateur peut accéder à cette page.');
            }
            
            // Vérifier si l'utilisateur est actif
            if (!$user->is_active) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
            }
            
            // Récupérer les statistiques pour l'administrateur
            $stats = [
                'total_etudiants' => User::where('role', 'eleve')->count(),
                'total_professeurs' => User::where('role', 'professeur')->count(),
                'total_cours' => class_exists(Cours::class) ? Cours::count() : 0,
                'total_classes' => class_exists(Classe::class) ? Classe::count() : 0,
            ];
            
            // Derniers événements pour l'administrateur
            $evenements = [
                [
                    'titre' => 'Rentrée scolaire',
                    'date' => now()->format('d/m/Y'),
                    'description' => 'Début de la nouvelle année scolaire'
                ],
                // Ajoutez d'autres événements ici
            ];

            return view('dashboard', [
                'user' => $user,
                'stats' => $stats,
                'evenements' => $evenements
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, rediriger vers la page d'accueil avec un message d'erreur
            return redirect()->route('home')->with('error', 'Une erreur est survenue lors du chargement du tableau de bord.');
        }
    }
}