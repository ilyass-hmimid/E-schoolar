<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvenementController extends Controller
{
    /**
     * Affiche la liste des événements
     */
    public function index()
    {
        return Inertia::render('Admin/Evenements/Index', [
            'evenements' => [],
        ]);
    }

    /**
     * Affiche le formulaire de création d'un événement
     */
    public function create()
    {
        return Inertia::render('Admin/Evenements/Create');
    }

    /**
     * Enregistre un nouvel événement
     */
    public function store(Request $request)
    {
        // Logique de création d'événement
        return redirect()->route('admin.evenements.index')
            ->with('success', 'Événement créé avec succès');
    }

    /**
     * Affiche les détails d'un événement
     */
    public function show($id)
    {
        return Inertia::render('Admin/Evenements/Show', [
            'evenement' => [],
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un événement
     */
    public function edit($id)
    {
        return Inertia::render('Admin/Evenements/Edit', [
            'evenement' => [],
        ]);
    }

    /**
     * Met à jour un événement
     */
    public function update(Request $request, $id)
    {
        // Logique de mise à jour
        return redirect()->route('admin.evenements.index')
            ->with('success', 'Événement mis à jour avec succès');
    }

    /**
     * Supprime un événement
     */
    public function destroy($id)
    {
        // Logique de suppression
        return redirect()->route('admin.evenements.index')
            ->with('success', 'Événement supprimé avec succès');
    }
}
