<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\User;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Absence::with(['eleve', 'matiere', 'professeur']);
        
        // Filtrage par élève
        if ($eleveId = request('eleve_id')) {
            $query->where('eleve_id', $eleveId);
        }
        
        // Filtrage par matière
        if ($matiereId = request('matiere_id')) {
            $query->where('matiere_id', $matiereId);
        }
        
        // Filtrage par professeur
        if ($professeurId = request('professeur_id')) {
            $query->where('professeur_id', $professeurId);
        }
        
        // Filtrage par date
        if ($dateDebut = request('date_debut')) {
            $query->whereDate('date_absence', '>=', $dateDebut);
        }
        
        if ($dateFin = request('date_fin')) {
            $query->whereDate('date_absence', '<=', $dateFin);
        }
        
        // Filtrage par statut
        if ($statut = request('statut')) {
            $query->where('statut', $statut);
        }
        
        // Tri
        $sort = request('sort', 'date_absence');
        $direction = request('direction', 'desc');
        
        $validSorts = ['date_absence', 'created_at', 'heures_manquees'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        }
        
        $absences = $query->paginate(request('per_page', 15));
        
        return response()->json([
            'data' => $absences->items(),
            'meta' => [
                'current_page' => $absences->currentPage(),
                'last_page' => $absences->lastPage(),
                'per_page' => $absences->perPage(),
                'total' => $absences->total(),
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
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'date_absence' => 'required|date',
            'heures_manquees' => 'required|numeric|min:0.5|max:8',
            'motif' => 'nullable|string|max:1000',
            'justificatif' => 'nullable|string',
            'statut' => 'required|in:non_justifiee,en_attente,justifiee',
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
        
        // Vérifier que le professeur est bien un professeur
        $professeur = User::findOrFail($request->professeur_id);
        if ($professeur->role !== 'professeur') {
            return response()->json([
                'message' => 'L\'utilisateur spécifié n\'est pas un professeur'
            ], 422);
        }
        
        // Vérifier que la matière existe
        Matiere::findOrFail($request->matiere_id);

        $absence = Absence::create($request->all());
        
        // Charger les relations pour la réponse
        $absence->load(['eleve', 'matiere', 'professeur']);

        return response()->json([
            'message' => 'Absence enregistrée avec succès',
            'data' => $absence
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        $absence->load(['eleve', 'matiere', 'professeur']);
        return response()->json(['data' => $absence]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Absence $absence)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'sometimes|required|exists:users,id',
            'matiere_id' => 'sometimes|required|exists:matieres,id',
            'professeur_id' => 'sometimes|required|exists:users,id',
            'date_absence' => 'sometimes|required|date',
            'heures_manquees' => 'sometimes|required|numeric|min:0.5|max:8',
            'motif' => 'nullable|string|max:1000',
            'justificatif' => 'nullable|string',
            'statut' => 'sometimes|required|in:non_justifiee,en_attente,justifiee',
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
        
        if ($request->has('professeur_id')) {
            $professeur = User::findOrFail($request->professeur_id);
            if ($professeur->role !== 'professeur') {
                return response()->json([
                    'message' => 'L\'utilisateur spécifié n\'est pas un professeur'
                ], 422);
            }
        }
        
        // Mise à jour de l'absence
        $absence->update($request->all());
        
        // Recharger les relations pour la réponse
        $absence->load(['eleve', 'matiere', 'professeur']);
        
        return response()->json([
            'message' => 'Absence mise à jour avec succès',
            'data' => $absence
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        $absence->delete();
        
        return response()->json([
            'message' => 'Absence supprimée avec succès'
        ]);
    }
    
    /**
     * Justifier une absence
     */
    public function justifier(Request $request, Absence $absence)
    {
        $validator = Validator::make($request->all(), [
            'justificatif' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $absence->update([
            'justificatif' => $request->justificatif,
            'statut' => 'en_attente',
            'date_justification' => now(),
        ]);
        
        return response()->json([
            'message' => 'Absence justifiée avec succès',
            'data' => $absence->fresh()
        ]);
    }
    
    /**
     * Valider ou rejeter une justification d'absence
     */
    public function traiterJustification(Request $request, Absence $absence)
    {
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:justifiee,non_justifiee',
            'commentaire' => 'required_if:statut,non_justifiee|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Vérifier que l'utilisateur est un administrateur ou le professeur concerné
        $user = auth()->user();
        if (!$user->is_admin && $user->id !== $absence->professeur_id) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }
        
        $updateData = [
            'statut' => $request->statut,
            'traite_par' => $user->id,
            'date_traitement' => now(),
        ];
        
        if ($request->statut === 'non_justifiee' && $request->has('commentaire')) {
            $updateData['commentaire_refus'] = $request->commentaire;
        }
        
        $absence->update($updateData);
        
        return response()->json([
            'message' => 'Justification traitée avec succès',
            'data' => $absence->fresh()
        ]);
    }
}
