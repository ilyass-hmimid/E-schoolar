<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\User;
use App\Models\PackVente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaiementApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Paiement::with(['eleve', 'pack']);
        
        // Filtrage par élève
        if ($eleveId = request('eleve_id')) {
            $query->where('eleve_id', $eleveId);
        }
        
        // Filtrage par pack
        if ($packId = request('pack_id')) {
            $query->where('pack_vente_id', $packId);
        }
        
        // Filtrage par statut
        if ($statut = request('statut')) {
            $query->where('statut', $statut);
        }
        
        // Filtrage par date
        if ($dateDebut = request('date_debut')) {
            $query->whereDate('date_paiement', '>=', $dateDebut);
        }
        
        if ($dateFin = request('date_fin')) {
            $query->whereDate('date_paiement', '<=', $dateFin);
        }
        
        // Tri
        $sort = request('sort', 'date_paiement');
        $direction = request('direction', 'desc');
        
        $validSorts = ['date_paiement', 'montant', 'created_at'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        }
        
        $paiements = $query->paginate(request('per_page', 15));
        
        return response()->json([
            'data' => $paiements->items(),
            'meta' => [
                'current_page' => $paiements->currentPage(),
                'last_page' => $paiements->lastPage(),
                'per_page' => $paiements->perPage(),
                'total' => $paiements->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:users,id',
            'pack_vente_id' => 'required|exists:pack_ventes,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'methode_paiement' => 'required|in:espece,cheque,virement,carte_bancaire,autre',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'statut' => 'required|in:en_attente,paye,annule,rembourse',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Vérifier que l'utilisateur est bien un élève
        $eleve = User::findOrFail($request->eleve_id);
        if ($eleve->role !== 'eleve') {
            return response()->json([
                'message' => 'L\'utilisateur spécifié n\'est pas un élève'
            ], 422);
        }
        
        // Vérifier que le pack existe
        $pack = PackVente::findOrFail($request->pack_vente_id);
        
        // Si le montant n'est pas fourni, utiliser celui du pack
        $montant = $request->montant ?? $pack->prix;
        
        $paiement = Paiement::create([
            'eleve_id' => $request->eleve_id,
            'pack_vente_id' => $request->pack_vente_id,
            'montant' => $montant,
            'date_paiement' => $request->date_paiement,
            'methode_paiement' => $request->methode_paiement,
            'reference' => $request->reference,
            'notes' => $request->notes,
            'statut' => $request->statut,
            'enregistre_par' => auth()->id(),
        ]);
        
        // Mettre à jour le statut du pack si nécessaire
        if ($request->statut === 'paye') {
            $pack->update(['statut' => 'paye']);
        }
        
        // Charger les relations pour la réponse
        $paiement->load(['eleve', 'pack']);

        return response()->json([
            'message' => 'Paiement enregistré avec succès',
            'data' => $paiement
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement)
    {
        $paiement->load(['eleve', 'pack']);
        return response()->json(['data' => $paiement]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paiement $paiement)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'sometimes|required|exists:users,id',
            'pack_vente_id' => 'sometimes|required|exists:pack_ventes,id',
            'montant' => 'sometimes|required|numeric|min:0',
            'date_paiement' => 'sometimes|required|date',
            'methode_paiement' => 'sometimes|required|in:espece,cheque,virement,carte_bancaire,autre',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'statut' => 'sometimes|required|in:en_attente,paye,annule,rembourse',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Vérifications supplémentaires si les champs sont fournis
        if ($request->has('eleve_id')) {
            $eleve = User::findOrFail($request->eleve_id);
            if ($eleve->role !== 'eleve') {
                return response()->json([
                    'message' => 'L\'utilisateur spécifié n\'est pas un élève'
                ], 422);
            }
        }
        
        // Sauvegarder l'ancien statut pour la mise à jour du pack
        $ancienStatut = $paiement->statut;
        
        // Mise à jour du paiement
        $paiement->update($request->all());
        
        // Mettre à jour le statut du pack si le statut a changé
        if ($request->has('statut') && $request->statut !== $ancienStatut) {
            $paiement->pack->update(['statut' => $request->statut]);
        }
        
        // Recharger les relations pour la réponse
        $paiement->load(['eleve', 'pack']);
        
        return response()->json([
            'message' => 'Paiement mis à jour avec succès',
            'data' => $paiement
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement)
    {
        // Ne pas permettre la suppression si le paiement est déjà validé
        if ($paiement->statut === 'paye') {
            return response()->json([
                'message' => 'Impossible de supprimer un paiement déjà validé'
            ], 422);
        }
        
        $paiement->delete();
        
        return response()->json([
            'message' => 'Paiement supprimé avec succès'
        ]);
    }
    
    /**
     * Valider un paiement
     */
    public function valider(Paiement $paiement)
    {
        if ($paiement->statut !== 'en_attente') {
            return response()->json([
                'message' => 'Seuls les paiements en attente peuvent être validés'
            ], 422);
        }
        
        $paiement->update([
            'statut' => 'paye',
            'valide_par' => auth()->id(),
            'date_validation' => now(),
        ]);
        
        // Mettre à jour le statut du pack
        $paiement->pack->update(['statut' => 'paye']);
        
        return response()->json([
            'message' => 'Paiement validé avec succès',
            'data' => $paiement->fresh()
        ]);
    }
    
    /**
     * Annuler un paiement
     */
    public function annuler(Request $request, Paiement $paiement)
    {
        $validator = Validator::make($request->all(), [
            'raison' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $paiement->update([
            'statut' => 'annule',
            'raison_annulation' => $request->raison,
            'annule_par' => auth()->id(),
            'date_annulation' => now(),
        ]);
        
        return response()->json([
            'message' => 'Paiement annulé avec succès',
            'data' => $paiement->fresh()
        ]);
    }
    
    /**
     * Rembourser un paiement
     */
    public function rembourser(Request $request, Paiement $paiement)
    {
        if ($paiement->statut !== 'paye') {
            return response()->json([
                'message' => 'Seuls les paiements validés peuvent être remboursés'
            ], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'montant_rembourse' => 'required|numeric|min:0|max:' . $paiement->montant,
            'methode_remboursement' => 'required|in:virement,cheque,espece,autre',
            'reference_remboursement' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $paiement->update([
            'statut' => 'rembourse',
            'montant_rembourse' => $request->montant_rembourse,
            'methode_remboursement' => $request->methode_remboursement,
            'reference_remboursement' => $request->reference_remboursement,
            'notes' => $request->notes,
            'rembourse_par' => auth()->id(),
            'date_remboursement' => now(),
        ]);
        
        return response()->json([
            'message' => 'Paiement marqué comme remboursé avec succès',
            'data' => $paiement->fresh()
        ]);
    }
}
