<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class MatiereController extends Controller
{
    /**
     * Afficher la liste des matières
     */
    public function index()
    {
        $this->authorize('viewAny', Matiere::class);
        
        $matieres = Matiere::latest()
            ->get()
            ->map(function ($matiere) {
                return [
                    'id' => $matiere->id,
                    'libelle' => $matiere->Libelle,
                    'etudiants_count' => $matiere->etudiants()->count(),
                    'created_at' => $matiere->created_at ? $matiere->created_at->format('d/m/Y H:i') : null,
                    'updated_at' => $matiere->updated_at ? $matiere->updated_at->format('d/m/Y H:i') : null,
                ];
            });

        return Inertia::render('Matieres/Index', [
            'matieres' => $matieres,
            'can' => [
                'create' => Auth::user()->can('create', Matiere::class),
                'edit' => Auth::user()->can('update', Matiere::class),
                'delete' => Auth::user()->can('delete', Matiere::class),
            ]
        ]);
    }

    /**
     * Afficher le formulaire de création d'une matière
     */
    public function create()
    {
        $this->authorize('create', Matiere::class);
        
        return Inertia::render('Matieres/Create');
    }

    /**
     * Enregistrer une nouvelle matière
     */
    public function store(Request $request)
    {
        $this->authorize('create', Matiere::class);
        
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:Matiere,Libelle',
        ]);

        Matiere::create([
            'Libelle' => $validated['libelle'],
        ]);

        return redirect()->route('matieres.index')
            ->with('success', 'Matière créée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'une matière
     */
    public function edit(Matiere $matiere)
    {
        $this->authorize('update', $matiere);
        
        return Inertia::render('Matieres/Edit', [
            'matiere' => [
                'id' => $matiere->id,
                'libelle' => $matiere->Libelle,
                'etudiants_count' => $matiere->etudiants()->count(),
                'created_at' => $matiere->created_at ? $matiere->created_at->format('d/m/Y H:i') : null,
                'updated_at' => $matiere->updated_at ? $matiere->updated_at->format('d/m/Y H:i') : null,
            ]
        ]);
    }

    /**
     * Mettre à jour une matière existante
     */
    public function update(Request $request, Matiere $matiere)
    {
        $this->authorize('update', $matiere);
        
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:Matiere,Libelle,' . $matiere->id,
        ]);

        $matiere->update([
            'Libelle' => $validated['libelle'],
        ]);

        return redirect()->route('matieres.index')
            ->with('success', 'Matière mise à jour avec succès.');
    }

    /**
     * Supprimer une matière
     */
    public function destroy(Matiere $matiere)
    {
        $this->authorize('delete', $matiere);
        
        // Vérifier s'il y a des étudiants inscrits à cette matière
        if ($matiere->etudiants()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette matière car des étudiants y sont inscrits.');
        }

        $matiere->delete();

        return redirect()->route('matieres.index')
            ->with('success', 'Matière supprimée avec succès.');
    }

    /**
     * Rechercher des matières pour l'autocomplétion
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        return Matiere::where('Libelle', 'like', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function ($matiere) {
                return [
                    'id' => $matiere->id,
                    'libelle' => $matiere->Libelle,
                ];
            });
    }
}
