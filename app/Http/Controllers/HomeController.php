<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Si l'utilisateur est déjà authentifié, le rediriger vers le tableau de bord approprié
        if (Auth::check()) {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est administrateur
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            
            // Pour les autres rôles, rediriger vers la page d'accueil par défaut
            return view('home');
        }
        
        // Si l'utilisateur n'est pas connecté, afficher la page d'accueil publique
        return view('home');
    }
}
