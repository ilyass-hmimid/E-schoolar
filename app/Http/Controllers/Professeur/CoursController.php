<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CoursController extends Controller
{
    /**
     * Affiche la liste des cours
     */
    public function index()
    {
        return Inertia::render('Professeur/Cours/Index');
    }

    /**
     * Affiche le formulaire de création d'un cours
     */
    public function create()
    {
        return Inertia::render('Professeur/Cours/Create');
    }

    /**
     * Enregistre un nouveau cours
     */
    public function store(Request $request)
    {
        // Logique de création d'un cours
    }

    /**
     * Affiche les détails d'un cours
     */
    public function show($id)
    {
        return Inertia::render('Professeur/Cours/Show', [
            'coursId' => $id
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un cours
     */
    public function edit($id)
    {
        return Inertia::render('Professeur/Cours/Edit', [
            'coursId' => $id
        ]);
    }

    /**
     * Met à jour un cours existant
     */
    public function update(Request $request, $id)
    {
        // Logique de mise à jour d'un cours
    }

    /**
     * Supprime un cours
     */
    public function destroy($id)
    {
        // Logique de suppression d'un cours
    }
}
