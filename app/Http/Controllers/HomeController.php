<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Si l'utilisateur est connecté, afficher la vue d'accueil
        return view('home');
    }
}
