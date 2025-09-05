<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Check if user is active
                if (!$user->is_active) {
                    Auth::guard($guard)->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')->withErrors([
                        'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
                    ]);
                }
                
                return $this->redirectToRoleBasedDashboard($user);
            }
        }

        return $next($request);
    }
    
    /**
     * Redirect user to their role-based dashboard
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToRoleBasedDashboard($user)
    {
        return redirect(RouteServiceProvider::getHomeForUser($user));
    }
}
