<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    /**
     * Redirige l'utilisateur vers le tableau de bord approprié en fonction de son rôle
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Redirige l'utilisateur vers le tableau de bord approprié en fonction de son rôle
     * en respectant l'URL d'origine si elle existe (intended URL)
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToDashboard()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte est désactivé. Veuillez contacter l\'administrateur.');
        }
        
        // Vérifier s'il y a une URL d'origine (intended URL)
        if (session()->has('url.intended')) {
            return redirect()->intended();
        }
        
        // Rediriger en fonction du rôle
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->hasRole('professeur')) {
            return redirect()->intended(route('professeur.dashboard'));
        } elseif ($user->hasRole('assistant')) {
            return redirect()->intended(route('assistant.dashboard'));
        } elseif ($user->hasRole('eleve')) {
            return redirect()->intended(route('eleve.dashboard'));
        }
        
        // Redirection par défaut vers le tableau de bord général
        return redirect()->intended(route('dashboard'));
    }
}
