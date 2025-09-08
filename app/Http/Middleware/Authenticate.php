<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Si la requête attend une réponse JSON, retourner une réponse 401
        if ($request->expectsJson()) {
            return null;
        }

        // Stocker l'URL actuelle pour redirection après connexion
        if (!$request->is('login') && !$request->is('logout') && !$request->is('admin*')) {
            // Utiliser put() au lieu de session() pour s'assurer que la session est sauvegardée
            $request->session()->put('url.intended', $request->fullUrl());
        }

        // Ajouter un message flash pour informer l'utilisateur
        session()->flash('status', 'Veuillez vous connecter pour accéder à cette page.');

        // Rediriger vers la page de connexion
        return route('login');
    }
}
