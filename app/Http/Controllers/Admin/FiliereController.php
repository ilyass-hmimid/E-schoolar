<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FiliereController extends Controller
{
    /**
     * Affiche la liste des filières
     */
    public function index(Request $request)
    {
        $query = Filiere::query()
            ->with(['niveau', 'matieres'])
            ->withCount('eleves')
            ->latest();

        // Filtrage par niveau
        if ($request->has('niveau_id') && $request->niveau_id) {
            $query->where('niveau_id', $request->niveau_id);
        }

        $filieres = $query->paginate(15);
        $niveaux = Niveau::orderBy('ordre')->get(['id', 'nom']);

        return Inertia::render('Admin/Filieres/Index', [
            'filieres' => $filieres,
            'niveaux' => $niveaux,
            'filters' => $request->only(['niveau_id'])
        ]);
    }

    /**
     * Affiche le formulaire de création d'une filière
     */
    public function create()
    {
        $niveaux = Niveau::orderBy('ordre')->get(['id', 'nom']);
        $matieres = Matiere::where('est_actif', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'prix']);

        return Inertia::render('Admin/Filieres/Create', [
            'niveaux' => $niveaux,
            'matieres' => $matieres,
        ]);
    }

    /**
     * Enregistre une nouvelle filière
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'abreviation' => 'nullable|string|max:10',
            'niveau_id' => 'required|exists:niveaux,id',
            'description' => 'nullable|string|max:1000',
            'est_actif' => 'required|boolean',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        try {
            DB::beginTransaction();
            
            $filiere = Filiere::create([
                'nom' => $validated['nom'],
                'abreviation' => $validated['abreviation'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'description' => $validated['description'] ?? null,
                'est_actif' => $validated['est_actif'],
            ]);
            
            // Attacher les matières à la filière
            $filiere->matieres()->sync($validated['matieres']);
            
            DB::commit();
            
            return redirect()
                ->route('admin.filieres.index')
                ->with('success', 'La filière a été créée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la création de la filière.');
        }
    }

    /**
     * Affiche les détails d'une filière
     */
    public function show(Filiere $filiere)
    {
        $filiere->load(['niveau', 'matieres', 'eleves']);

        return Inertia::render('Admin/Filieres/Show', [
            'filiere' => $filiere,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une filière
     */
    public function edit(Filiere $filiere)
    {
        $filiere->load('matieres');
        $niveaux = Niveau::orderBy('ordre')->get(['id', 'nom']);
        $matieres = Matiere::where('est_actif', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'prix']);

        return Inertia::render('Admin/Filieres/Edit', [
            'filiere' => $filiere,
            'niveaux' => $niveaux,
            'matieres' => $matieres,
        ]);
    }

    /**
     * Met à jour une filière existante
     */
    public function update(Request $request, Filiere $filiere)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'abreviation' => 'nullable|string|max:10',
            'niveau_id' => 'required|exists:niveaux,id',
            'description' => 'nullable|string|max:1000',
            'est_actif' => 'required|boolean',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        try {
            DB::beginTransaction();
            
            $filiere->update([
                'nom' => $validated['nom'],
                'abreviation' => $validated['abreviation'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'description' => $validated['description'] ?? null,
                'est_actif' => $validated['est_actif'],
            ]);
            
            // Mettre à jour les matières de la filière
            $filiere->matieres()->sync($validated['matieres']);
            
            DB::commit();
            
            return redirect()
                ->route('admin.filieres.index')
                ->with('success', 'La filière a été mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de la filière.');
        }
    }

    /**
     * Supprime une filière
     */
    public function destroy(Filiere $filiere)
    {
        // Vérifier s'il y a des élèves inscrits
        if ($filiere->eleves()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette filière car des élèves y sont inscrits.');
        }

        try {
            DB::beginTransaction();
            
            // Détacher les matières avant de supprimer
            $filiere->matieres()->detach();
            $filiere->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.filieres.index')
                ->with('success', 'La filière a été supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la filière.');
        }
    }
}
