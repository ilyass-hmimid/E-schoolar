<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    /**
     * Send a new email verification notification.
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur a déjà vérifié son email
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Vérifier si l'utilisateur est autorisé à envoyer une notification de vérification
        $this->authorize('update', $user);

        // Envoyer la notification de vérification
        $user->sendEmailVerificationNotification();

        return back()->with('status', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
    }
}
