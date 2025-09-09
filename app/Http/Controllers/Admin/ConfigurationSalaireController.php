<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConfigurationSalaireController extends Controller
{
    /**
     * Affiche la page de configuration des salaires
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer toutes les matières avec leurs prix
        $matieres = Matiere::orderBy('nom')->get();
        
        // Récupérer tous les professeurs avec leurs matières enseignées et leurs pourcentages
        $professeurs = User::where('role', 'professeur')
            ->with(['matieresEnseignees' => function($query) {
                $query->withPivot(['pourcentage_remuneration']);
            }])
            ->orderBy('name')
            ->orderBy('prenom')
            ->get();
            
        return view('admin.configuration.salaires.index', compact('matieres', 'professeurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Met à jour le prix d'une matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMatierePrix(Request $request, int $matiereId)
    {
        $validator = Validator::make($request->all(), [
            'prix_mensuel' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $matiere = Matiere::findOrFail($matiereId);
        $matiere->prix_mensuel = $request->prix_mensuel;
        $matiere->save();

        // Convertir en float et formater le prix
        $prix = (float) $matiere->prix_mensuel;
        
        return response()->json([
            'success' => true,
            'message' => 'Prix mis à jour avec succès',
            'prix_formate' => number_format($prix, 2, ',', ' ') . ' DH'
        ]);
    }
    
    /**
     * Met à jour le pourcentage de rémunération d'un professeur pour une matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfesseurPourcentage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'professeur_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'pourcentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mettre à jour ou créer l'entrée dans la table d'association
        DB::table('enseignements')
            ->updateOrInsert(
                [
                    'professeur_id' => $request->professeur_id,
                    'matiere_id' => $request->matiere_id,
                ],
                [
                    'pourcentage_remuneration' => $request->pourcentage,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'Pourcentage mis à jour avec succès',
            'pourcentage' => $request->pourcentage
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
