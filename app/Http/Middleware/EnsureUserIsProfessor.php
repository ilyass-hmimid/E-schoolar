<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsProfessor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated and is a professor
        if (!$user || !$user->hasRole('professor')) {
            // Log unauthorized access attempt
            if ($user) {
                activity()
                    ->causedBy($user)
                    ->withProperties([
                        'ip' => $request->ip(),
                        'url' => $request->fullUrl(),
                    ])
                    ->log('Unauthorized access attempt to professor area');
            }
            
            // Redirect to appropriate dashboard or home
            if ($user) {
                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'Accès réservé aux professeurs.');
                } elseif ($user->hasRole('student')) {
                    return redirect()->route('student.dashboard')
                        ->with('error', 'Accès réservé aux professeurs.');
                } elseif ($user->hasRole('parent')) {
                    return redirect()->route('parent.dashboard')
                        ->with('error', 'Accès réservé aux professeurs.');
                }
            }
            
            // If not authenticated or no specific role, redirect to login
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter en tant que professeur pour accéder à cette page.');
        }
        
        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
        }
        
        return $next($request);
    }
}
