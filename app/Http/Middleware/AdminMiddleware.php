<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!Auth::check()) {
            // Si c'est une requête API, retourner une erreur 401
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur est un administrateur
        if (!$user->is_admin) {
            // Si c'est une requête API, retourner une erreur 403
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès non autorisé. Seul un administrateur peut accéder à cette ressource.'], 403);
            }

            // Déconnexion et redirection vers la page de connexion avec un message d'erreur
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Accès non autorisé. Seul un administrateur peut accéder à cette page.');
        }

        return $next($request);
    }
}
