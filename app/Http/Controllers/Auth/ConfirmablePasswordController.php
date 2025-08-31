<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ConfirmablePasswordController extends Controller
{
    /**
     * The maximum number of attempts to allow.
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * The number of minutes to throttle for.
     *
     * @var int
     */
    protected $decayMinutes = 15;

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->ip()).'|'.$request->user()->id;
    }
    /**
     * Show the confirm password view.
     */
    /**
     * Show the confirm password view.
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function show(): View
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            abort(403, 'Accès non autorisé.');
        }

        // Vérifier si l'utilisateur est autorisé à confirmer son mot de passe
        $this->authorize('update', $user);

        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    /**
     * Confirm the user's password.
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur est autorisé à confirmer son mot de passe
        $this->authorize('update', $user);

        // Valider le mot de passe
        if (!Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $request->password,
        ])) {
            // Enregistrer la tentative échouée
            RateLimiter::hit($this->throttleKey($request));
            
            throw ValidationException::withMessages([
                'password' => __('Le mot de passe fourni est incorrect.'),
            ]);
        }

        // Réinitialiser le compteur de tentatives
        RateLimiter::clear($this->throttleKey($request));
        
        // Enregistrer la confirmation du mot de passe dans la session
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(RouteServiceProvider::HOME)
            ->with('status', 'Mot de passe confirmé avec succès.');
    }
}
