<?php

namespace App\Http\Middleware;

use App\Enums\RoleType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();
        
        // L'administrateur a tous les droits
        if ($user->role === RoleType::ADMIN->value) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a la permission requise
        $role = RoleType::from($user->role);
        
        if (!$role->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Accès non autorisé. Permissions insuffisantes.'
                ], 403);
            }
            
            return redirect(getDashboardUrl())
                ->with('error', 'Vous n\'avez pas la permission d\'accéder à cette page.');
        }

        return $next($request);
    }
}
