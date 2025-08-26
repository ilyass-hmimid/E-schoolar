<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackController extends Controller
{
    /**
     * Affiche la liste des packs
     */
    public function index(Request $request)
    {
        $query = Pack::query()
            ->withCount(['ventes'])
            ->orderBy('nom');

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

        // Filtrage par statut
        if ($request->has('est_actif') && $request->est_actif !== '') {
            $query->where('est_actif', $request->est_actif);
        }

        // Tri
        $sortField = $request->input('sort_field', 'nom');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        // Vérification des champs de tri autorisés
        $validSortFields = ['nom', 'type', 'prix', 'duree_jours', 'est_actif', 'created_at'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'nom';
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortField, $sortDirection);

        $packs = $query->paginate(15);

        // Statistiques
        $stats = [
            'total' => Pack::count(),
            'actifs' => Pack::where('est_actif', true)->count(),
            'prix_moyen' => (float) Pack::avg('prix') ?? 0,
            'total_ventes' => (float) DB::table('ventes')
                ->join('pack_vente', 'ventes.id', '=', 'pack_vente.vente_id')
                ->sum('pack_vente.prix')
        ];

        return Inertia::render('Admin/Packs/Index', [
            'packs' => $packs,
            'filters' => array_merge([
                'search' => $request->search ?? '',
                'type' => $request->type,
                'est_actif' => $request->est_actif,
                'sort_field' => $sortField,
                'sort_direction' => $sortDirection,
            ], $request->query()),
            'stats' => $stats
        ]);
    }

    /**
     * Affiche le formulaire de création d'un pack
     */
    public function create()
    {
        return Inertia::render('Admin/Packs/Create');
    }

    /**
     * Enregistre un nouveau pack
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:cours,abonnement,formation,autre',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0|lt:prix',
            'duree_jours' => 'required|integer|min:1',
            'est_actif' => 'boolean',
            'est_populaire' => 'boolean',
        ]);

        // Création du slug à partir du nom
        $validated['slug'] = Str::slug($validated['nom']) . '-' . Str::random(6);

        $pack = Pack::create($validated);

        return redirect()
            ->route('admin.packs.show', $pack->id)
            ->with('success', 'Le pack a été créé avec succès.');
    }

    /**
     * Affiche les détails d'un pack
     */
    public function show(Pack $pack)
    {
        $pack->loadCount('ventes');

        // Statistiques du pack
        $stats = [
            'nombre_ventes' => $pack->ventes()->count(),
            'revenu_total' => $pack->ventes()->sum('prix'),
            'taux_utilisation' => 0, // À implémenter selon la logique métier
        ];

        // Dernières ventes
        $dernieresVentes = $pack->ventes()
            ->with('utilisateur')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($vente) {
                return [
                    'id' => $vente->id,
                    'client_nom' => $vente->utilisateur->name,
                    'date_vente' => $vente->created_at->toDateTimeString(),
                    'montant' => $vente->pivot->prix
                ];
            });

        return Inertia::render('Admin/Packs/Show', [
            'pack' => $pack,
            'stats' => $stats,
            'dernieresVentes' => $dernieresVentes
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un pack
     */
    public function edit(Pack $pack)
    {
        return Inertia::render('Admin/Packs/Edit', [
            'pack' => $pack
        ]);
    }

    /**
     * Met à jour un pack existant
     */
    public function update(Request $request, Pack $pack)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:cours,abonnement,formation,autre',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0|lt:prix',
            'duree_jours' => 'required|integer|min:1',
            'est_actif' => 'boolean',
            'est_populaire' => 'boolean',
        ]);

        $pack->update($validated);

        return redirect()
            ->route('admin.packs.show', $pack->id)
            ->with('success', 'Le pack a été mis à jour avec succès.');
    }

    /**
     * Supprime un pack
     */
    public function destroy(Pack $pack)
    {
        // Vérifier si le pack est utilisé dans des ventes
        if ($pack->ventes()->count() > 0) {
            return back()->withErrors([
                'message' => 'Impossible de supprimer ce pack car il est associé à des ventes.'
            ]);
        }

        $pack->delete();

        return redirect()
            ->route('admin.packs.index')
            ->with('success', 'Le pack a été supprimé avec succès.');
    }

    /**
     * Bascule le statut actif/inactif d'un pack
     */
    public function toggleStatus(Pack $pack)
    {
        $pack->update([
            'est_actif' => !$pack->est_actif
        ]);

        return back()->with('success', 'Le statut du pack a été mis à jour.');
    }

    /**
     * Bascule la mise en avant d'un pack
     */
    public function togglePopularity(Pack $pack)
    {
        $pack->update([
            'est_populaire' => !$pack->est_populaire
        ]);

        return back()->with('success', 'La mise en avant du pack a été mise à jour.');
    }

    /**
     * Duplique un pack existant
     */
    public function duplicate(Pack $pack)
    {
        $newPack = $pack->replicate();
        $newPack->nom = $pack->nom . ' (Copie)';
        $newPack->slug = Str::slug($newPack->nom) . '-' . Str::random(6);
        $newPack->est_actif = false;
        $newPack->est_populaire = false;
        $newPack->save();

        return redirect()
            ->route('admin.packs.edit', $newPack->id)
            ->with('success', 'Le pack a été dupliqué. Vous pouvez maintenant le modifier.');
    }
}
