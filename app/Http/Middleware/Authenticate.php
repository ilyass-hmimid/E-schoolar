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
        // Si la requête attend une réponse JSON, retourner null
        if ($request->expectsJson()) {
            return null;
        }

        // Si l'utilisateur essaie d'accéder à une route d'administration
        if ($request->is('admin*') || $request->is('admin/*')) {
            return route('login', ['redirect' => $request->url()]);
        }

        // Pour toutes les autres routes non authentifiées, rediriger vers la page de connexion
        return route('login');
    }
}
