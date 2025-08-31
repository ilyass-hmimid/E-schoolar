<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur a déjà vérifié son email
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        // Vérifier si l'utilisateur est autorisé à vérifier son email
        $this->authorize('update', $user);

        // Marquer l'email comme vérifié
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            
            // Mettre à jour la date de vérification de l'email
            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        return redirect()->intended(RouteServiceProvider::HOME.'?verified=1')
            ->with('status', 'Votre adresse email a été vérifiée avec succès.');
    }
}
