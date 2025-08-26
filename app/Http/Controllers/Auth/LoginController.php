<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

            // Appeler la méthode authenticated pour la redirection
            return $this->authenticated($request, $user) ?: redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Méthode appelée après une authentification réussie
     */
    protected function authenticated(Request $request, $user)
    {
        // Rediriger selon le rôle
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Rediriger l'utilisateur selon son rôle
     */
    private function redirectBasedOnRole(User $user)
    {
        // Récupérer le rôle de manière sécurisée
        $role = $user->role;
        
        // Si c'est un enum, prendre la valeur
        if (is_object($role) && method_exists($role, 'value')) {
            $role = $role->value;
        }
        
        // Convertir en minuscules
        $role = strtolower(trim($role));
        
        // Correspondances directes
        switch ($role) {
            case 'admin':
            case 'administrateur':
            case '1':
                return redirect()->route('admin.dashboard');
                
            case 'professeur':
            case 'prof':
            case 'teacher':
            case '2':
                return redirect()->route('professeur.dashboard');
                
            case 'assistant':
            case 'assist':
            case '3':
                return redirect()->route('assistant.dashboard');
                
            case 'eleve':
            case 'etudiant':
            case 'student':
            case '4':
                return redirect()->route('eleve.dashboard');
                
            default:
                // Log l'erreur et rediriger vers login
                Log::error('Rôle non reconnu lors de la redirection', [
                    'user_id' => $user->id,
                    'role' => $role
                ]);
                return redirect()->route('login')->withErrors([
                    'email' => 'Rôle utilisateur non reconnu. Contactez l\'administrateur.'
                ]);
        }
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
}
