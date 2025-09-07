<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si l'utilisateur est déjà authentifié et accède à la page de connexion,
                // le rediriger vers le tableau de bord
                if ($request->is('login') || $request->is('auth/login')) {
                    return redirect()->route('admin.dashboard');
                }
                
                // Pour toutes les autres requêtes, continuer normalement
                return $next($request);
            }
        }

        // Si l'utilisateur n'est pas authentifié et essaie d'accéder à une zone protégée
        // le rediriger vers la page de connexion
        if ($request->is('admin/*') || $request->is('admin')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
