<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
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
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Vérifier que l'utilisateur est actif
                if ($user && $user->is_active) {
                    // Si l'utilisateur est connecté et actif, le rediriger vers son dashboard
                    // Mais seulement si il essaie d'accéder aux pages d'authentification
                    if ($request->is('login') || $request->is('register') || $request->is('forgot-password') || $request->is('reset-password*')) {
                        return redirect(getDashboardUrl());
                    }
                } else {
                    // Si l'utilisateur n'est pas actif, le déconnecter
                    Auth::guard($guard)->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }
        }

        return $next($request);
    }
}
