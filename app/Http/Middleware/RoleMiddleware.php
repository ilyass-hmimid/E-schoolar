<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role = 'admin')
    {
        $user = $request->user();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur a le rôle requis
        if ($user->role === $role) {
            return $next($request);
        }

        // Si l'utilisateur est déjà sur la page d'accueil, éviter la boucle
        if ($request->is('admin/dashboard') || $request->is('admin')) {
            return $next($request);
        }

        // Journaliser la tentative d'accès non autorisée
        Log::warning('Accès refusé - Rôle insuffisant', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_role' => $role,
            'route' => $request->route()?->getName(),
            'ip' => $request->ip()
        ]);

        // Rediriger vers le tableau de bord avec un message d'erreur
        return redirect()->route('admin.dashboard')
            ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
    }

    /**
     * Gérer une réponse non autorisée
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthorized(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        
        // Éviter les redirections en boucle
        if ($request->is('login') || $request->is('admin/login')) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        
        return redirect()->route('login')->with('error', 'Accès non autorisé.');
    }
}
