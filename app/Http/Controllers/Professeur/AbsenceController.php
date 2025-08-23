<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences
     */
    public function index()
    {
        return Inertia::render('Professeur/Absences/Index');
    }

    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create()
    {
        return Inertia::render('Professeur/Absences/Create');
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        // Logique de création d'une absence
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show($id)
    {
        return Inertia::render('Professeur/Absences/Show', [
            'absenceId' => $id
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une absence
     */
    public function edit($id)
    {
        return Inertia::render('Professeur/Absences/Edit', [
            'absenceId' => $id
        ]);
    }

    /**
     * Met à jour une absence existante
     */
    public function update(Request $request, $id)
    {
        // Logique de mise à jour d'une absence
    }

    /**
     * Supprime une absence
     */
    public function destroy($id)
    {
        // Logique de suppression d'une absence
    }
}
