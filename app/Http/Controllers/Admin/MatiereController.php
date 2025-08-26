<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MatiereController extends Controller
{
    /**
     * Affiche la liste des matières
     */
    public function index(Request $request)
    {
        $query = Matiere::query()
            ->with(['niveaux'])
            ->withCount(['filieres', 'professeurs']);

        // Filtrage par recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtrage par type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filtrage par niveau
        if ($request->has('niveau_id') && $request->niveau_id) {
            $query->whereHas('niveaux', function($q) use ($request) {
                $q->where('niveaux.id', $request->niveau_id);
            });
        }

        // Filtrage par statut
        if ($request->has('est_actif') && $request->est_actif !== '') {
            $query->where('est_actif', $request->est_actif);
        }

        // Tri
        $sortField = $request->input('sort_field', 'nom');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        // Vérification des champs de tri autorisés
        $validSortFields = ['nom', 'type', 'prix', 'prix_prof', 'est_actif', 'created_at'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'nom';
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortField, $sortDirection);

        $matieres = $query->paginate(15);
        $niveaux = Niveau::orderBy('ordre')->get(['id', 'nom', 'type']);

        // Statistiques
        $stats = [
            'total' => Matiere::count(),
            'actives' => Matiere::where('est_actif', true)->count(),
            'inactives' => Matiere::where('est_actif', false)->count(),
            'prix_moyen' => [
                'eleve' => (float) Matiere::avg('prix') ?? 0,
                'professeur' => (float) Matiere::avg('prix_prof') ?? 0,
            ]
        ];

        return Inertia::render('Admin/Matieres/Index', [
            'matieres' => $matieres,
            'niveaux' => $niveaux,
            'filters' => array_merge([
                'search' => $request->search ?? '',
                'niveau_id' => $request->niveau_id,
                'type' => $request->type,
                'est_actif' => $request->est_actif,
                'sort_field' => $sortField,
                'sort_direction' => $sortDirection,
            ], $request->query()),
            'stats' => $stats
        ]);
    }

    /**
     * Affiche le formulaire de création d'une matière
     */
    public function create()
    {
        $niveaux = Niveau::orderBy('ordre')->get(['id', 'nom', 'type']);

        return Inertia::render('Admin/Matieres/Create', [
            'niveaux' => $niveaux,
        ]);
    }

    /**
     * Enregistre une nouvelle matière
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom',
            'description' => 'nullable|string|max:1000',
            'prix' => 'required|numeric|min:0',
            'prix_prof' => 'required|numeric|min:0|lte:prix',
            'est_actif' => 'required|boolean',
            'niveaux' => 'required|array|min:1',
            'niveaux.*' => 'exists:niveaux,id',
        ]);

        try {
            DB::beginTransaction();
            
            $matiere = Matiere::create([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'prix' => $validated['prix'],
                'prix_prof' => $validated['prix_prof'],
                'est_actif' => $validated['est_actif'],
            ]);
            
            // Attacher les niveaux à la matière
            $matiere->niveaux()->sync($validated['niveaux']);
            
            DB::commit();
            
            return redirect()
                ->route('admin.matieres.index')
                ->with('success', 'La matière a été créée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la création de la matière.');
        }
    }

    /**
     * Affiche les détails d'une matière
     */
    public function show(Matiere $matiere)
    {
        $matiere->load(['niveaux', 'professeurs', 'filieres']);

        return Inertia::render('Admin/Matieres/Show', [
            'matiere' => $matiere,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une matière
     */
    public function edit(Matiere $matiere)
    {
        $matiere->load('niveaux');
        $niveaux = Niveau::orderBy('ordre')->get(['id', 'nom', 'type']);

        return Inertia::render('Admin/Matieres/Edit', [
            'matiere' => $matiere,
            'niveaux' => $niveaux,
        ]);
    }

    /**
     * Met à jour une matière existante
     */
    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom,' . $matiere->id,
            'description' => 'nullable|string|max:1000',
            'prix' => 'required|numeric|min:0',
            'prix_prof' => 'required|numeric|min:0|lte:prix',
            'est_actif' => 'required|boolean',
            'niveaux' => 'required|array|min:1',
            'niveaux.*' => 'exists:niveaux,id',
        ]);

        try {
            DB::beginTransaction();
            
            $matiere->update([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'prix' => $validated['prix'],
                'prix_prof' => $validated['prix_prof'],
                'est_actif' => $validated['est_actif'],
            ]);
            
            // Mettre à jour les niveaux de la matière
            $matiere->niveaux()->sync($validated['niveaux']);
            
            DB::commit();
            
            return redirect()
                ->route('admin.matieres.index')
                ->with('success', 'La matière a été mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de la matière.');
        }
    }

    /**
     * Supprime une matière
     */
    public function destroy(Matiere $matiere)
    {
        // Vérifier s'il y a des professeurs ou des filières associées
        if ($matiere->professeurs()->exists() || $matiere->filieres()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette matière car elle est utilisée par des professeurs ou des filières.');
        }

        try {
            DB::beginTransaction();
            
            // Détacher les niveaux avant de supprimer
            $matiere->niveaux()->detach();
            $matiere->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.matieres.index')
                ->with('success', 'La matière a été supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la matière.');
        }
    }
}
