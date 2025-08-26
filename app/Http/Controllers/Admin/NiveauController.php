<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class NiveauController extends Controller
{
    /**
     * Affiche la liste des niveaux
     */
    public function index(Request $request)
    {
        $query = Niveau::query()
            ->withCount('filieres')
            ->orderBy('ordre')
            ->orderBy('nom');

        // Filtrage par type si spécifié
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $niveaux = $query->paginate(15);

        return Inertia::render('Admin/Niveaux/Index', [
            'niveaux' => $niveaux,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un niveau
     */
    public function create()
    {
        return Inertia::render('Admin/Niveaux/Create');
    }

    /**
     * Enregistre un nouveau niveau
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:niveaux,nom',
            'type' => 'required|in:primaire,college,tronc_commun,premiere_bac,deuxieme_bac',
            'ordre' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            
            $niveau = Niveau::create($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.niveaux.index')
                ->with('success', 'Le niveau a été créé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la création du niveau.');
        }
    }

    /**
     * Affiche les détails d'un niveau
     */
    public function show(Niveau $niveau)
    {
        $niveau->load(['filieres' => function($query) {
            $query->orderBy('nom');
        }]);

        return Inertia::render('Admin/Niveaux/Show', [
            'niveau' => $niveau,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un niveau
     */
    public function edit(Niveau $niveau)
    {
        return Inertia::render('Admin/Niveaux/Edit', [
            'niveau' => $niveau,
        ]);
    }

    /**
     * Met à jour un niveau existant
     */
    public function update(Request $request, Niveau $niveau)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:niveaux,nom,' . $niveau->id,
            'type' => 'required|in:primaire,college,tronc_commun,premiere_bac,deuxieme_bac',
            'ordre' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            
            $niveau->update($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.niveaux.index')
                ->with('success', 'Le niveau a été mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du niveau.');
        }
    }

    /**
     * Supprime un niveau
     */
    public function destroy(Niveau $niveau)
    {
        // Vérifier s'il y a des filières associées
        if ($niveau->filieres()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce niveau car il est associé à des filières.');
        }

        try {
            DB::beginTransaction();
            
            $niveau->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.niveaux.index')
                ->with('success', 'Le niveau a été supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression du niveau.');
        }
    }
}
