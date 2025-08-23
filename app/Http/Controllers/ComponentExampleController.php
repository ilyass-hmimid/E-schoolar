<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComponentExampleController extends Controller
{
    /**
     * Affiche la page d'exemples de composants
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('examples');
    }
}
