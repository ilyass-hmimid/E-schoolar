<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $this->authorize('login', [User::class]);
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->authorize('login', [User::class]);
        
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $this->authorize('viewDashboard', $user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user) {
            $this->authorize('logout', $user);
            
            Auth::guard('web')->logout();
            $request->session()->invalidate();

            // Récupérer tous les cookies de la requête
            $cookies = $request->cookies->all();

            // Supprimer tous les cookies un par un
            foreach ($cookies as $name => $value) {
                $request->session()->forget($name);
            }

            $request->session()->regenerateToken();
        }

        return redirect()->route('login')->with('status', 'Vous avez été déconnecté avec succès.');
    }

}
