<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RessourceController extends Controller
{
    public function index()
    {
        return Inertia::render('Professeur/Ressources/Index');
    }

    public function create()
    {
        return Inertia::render('Professeur/Ressources/Create');
    }

    public function store(Request $request)
    {
        // Logique de création d'une ressource
    }

    public function show($id)
    {
        return Inertia::render('Professeur/Ressources/Show', [
            'ressourceId' => $id
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('Professeur/Ressources/Edit', [
            'ressourceId' => $id
        ]);
    }

    public function update(Request $request, $id)
    {
        // Logique de mise à jour d'une ressource
    }

    public function destroy($id)
    {
        // Logique de suppression d'une ressource
    }
}
