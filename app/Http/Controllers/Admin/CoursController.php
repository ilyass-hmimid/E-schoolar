<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cours;
use Illuminate\Http\Request;

class CoursController extends Controller
{
    /**
     * Afficher la liste des cours
     */
    public function index()
    {
        $cours = Cours::with(['niveau', 'matiere', 'professeur'])
                     ->latest()
                     ->paginate(10);
                     
        return view('admin.cours.index', compact('cours'));
    }

    /**
     * Afficher le formulaire de création d'un cours
     */
    public function create()
    {
        return view('admin.cours.create');
    }

    /**
     * Enregistrer un nouveau cours
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'niveau_id' => 'required|exists:niveaux,id',
            'matiere_id' => 'required|exists:matieres,id',
        ]);

        Cours::create($validated);

        return redirect()->route('admin.cours.index')
            ->with('success', 'Cours créé avec succès');
    }

    /**
     * Afficher un cours spécifique
     */
    public function show(Cours $cour)
    {
        return view('admin.cours.show', compact('cour'));
    }

    /**
     * Afficher le formulaire d'édition d'un cours
     */
    public function edit(Cours $cour)
    {
        return view('admin.cours.edit', compact('cour'));
    }

    /**
     * Mettre à jour un cours
     */
    public function update(Request $request, Cours $cour)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'niveau_id' => 'required|exists:niveaux,id',
            'matiere_id' => 'required|exists:matieres,id',
        ]);

        $cour->update($validated);

        return redirect()->route('admin.cours.index')
            ->with('success', 'Cours mis à jour avec succès');
    }

    /**
     * Supprimer un cours
     */
    public function destroy(Cours $cour)
    {
        $cour->delete();
        
        return redirect()->route('admin.cours.index')
            ->with('success', 'Cours supprimé avec succès');
    }
}
