<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackController extends Controller
{
    /**
     * Récupère la liste des packs pour l'API
     */
    public function index(Request $request)
    {
        $query = Pack::query()
            ->select([
                'id',
                'nom',
                'type',
                'prix',
                'prix_promo',
                'duree_jours',
                'est_actif',
                'est_populaire',
                'created_at'
            ]);

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
        if (in_array($sortField, $validSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('nom', 'asc');
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $packs = $query->paginate($perPage);

        return response()->json([
            'data' => $packs->items(),
            'current_page' => $packs->currentPage(),
            'last_page' => $packs->lastPage(),
            'per_page' => $packs->perPage(),
            'total' => $packs->total(),
        ]);
    }

    /**
     * Récupère les statistiques des packs
     */
    public function stats()
    {
        $stats = [
            'total' => Pack::count(),
            'actifs' => Pack::where('est_actif', true)->count(),
            'prix_moyen' => (float) Pack::avg('prix') ?? 0,
            'total_ventes' => (float) DB::table('ventes')
                ->join('pack_vente', 'ventes.id', '=', 'pack_vente.vente_id')
                ->sum('pack_vente.prix')
        ];

        return response()->json($stats);
    }

    /**
     * Récupère les détails d'un pack
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

        return response()->json([
            'pack' => $pack,
            'stats' => $stats,
            'dernieres_ventes' => $dernieresVentes
        ]);
    }
}
