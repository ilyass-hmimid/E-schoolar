<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        // Si l'utilisateur est déjà connecté et actif, le rediriger vers son dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->is_active) {
                return $this->redirectBasedOnRole($user);
            } else {
                // Si l'utilisateur n'est pas actif, le déconnecter
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
            }
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Traiter la tentative de connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est actif
            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
                ]);
            }

            $request->session()->regenerate();

            // Rediriger selon le rôle
            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Rediriger l'utilisateur selon son rôle
     */
    private function redirectBasedOnRole(User $user)
    {
        // Récupérer la valeur du rôle de manière sécurisée
        $roleValue = null;
        if (is_object($user->role) && isset($user->role->value)) {
            $roleValue = $user->role->value;
        } elseif (is_numeric($user->role)) {
            $roleValue = $user->role;
        } else {
            $roleValue = $user->role; // Si c'est déjà une chaîne
        }
        
        // Convertir en minuscules pour la correspondance
        $role = is_string($roleValue) ? strtolower($roleValue) : $roleValue;
        
        return match($role) {
            RoleType::ADMIN->value, 'admin' => redirect()->route('admin.dashboard'),
            RoleType::PROFESSEUR->value, 'professeur' => redirect()->route('professeur.dashboard'),
            RoleType::ASSISTANT->value, 'assistant' => redirect()->route('assistant.dashboard'),
            RoleType::ELEVE->value, 'eleve' => redirect()->route('eleve.dashboard'),
            RoleType::PARENT->value, 'parent' => redirect()->route('parent.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe
     */
    public function showForgotPasswordForm()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Envoyer le lien de réinitialisation
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Ici vous pouvez implémenter l'envoi d'email de réinitialisation
        // Pour l'instant, on simule un succès
        return back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
    }

    /**
     * Afficher le formulaire de réinitialisation
     */
    public function showResetForm(Request $request, $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Ici vous pouvez implémenter la logique de réinitialisation
        // Pour l'instant, on simule un succès
        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
    }
}
