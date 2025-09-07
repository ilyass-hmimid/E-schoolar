<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Matiere::query();
        
        // Filtrage par recherche
        if ($search = request('search')) {
            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        // Tri
        $sort = request('sort', 'nom');
        $direction = request('direction', 'asc');
        
        $validSorts = ['nom', 'created_at'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        }
        
        $matieres = $query->paginate(request('per_page', 15));
        
        return response()->json([
            'data' => $matieres->items(),
            'meta' => [
                'current_page' => $matieres->currentPage(),
                'last_page' => $matieres->lastPage(),
                'per_page' => $matieres->perPage(),
                'total' => $matieres->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:matieres',
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0.1|max:10',
            'niveau_id' => 'required|exists:niveaux,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $matiere = Matiere::create($request->all());

        return response()->json([
            'message' => 'Matière créée avec succès',
            'data' => $matiere
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Matiere $matiere)
    {
        return response()->json(['data' => $matiere]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matiere $matiere)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string|max:255|unique:matieres,nom,' . $matiere->id,
            'description' => 'nullable|string',
            'coefficient' => 'sometimes|required|numeric|min:0.1|max:10',
            'niveau_id' => 'sometimes|required|exists:niveaux,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $matiere->update($request->all());
        
        return response()->json([
            'message' => 'Matière mise à jour avec succès',
            'data' => $matiere->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matiere $matiere)
    {
        // Vérifier si la matière est utilisée avant de supprimer
        if ($matiere->cours()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer cette matière car elle est utilisée dans des cours'
            ], 422);
        }
        
        // Détacher les professeurs de cette matière
        $matiere->professeurs()->detach();
        
        $matiere->delete();
        
        return response()->json([
            'message' => 'Matière supprimée avec succès'
        ]);
    }
}
