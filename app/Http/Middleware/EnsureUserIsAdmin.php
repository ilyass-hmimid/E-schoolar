<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Si aucun utilisateur n'est connecté, rediriger vers la page de connexion
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }
        
        // Vérifier si le compte est actif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
        }
        
        // Vérifier si l'utilisateur est administrateur
        if (!$user->is_admin) {
            // Si l'utilisateur n'est pas admin, le déconnecter et rediriger
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Accès non autorisé. Seul un administrateur peut accéder à cette page.');
        }
        
        // Si tout est bon, continuer vers la requête suivante
        return $next($request);
    }
}
