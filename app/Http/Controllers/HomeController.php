<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (auth()->check()) {
            // Rediriger en fonction du rôle de l'utilisateur
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif (auth()->user()->hasRole('professeur')) {
                return redirect()->route('professeur.dashboard');
            } elseif (auth()->user()->hasRole('assistant')) {
                return redirect()->route('assistant.dashboard');
            } elseif (auth()->user()->hasRole('eleve')) {
                return redirect()->route('eleve.dashboard');
            }
        }
        
        // Par défaut, rediriger vers la page de connexion
        return redirect()->route('login');
    }
}
