<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfesseurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::where('role', 'professeur')
            ->with(['matieres:id,nom']);
            
        // Filtrage par recherche
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Tri
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        
        $validSorts = ['name', 'email', 'status'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        }
        
        $professeurs = $query->paginate(request('per_page', 15));
        
        return response()->json([
            'data' => $professeurs->items(),
            'meta' => [
                'current_page' => $professeurs->currentPage(),
                'last_page' => $professeurs->lastPage(),
                'per_page' => $professeurs->perPage(),
                'total' => $professeurs->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'matieres' => 'required|array',
            'matieres.*' => 'exists:matieres,id',
            'date_naissance' => 'required|date',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $professeur = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'date_naissance' => $request->date_naissance,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
            'role' => 'professeur',
            'status' => 'actif',
        ]);

        // Attacher les matières au professeur
        if (!empty($request->matieres)) {
            $professeur->matieres()->sync($request->matieres);
        }

        return response()->json([
            'message' => 'Professeur créé avec succès',
            'data' => $professeur->load('matieres')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $professeur)
    {
        if ($professeur->role !== 'professeur') {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $professeur->load('matieres:id,nom');
        return response()->json(['data' => $professeur]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $professeur)
    {
        if ($professeur->role !== 'professeur') {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $professeur->id,
            'matieres' => 'sometimes|array',
            'matieres.*' => 'exists:matieres,id',
            'date_naissance' => 'sometimes|required|date',
            'adresse' => 'sometimes|required|string|max:255',
            'telephone' => 'sometimes|required|string|max:20',
            'status' => 'sometimes|required|in:actif,inactif,suspendu',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->except(['password', 'password_confirmation', 'matieres']);
        
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
        
        $professeur->update($updateData);
        
        // Mettre à jour les matières du professeur si fournies
        if ($request->has('matieres')) {
            $professeur->matieres()->sync($request->matieres);
        }
        
        return response()->json([
            'message' => 'Professeur mis à jour avec succès',
            'data' => $professeur->fresh()->load('matieres')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $professeur)
    {
        if ($professeur->role !== 'professeur') {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        // Détacher toutes les matières avant de supprimer
        $professeur->matieres()->detach();
        
        $professeur->delete();
        
        return response()->json([
            'message' => 'Professeur supprimé avec succès'
        ]);
    }
}
