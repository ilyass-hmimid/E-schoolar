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

        // Vérifier si l'utilisateur a un des rôles requis via Spatie Permission
        foreach ($roles as $role) {
            // Vérifier si le rôle est un nom de rôle Spatie
            if ($user->hasRole($role)) {
                return $next($request);
            }
            
            // Vérifier les alias de rôles
            $roleAliases = $this->getRoleAliases($role);
            foreach ($roleAliases as $alias) {
                if ($user->hasRole($alias)) {
                    return $next($request);
                }
            }
        }
        
        // Si on arrive ici, l'utilisateur n'a aucun des rôles requis
        abort(403, 'Accès non autorisé pour votre rôle.');
    }
    
    /**
     * Récupère les alias pour un rôle donné
     */
    private function getRoleAliases($role)
    {
        // Convertir en chaîne et en minuscules pour la comparaison
        $role = strtolower((string)$role);
        
        // Définition des alias de rôles
        $aliases = [
            // Admin
            '1' => ['admin', 'administrateur', 'administrator'],
            'admin' => ['admin', 'administrateur', 'administrator'],
            'administrateur' => ['admin', 'administrateur', 'administrator'],
            
            // Professeur
            '2' => ['professeur', 'prof', 'teacher'],
            'professeur' => ['professeur', 'prof', 'teacher'],
            'prof' => ['professeur', 'prof', 'teacher'],
            
            // Assistant
            '3' => ['assistant', 'assist', 'aide'],
            'assistant' => ['assistant', 'assist', 'aide'],
            
            // Élève
            '4' => ['eleve', 'etudiant', 'student'],
            'eleve' => ['eleve', 'etudiant', 'student'],
            'etudiant' => ['eleve', 'etudiant', 'student'],
        ];
        
        return $aliases[$role] ?? [$role];
    }
}
