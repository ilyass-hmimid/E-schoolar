<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salaire;
use App\Models\ConfigurationSalaire;
use App\Services\GestionSalaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaireApiController extends Controller
{
    protected $gestionSalaireService;

    public function __construct(GestionSalaireService $gestionSalaireService)
    {
        $this->gestionSalaireService = $gestionSalaireService;
        $this->middleware('auth:api');
        $this->middleware('role:admin');
    }

    /**
     * Affiche la liste des salaires avec pagination
     */
    public function index(Request $request)
    {
        $query = Salaire::with(['professeur', 'matiere']);

        // Filtres
        if ($request->has('mois_periode')) {
            $query->where('mois_periode', $request->mois_periode);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }

        if ($request->has('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        $perPage = $request->get('per_page', 15);
        $salaires = $query->orderBy('mois_periode', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $salaires
        ]);
    }

    /**
     * Affiche les détails d'un salaire
     */
    public function show($id)
    {
        $salaire = Salaire::with(['professeur', 'matiere'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $salaire
        ]);
    }

    /**
     * Calcule les salaires pour un mois donné
     */
    public function calculerSalaires(Request $request)
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

        $resultat = $this->gestionSalaireService->calculerSalaires($request->mois_periode);
        
        $status = $resultat['success'] ? 200 : 400;
        return response()->json($resultat, $status);
    }

    /**
     * Valide le paiement d'un salaire
     */
    public function validerPaiement(Request $request, $id)
    {
        $salaire = Salaire::findOrFail($id);
        
        $resultat = $this->gestionSalaireService->validerPaiement(
            $salaire, 
            $request->date_paiement,
            $request->commentaires
        );
        
        $status = $resultat['success'] ? 200 : 400;
        return response()->json($resultat, $status);
    }

    /**
     * Annule un salaire
     */
    public function annulerSalaire(Request $request, $id)
    {
        $salaire = Salaire::findOrFail($id);
        
        $resultat = $this->gestionSalaireService->annulerSalaire(
            $salaire, 
            $request->raison
        );
        
        $status = $resultat['success'] ? 200 : 400;
        return response()->json($resultat, $status);
    }

    /**
     * Liste des configurations de salaires
     */
    public function configurations(Request $request)
    {
        $query = ConfigurationSalaire::with('matiere');
        
        if ($request->has('est_actif')) {
            $query->where('est_actif', $request->est_actif);
        }
        
        $perPage = $request->get('per_page', 15);
        $configurations = $query->orderBy('matiere_id')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $configurations
        ]);
    }

    /**
     * Met à jour une configuration de salaire
     */
    public function updateConfiguration(Request $request, $id)
    {
        $configuration = ConfigurationSalaire::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'prix_unitaire' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|between:0,100',
            'prix_min' => 'nullable|numeric|min:0',
            'prix_max' => 'nullable|numeric|min:0|gt:prix_min',
            'est_actif' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $configuration->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Configuration mise à jour avec succès',
                'data' => $configuration->load('matiere')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
