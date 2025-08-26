<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfigurationSalaire;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfigurationSalaireController extends Controller
{
    /**
     * Affiche la liste des configurations de salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = ConfigurationSalaire::with(['matiere']);
        
        // Filtrer par statut actif/inactif
        if ($request->has('est_actif')) {
            $query->where('est_actif', $request->boolean('est_actif'));
        }
        
        // Filtrer par matière
        if ($request->has('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        $configurations = $query->orderBy('matiere_id')->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $configurations
        ]);
    }

    /**
     * Affiche une configuration de salaire spécifique
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $configuration = ConfigurationSalaire::with(['matiere'])->find($id);
        
        if (!$configuration) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration non trouvée.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $configuration
        ]);
    }

    /**
     * Crée une nouvelle configuration de salaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matiere_id' => 'required|exists:matieres,id|unique:configuration_salaires,matiere_id',
            'prix_unitaire' => 'required|numeric|min:0',
            'commission_prof' => 'required|numeric|min:0|max:100',
            'prix_min' => 'nullable|numeric|min:0',
            'prix_max' => 'nullable|numeric|min:' . ($request->prix_min ?? 0),
            'est_actif' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $configuration = ConfigurationSalaire::create([
                'matiere_id' => $request->matiere_id,
                'prix_unitaire' => $request->prix_unitaire,
                'commission_prof' => $request->commission_prof,
                'prix_min' => $request->prix_min,
                'prix_max' => $request->prix_max,
                'est_actif' => $request->boolean('est_actif', true),
                'description' => $request->description,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Configuration créée avec succès.',
                'data' => $configuration->load('matiere')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la configuration de salaire: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création de la configuration.'
            ], 500);
        }
    }

    /**
     * Met à jour une configuration de salaire existante
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $configuration = ConfigurationSalaire::find($id);
        
        if (!$configuration) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration non trouvée.'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'matiere_id' => 'sometimes|required|exists:matieres,id|unique:configuration_salaires,matiere_id,' . $id,
            'prix_unitaire' => 'sometimes|required|numeric|min:0',
            'commission_prof' => 'sometimes|required|numeric|min:0|max:100',
            'prix_min' => 'nullable|numeric|min:0',
            'prix_max' => 'nullable|numeric|min:' . ($request->prix_min ?? 0),
            'est_actif' => 'sometimes|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $configuration->update([
                'matiere_id' => $request->has('matiere_id') ? $request->matiere_id : $configuration->matiere_id,
                'prix_unitaire' => $request->has('prix_unitaire') ? $request->prix_unitaire : $configuration->prix_unitaire,
                'commission_prof' => $request->has('commission_prof') ? $request->commission_prof : $configuration->commission_prof,
                'prix_min' => $request->has('prix_min') ? $request->prix_min : $configuration->prix_min,
                'prix_max' => $request->has('prix_max') ? $request->prix_max : $configuration->prix_max,
                'est_actif' => $request->has('est_actif') ? $request->boolean('est_actif') : $configuration->est_actif,
                'description' => $request->has('description') ? $request->description : $configuration->description,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Configuration mise à jour avec succès.',
                'data' => $configuration->load('matiere')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la configuration de salaire: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la configuration.'
            ], 500);
        }
    }

    /**
     * Supprime une configuration de salaire
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $configuration = ConfigurationSalaire::find($id);
        
        if (!$configuration) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration non trouvée.'
            ], 404);
        }
        
        try {
            $configuration->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Configuration supprimée avec succès.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la configuration de salaire: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression de la configuration.'
            ], 500);
        }
    }
    
    /**
     * Récupère la liste des matières non configurées
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matieresNonConfigurees()
    {
        $matieresConfigurees = ConfigurationSalaire::pluck('matiere_id');
        
        $matieresNonConfigurees = Matiere::whereNotIn('id', $matieresConfigurees)
            ->select('id', 'nom', 'description')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $matieresNonConfigurees
        ]);
    }
}
