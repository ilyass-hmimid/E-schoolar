<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PackResource;
use App\Http\Resources\V1\PackCollection;
use App\Models\Pack;
use App\Helpers\PackHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackController extends Controller
{
    /**
     * Affiche la liste des packs avec pagination et filtres
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'type' => [
                'nullable',
                Rule::in(array_keys(config('packs.types')))
            ],
            'status' => 'nullable|in:active,inactive',
            'sort_by' => 'nullable|in:nom,type,prix,created_at',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Pack::query();

        // Filtre par recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('est_actif', $request->input('status') === 'active');
        }

        // Tri
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 15);
        $packs = $query->paginate($perPage);

        return new PackCollection($packs);
    }

    /**
     * Affiche les détails d'un pack
     *
     * @param  \App\Models\Pack  $pack
     * @return \App\Http\Resources\V1\PackResource
     */
    public function show(Pack $pack)
    {
        $pack->load(['matieres', 'ventes']);
        return new PackResource($pack);
    }

    /**
     * Crée un nouveau pack
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => [
                'required',
                Rule::in(array_keys(config('packs.types')))
            ],
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0|lt:prix',
            'duree_jours' => 'required|integer|min:1',
            'est_actif' => 'boolean',
            'est_populaire' => 'boolean',
            'matieres' => 'nullable|array',
            'matieres.*.id' => 'required|exists:matieres,id',
            'matieres.*.nombre_heures' => 'required|integer|min:1',
        ]);

        // Création du pack
        $pack = Pack::create([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'prix' => $validated['prix'],
            'prix_promo' => $validated['prix_promo'] ?? null,
            'duree_jours' => $validated['duree_jours'],
            'est_actif' => $validated['est_actif'] ?? true,
            'est_populaire' => $validated['est_populaire'] ?? false,
        ]);

        // Attachement des matières si fournies
        if (isset($validated['matieres'])) {
            $matieres = collect($validated['matieres'])->mapWithKeys(function ($item) {
                return [$item['id'] => ['nombre_heures_par_matiere' => $item['nombre_heures']]];
            });
            
            $pack->matieres()->attach($matieres);
        }

        return response()->json([
            'message' => 'Pack créé avec succès',
            'data' => new PackResource($pack->load('matieres'))
        ], 201);
    }

    /**
     * Met à jour un pack existant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pack  $pack
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Pack $pack)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => [
                'sometimes',
                'required',
                Rule::in(array_keys(config('packs.types')))
            ],
            'prix' => 'sometimes|required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0|lt:prix',
            'duree_jours' => 'sometimes|required|integer|min:1',
            'est_actif' => 'sometimes|boolean',
            'est_populaire' => 'sometimes|boolean',
            'matieres' => 'nullable|array',
            'matieres.*.id' => 'required|exists:matieres,id',
            'matieres.*.nombre_heures' => 'required|integer|min:1',
        ]);

        // Mise à jour du pack
        $pack->update([
            'nom' => $validated['nom'] ?? $pack->nom,
            'description' => $validated['description'] ?? $pack->description,
            'type' => $validated['type'] ?? $pack->type,
            'prix' => $validated['prix'] ?? $pack->prix,
            'prix_promo' => $validated['prix_promo'] ?? $pack->prix_promo,
            'duree_jours' => $validated['duree_jours'] ?? $pack->duree_jours,
            'est_actif' => $validated['est_actif'] ?? $pack->est_actif,
            'est_populaire' => $validated['est_populaire'] ?? $pack->est_populaire,
        ]);

        // Mise à jour des matières si fournies
        if (isset($validated['matieres'])) {
            $matieres = collect($validated['matieres'])->mapWithKeys(function ($item) {
                return [$item['id'] => ['nombre_heures_par_matiere' => $item['nombre_heures']]];
            });
            
            $pack->matieres()->sync($matieres);
        }

        return response()->json([
            'message' => 'Pack mis à jour avec succès',
            'data' => new PackResource($pack->load('matieres'))
        ]);
    }

    /**
     * Supprime un pack
     *
     * @param  \App\Models\Pack  $pack
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Pack $pack)
    {
        // Vérifier si le pack peut être supprimé
        if ($pack->ventes()->exists() || $pack->inscriptions()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer ce pack car il est associé à des ventes ou inscriptions',
            ], 422);
        }

        $pack->delete();

        return response()->json([
            'message' => 'Pack supprimé avec succès'
        ]);
    }

    /**
     * Active ou désactive un pack
     *
     * @param  \App\Models\Pack  $pack
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Pack $pack)
    {
        $pack->update([
            'est_actif' => !$pack->est_actif
        ]);

        return response()->json([
            'message' => $pack->est_actif ? 'Pack activé avec succès' : 'Pack désactivé avec succès',
            'data' => new PackResource($pack)
        ]);
    }

    /**
     * Met en avant ou retire de la mise en avant un pack
     *
     * @param  \App\Models\Pack  $pack
     * @return \Illuminate\Http\JsonResponse
     */
    public function togglePopularity(Pack $pack)
    {
        $pack->update([
            'est_populaire' => !$pack->est_populaire
        ]);

        return response()->json([
            'message' => $pack->est_populaire ? 'Pack mis en avant avec succès' : 'Pack retiré de la mise en avant avec succès',
            'data' => new PackResource($pack)
        ]);
    }

    /**
     * Duplique un pack existant
     *
     * @param  \App\Models\Pack  $pack
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate(Pack $pack)
    {
        $newPack = $pack->replicate();
        $newPack->nom = $pack->nom . ' (Copie)';
        $newPack->est_actif = false;
        $newPack->est_populaire = false;
        $newPack->push();

        // Dupliquer les relations si nécessaire
        foreach ($pack->matieres as $matiere) {
            $newPack->matieres()->attach($matiere->id, [
                'nombre_heures_par_matiere' => $matiere->pivot->nombre_heures_par_matiere
            ]);
        }

        return response()->json([
            'message' => 'Pack dupliqué avec succès',
            'data' => new PackResource($newPack->load('matieres'))
        ], 201);
    }

    /**
     * Récupère les statistiques des packs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $stats = PackHelper::getStats();
        
        return response()->json([
            'data' => $stats
        ]);
    }
}
