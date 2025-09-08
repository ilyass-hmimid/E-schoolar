<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleWelcomePage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur est connecté et actif, le laisser accéder à la page demandée
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->is_active) {
                // Si l'utilisateur n'est pas actif, le déconnecter
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Votre compte a été désactivé.');
            }
            // Si l'utilisateur est actif, continuer vers la page demandée
        }

        return $next($request);
    }
}
