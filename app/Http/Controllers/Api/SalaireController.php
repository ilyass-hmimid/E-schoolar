<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CalculSalaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaireController extends Controller
{
    protected $calculSalaireService;

    public function __construct(CalculSalaireService $calculSalaireService)
    {
        $this->calculSalaireService = $calculSalaireService;
        $this->middleware('auth:api');
        $this->middleware('role:admin|assistant', ['except' => ['mesSalaires']]);
    }

    /**
     * Calcule le salaire d'un professeur pour une matière et un mois donnés
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculerSalaireMatiere(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'professeur_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'mois_periode' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->calculSalaireService->calculerSalaireMatiere(
            $request->professeur_id,
            $request->matiere_id,
            $request->mois_periode
        );

        return response()->json($result);
    }

    /**
     * Calcule les salaires de tous les professeurs pour un mois donné
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculerTousLesSalaires(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mois_periode' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->calculSalaireService->calculerTousLesSalaires(
            $request->mois_periode
        );

        return response()->json($result);
    }

    /**
     * Liste tous les salaires pour une période donnée
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listerSalaires(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mois_periode' => 'nullable|date_format:Y-m',
            'statut' => 'nullable|in:en_attente,paye,annule',
            'professeur_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = \App\Models\Salaire::with(['professeur:id,name', 'matiere:id,nom']);

        if ($request->has('mois_periode')) {
            $query->where('mois_periode', $request->mois_periode);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }

        $salaires = $query->orderBy('mois_periode', 'desc')
                         ->orderBy('professeur_id')
                         ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $salaires
        ]);
    }

    /**
     * Récupère les salaires de l'utilisateur connecté (pour les professeurs)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mesSalaires(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'mois_periode' => 'nullable|date_format:Y-m',
            'statut' => 'nullable|in:en_attente,paye,annule',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $user->salaires()
                     ->with(['matiere:id,nom'])
                     ->orderBy('mois_periode', 'desc');

        if ($request->has('mois_periode')) {
            $query->where('mois_periode', $request->mois_periode);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $salaires = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $salaires
        ]);
    }

    /**
     * Valide et paie un salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function validerPaiement(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date_paiement' => 'nullable|date_format:Y-m-d',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->calculSalaireService->validerPaiementSalaire(
            $id,
            $request->date_paiement,
            $request->commentaires
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }
}
