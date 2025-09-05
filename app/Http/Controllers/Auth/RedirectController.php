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
        
        // Journalisation des informations de débogage
        \Log::info('Redirection après connexion', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'intended_url' => session('url.intended')
        ]);
        
        // Vérifier s'il y a une URL d'origine (intended URL)
        if (session()->has('url.intended')) {
            return redirect()->intended();
        }
        
        // Redirection basée sur le rôle
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('professeur')) {
            return redirect()->route('professeur.dashboard');
        } elseif ($user->hasRole('eleve')) {
            return redirect()->route('eleve.dashboard');
        } elseif ($user->hasRole('assistant')) {
            return redirect()->route('assistant.dashboard');
        }
        
        // Redirection par défaut si aucun rôle ne correspond
        return redirect()->route('home');
    }
}
