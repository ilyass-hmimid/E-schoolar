<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    /**
     * Display the email verification prompt.
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function __invoke(Request $request): RedirectResponse|View
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

        // Vérifier si l'utilisateur est autorisé à accéder à cette page
        $this->authorize('view', $user);

        return view('auth.verify-email');
    }
}
