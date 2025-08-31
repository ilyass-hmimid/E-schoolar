<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // If user is admin, allow access
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Get user role and normalize it
        $userRole = $this->normalizeRole($user->role);
        $requiredRoles = array_map([$this, 'normalizeRole'], explode('|', $role));
        
        // Check if user has any of the required roles
        if (in_array($userRole, $requiredRoles, true)) {
            return $next($request);
        }
        
        // Log unauthorized access attempt
        Log::warning('Tentative d\'accès non autorisée', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'required_roles' => $requiredRoles,
            'route' => $request->route()?->getName(),
            'ip' => $request->ip()
        ]);

        return response()->json(['message' => 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires.'], 403);
    }
    
    /**
     * Normalize role to string representation
     */
    private function normalizeRole($role): string
    {
        if ($role instanceof \BackedEnum) {
            return strtolower($role->value);
        }
        
        if (is_object($role) && method_exists($role, 'value')) {
            $role = $role->value;
        }
        
        return is_string($role) ? strtolower(trim($role)) : '';
    }
}
