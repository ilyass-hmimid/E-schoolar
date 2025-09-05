<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'active']);
    }

    /**
     * Afficher la page d'accueil ou rediriger vers le tableau de bord approprié.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();
        
        // Si l'utilisateur est authentifié et actif, le rediriger vers son tableau de bord
        if ($user && $user->is_active) {
            if ($user->role === \App\Enums\RoleType::ADMIN) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === \App\Enums\RoleType::PROFESSEUR) {
                return redirect()->route('professeur.dashboard');
            } elseif ($user->role === \App\Enums\RoleType::ASSISTANT) {
                return redirect()->route('assistant.dashboard');
            } elseif ($user->role === \App\Enums\RoleType::ELEVE) {
                return redirect()->route('eleve.dashboard');
            }
            
            // Si l'utilisateur n'a pas de rôle connu, afficher la vue d'accueil
            return view('welcome');
        }
        
        // Si l'utilisateur n'est pas actif, le déconnecter et rediriger vers la page de connexion
        if ($user) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte est désactivé. Veuillez contacter l\'administrateur.');
        }
        
        // Par défaut, rediriger vers la page de connexion
        return redirect()->route('login');
    }
} 
