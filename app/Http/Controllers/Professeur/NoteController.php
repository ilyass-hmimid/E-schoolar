<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes
     */
    public function index()
    {
        return Inertia::render('Professeur/Notes/Index');
    }

    /**
     * Affiche le formulaire de création d'une note
     */
    public function create()
    {
        return Inertia::render('Professeur/Notes/Create');
    }

    /**
     * Enregistre une nouvelle note
     */
    public function store(Request $request)
    {
        // Logique de création d'une note
    }

    /**
     * Affiche les détails d'une note
     */
    public function show($id)
    {
        return Inertia::render('Professeur/Notes/Show', [
            'noteId' => $id
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une note
     */
    public function edit($id)
    {
        return Inertia::render('Professeur/Notes/Edit', [
            'noteId' => $id
        ]);
    }

    /**
     * Met à jour une note existante
     */
    public function update(Request $request, $id)
    {
        // Logique de mise à jour d'une note
    }

    /**
     * Supprime une note
     */
    public function destroy($id)
    {
        // Logique de suppression d'une note
    }
}
