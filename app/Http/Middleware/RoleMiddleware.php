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
     * @param  string|array  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        $user = $request->user();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->unauthorized($request);
        }

        // Normaliser les rôles (s'assurer que c'est un tableau plat)
        $roles = is_array($roles) ? array_map('strtolower', $roles) : [strtolower($roles)];
        $roles = array_map('trim', $roles);
        $roles = array_unique($roles);

        // Si l'utilisateur a le rôle admin, on lui donne accès
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        $userRoles = $user->getRoleNames()->map(fn($role) => strtolower(trim($role)));
        
        foreach ($roles as $requiredRole) {
            if ($userRoles->contains($requiredRole)) {
                return $next($request);
            }
        }

        // Journaliser la tentative d'accès non autorisée
        $this->logUnauthorizedAccess($request, $user, $roles);

        return $this->unauthorized($request);
    }

    /**
     * Journaliser une tentative d'accès non autorisée
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @param  array  $requiredRoles
     * @return void
     */
    private function logUnauthorizedAccess($request, $user, array $requiredRoles): void
    {
        Log::warning('Tentative d\'accès non autorisée', [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_roles' => $user->getRoleNames(),
            'required_roles' => $requiredRoles,
            'route' => $request->route()?->getName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Gérer une réponse non autorisée
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    private function unauthorized($request)
    {
        $message = 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.';
        
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'code' => 403
            ], 403);
        }

        return redirect()
            ->route('home')
            ->with('error', $message);
    }
}
