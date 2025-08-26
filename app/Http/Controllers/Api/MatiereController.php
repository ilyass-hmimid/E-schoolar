<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    /**
     * Récupère la liste des matières avec pagination et filtres
     */
    public function index(Request $request)
    {
        $query = Matiere::query()
            ->with(['niveaux', 'filieres'])
            ->withCount(['professeurs', 'filieres']);

        // Filtre par recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        // Filtre par statut
        if ($request->has('est_actif') && $request->est_actif !== '') {
            $query->where('est_actif', $request->est_actif);
        }

        // Filtre par niveau
        if ($request->has('niveau_id') && !empty($request->niveau_id)) {
            $query->whereHas('niveaux', function($q) use ($request) {
                $q->where('niveaux.id', $request->niveau_id);
            });
        }

        // Tri
        $sortField = $request->input('sort_field', 'nom');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        // Vérifier que le champ de tri est valide
        $validSortFields = ['nom', 'type', 'prix', 'prix_prof', 'est_actif', 'created_at'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'nom';
        }
        
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 15);
        $perPage = min(max($perPage, 5), 100); // Limiter entre 5 et 100 éléments par page
        
        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Récupère les statistiques des matières
     */
    public function stats()
    {
        $stats = [
            'total' => Matiere::count(),
            'actives' => Matiere::where('est_actif', true)->count(),
            'inactives' => Matiere::where('est_actif', false)->count(),
            'types' => [],
            'types_details' => []
        ];
        
        // Récupérer le nombre de matières par type
        $types = Matiere::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
        
        // Formater les données pour le graphique
        foreach (Matiere::$types as $key => $label) {
            $count = $types[$key] ?? 0;
            $stats['types'][] = [
                'name' => $label,
                'value' => $count
            ];
            
            $stats['types_details'][$key] = [
                'label' => $label,
                'count' => $count,
                'percentage' => $stats['total'] > 0 ? round(($count / $stats['total']) * 100, 1) : 0
            ];
        }
        
        // Ajouter les statistiques de prix moyens
        $stats['prix_moyen'] = [
            'eleve' => Matiere::avg('prix') ?? 0,
            'professeur' => Matiere::avg('prix_prof') ?? 0
        ];
        
        return $stats;
    }

    /**
     * Récupère une matière par son ID
     */
    public function show(Matiere $matiere)
    {
        return $matiere->load(['niveaux', 'filieres', 'professeurs']);
    }

    /**
     * Crée une nouvelle matière
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom',
            'type' => 'required|in:' . implode(',', array_keys(\App\Models\Matiere::$types)),
            'description' => 'nullable|string|max:1000',
            'prix' => 'required|numeric|min:0',
            'prix_prof' => 'required|numeric|min:0|lte:prix',
            'est_actif' => 'required|boolean',
            'niveaux' => 'required|array|min:1',
            'niveaux.*' => 'exists:niveaux,id',
        ]);

        try {
            DB::beginTransaction();
            
            $matiere = Matiere::create($validated);
            $matiere->niveaux()->sync($validated['niveaux']);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Matière créée avec succès',
                'data' => $matiere->load('niveaux')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de la matière',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour une matière existante
     */
    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom,' . $matiere->id,
            'type' => 'required|in:' . implode(',', array_keys(\App\Models\Matiere::$types)),
            'description' => 'nullable|string|max:1000',
            'prix' => 'required|numeric|min:0',
            'prix_prof' => 'required|numeric|min:0|lte:prix',
            'est_actif' => 'required|boolean',
            'niveaux' => 'required|array|min:1',
            'niveaux.*' => 'exists:niveaux,id',
        ]);

        try {
            DB::beginTransaction();
            
            $matiere->update($validated);
            $matiere->niveaux()->sync($validated['niveaux']);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Matière mise à jour avec succès',
                'data' => $matiere->load('niveaux')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de la matière',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime une matière
     */
    public function destroy(Matiere $matiere)
    {
        // Vérifier s'il y a des professeurs ou des filières associées
        if ($matiere->professeurs()->exists() || $matiere->filieres()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer cette matière car elle est utilisée par des professeurs ou des filières.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $matiere->niveaux()->detach();
            $matiere->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Matière supprimée avec succès'
            ], 204);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la matière',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
