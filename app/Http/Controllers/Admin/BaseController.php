<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class BaseController extends Controller
{
    /**
     * Affiche la page d'accueil de l'administration
     */
    public function index()
    {
        return Inertia::render('Admin/Dashboard');
    }
}
