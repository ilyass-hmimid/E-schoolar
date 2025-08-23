<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
            ]);
        }

        // Si aucun rôle spécifique n'est requis, autoriser l'accès
        if (empty($roles)) {
            return $next($request);
        }

        // Récupérer le rôle de l'utilisateur sous forme de chaîne
        $userRole = $user->role;
        
        // Si le rôle est un objet (enum), on récupère sa valeur
        if (is_object($userRole) && method_exists($userRole, 'value')) {
            $userRole = $userRole->value;
        }
        
        // Convertir le rôle en minuscules pour la comparaison si c'est une chaîne
        $userRole = is_string($userRole) ? strtolower($userRole) : $userRole;
        
        // Vérifier si l'utilisateur a l'un des rôles requis
        foreach ($roles as $role) {
            $role = is_string($role) ? strtolower($role) : $role;
            
            // Si le rôle correspond ou si c'est un admin (accès complet)
            if ($userRole === $role || $userRole === 'admin' || $userRole === 1) {
                return $next($request);
            }
            
            // Gestion des alias de rôles
            $roleAliases = [
                'admin' => [1, 'admin', 'administrateur'],
                'professeur' => [2, 'professeur', 'teacher', 'prof'],
                'assistant' => [3, 'assistant', 'assist'],
                'eleve' => [4, 'eleve', 'etudiant', 'student'],
                'parent' => [5, 'parent', 'tuteur']
            ];
            
            // Vérifier les alias de rôles
            foreach ($roleAliases as $mainRole => $aliases) {
                if (in_array($role, $aliases) && in_array($userRole, $aliases)) {
                    return $next($request);
                }
            }
        }

        // Accès refusé
        abort(403, 'Accès non autorisé pour votre rôle.');
    }
}
