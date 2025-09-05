<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RedirectController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectTo()
    {
        $user = auth()->user();
        
        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
            auth()->logout();
            return '/login';
        }

        // Redirection basée sur le rôle
        if ($user->role == 1) { // Admin
            return '/admin/dashboard';
        }
        
        if ($user->role == 2) { // Professeur
            return '/professeur/dashboard';
        }
        
        if ($user->role == 3) { // Assistant
            return '/assistant/dashboard';
        }
        
        if ($user->role == 4) { // Élève
            return '/eleve/dashboard';
        }

        // Si le rôle n'est pas reconnu, on déconnecte et on redirige avec une erreur
        auth()->logout();
        return '/login';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
