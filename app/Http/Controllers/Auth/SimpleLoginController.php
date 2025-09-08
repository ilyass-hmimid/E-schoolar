<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SimpleLoginController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion
     */
    public function login(Request $request)
    {
        if (auth()->check()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Vérifier si l'utilisateur est un administrateur avant de régénérer la session
            if (!auth()->user()->is_admin) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Accès non autorisé. Seul un administrateur peut se connecter ici.',
                ]);
            }
            
            // Régénérer la session
            $request->session()->regenerate();
            
            // Récupérer l'URL de redirection prévue ou utiliser le tableau de bord par défaut
            $intended = $request->session()->pull('url.intended', route('admin.dashboard'));
            
            // Rediriger vers l'URL prévue
            return redirect()->intended($intended);
        }

        throw ValidationException::withMessages([
            'email' => 'Email ou mot de passe incorrect.',
        ]);
    }

    /**
     * Déconnecte l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
