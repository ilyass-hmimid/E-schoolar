<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Affiche la page des paramètres
     */
    public function index(): View
    {
        return view('admin.settings.index');
    }
}
