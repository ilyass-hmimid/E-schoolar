<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::where('role', 'eleve')
            ->with(['niveau:id,nom']);
            
        // Filtrage par recherche
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cne', 'like', "%{$search}%");
            });
        }
        
        // Tri
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        
        $validSorts = ['name', 'email', 'cne', 'status', 'niveau_id'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        }
        
        $eleves = $query->paginate(request('per_page', 15));
        
        return response()->json([
            'data' => $eleves->items(),
            'meta' => [
                'current_page' => $eleves->currentPage(),
                'last_page' => $eleves->lastPage(),
                'per_page' => $eleves->perPage(),
                'total' => $eleves->total(),
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
            'cne' => 'required|string|max:255|unique:users',
            'niveau_id' => 'required|exists:niveaux,id',
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

        $eleve = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cne' => $request->cne,
            'niveau_id' => $request->niveau_id,
            'date_naissance' => $request->date_naissance,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
            'role' => 'eleve',
            'status' => 'actif',
        ]);

        return response()->json([
            'message' => 'Élève créé avec succès',
            'data' => $eleve
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $eleve)
    {
        if ($eleve->role !== 'eleve') {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $eleve->load('niveau:id,nom');
        return response()->json(['data' => $eleve]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $eleve)
    {
        if ($eleve->role !== 'eleve') {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $eleve->id,
            'cne' => 'sometimes|required|string|max:255|unique:users,cne,' . $eleve->id,
            'niveau_id' => 'sometimes|required|exists:niveaux,id',
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

        $updateData = $request->except(['password', 'password_confirmation']);
        
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
        
        $eleve->update($updateData);
        
        return response()->json([
            'message' => 'Élève mis à jour avec succès',
            'data' => $eleve->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $eleve)
    {
        if ($eleve->role !== 'eleve') {
            return response()->json([
                'message' => 'Ressource non trouvée'
            ], 404);
        }
        
        $eleve->delete();
        
        return response()->json([
            'message' => 'Élève supprimé avec succès'
        ]);
    }
}
