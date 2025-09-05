<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AbsenceResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences avec filtres
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Absence::with(['eleve', 'matiere', 'professeur']);

        // Filtre par élève
        if ($request->has('eleve_id')) {
            $query->where('eleve_id', $request->eleve_id);
        }

        // Filtre par professeur
        if ($request->has('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }

        // Filtre par matière
        if ($request->has('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        // Filtre par plage de dates
        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('date_absence', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Filtre par type d'absence
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Pour les élèves, ne montrer que leurs propres absences
        if ($user->hasRole('eleve')) {
            $query->where('eleve_id', $user->id);
        }

        // Pour les professeurs, ne montrer que les absences de leurs classes
        if ($user->hasRole('professeur')) {
            $query->where('professeur_id', $user->id);
        }

        // Pour les parents, montrer les absences de leurs enfants
        if ($user->hasRole('parent')) {
            $childrenIds = $user->enfants()->pluck('id');
            $query->whereIn('eleve_id', $childrenIds);
        }

        // Trier par date d'absence (plus récent en premier)
        $absences = $query->orderBy('date_absence', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->per_page ?? 15);

        return AbsenceResource::collection($absences);
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'date_absence' => 'required|date',
            'type' => ['required', Rule::in(['absence', 'retard', 'sortie_anticipée'])],
            'justifiee' => 'boolean',
            'motif' => 'nullable|string|max:500',
        ]);

        // Set the professor as the current user
        $validated['professeur_id'] = $request->user()->id;
        
        // Set default status
        $validated['statut_justification'] = Absence::STATUT_EN_ATTENTE;
        $validated['justifiee'] = false;

        try {
            DB::beginTransaction();
            
            $absence = Absence::create($validated);
            
            // Notify the student and parents
            $this->notifyAboutNewAbsence($absence);
            
            DB::commit();
            
            return new AbsenceResource($absence->load(['etudiant', 'matiere', 'professeur', 'cours']));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating absence: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create absence record'], 500);
        }
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show(Absence $absence)
    {
        // Vérifier que l'utilisateur a le droit de voir cette absence
        $user = request()->user();
        
        if ($user->hasRole('eleve') && $absence->eleve_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if ($user->hasRole('professeur') && $absence->professeur_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if ($user->hasRole('parent') && !$user->enfants()->where('id', $absence->eleve_id)->exists()) {
            abort(403, 'Accès non autorisé');
        }
        
        return new AbsenceResource($absence->load(['eleve', 'matiere', 'professeur']));
    }

    /**
     * Met à jour une absence existante
     */
    public function update(Request $request, Absence $absence)
    {
        $user = $request->user();
        
        // Vérifier les permissions
        if ($user->hasRole('eleve') && $absence->eleve_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if ($user->hasRole('professeur') && $absence->professeur_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = $request->validate([
            'justifiee' => 'sometimes|boolean',
            'motif' => 'nullable|string|max:500',
        ]);
        
        // Seuls les professeurs peuvent modifier le statut de justification
        if ($user->hasRole('professeur')) {
            if (isset($validated['justifiee'])) {
                $absence->justifiee = $validated['justifiee'];
            }
        }
        
        // Mise à jour du motif
        if (isset($validated['motif'])) {
            $absence->motif = $validated['motif'];
        }
        
        $absence->save();
        
        return new AbsenceResource($absence->load(['eleve', 'matiere', 'professeur']));
    }

    /**
     * Supprime une absence
     */
    public function destroy(Absence $absence)
    {
        $user = request()->user();
        
        // Seuls les administrateurs et les professeurs peuvent supprimer une absence
        if (!$user->hasRole(['admin', 'professeur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        // Un professeur ne peut supprimer que ses propres absences
        if ($user->hasRole('professeur') && $absence->professeur_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        $absence->delete();
        
        return response()->json(['message' => 'Absence supprimée avec succès']);
    }
    
    /**
     * Récupère les statistiques sur les absences
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        
        $query = Absence::query();
        
        // Appliquer les filtres en fonction du rôle de l'utilisateur
        if ($user->hasRole('eleve')) {
            $query->where('eleve_id', $user->id);
        } elseif ($user->hasRole('professeur')) {
            $query->where('professeur_id', $user->id);
        } elseif ($user->hasRole('parent')) {
            $childrenIds = $user->enfants()->pluck('id');
            $query->whereIn('eleve_id', $childrenIds);
        }
        
        // Filtrer par plage de dates (par défaut : mois en cours)
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
        $query->whereBetween('date_absence', [$startDate, $endDate]);
        
        // Nombre total d'absences
        $totalAbsences = (clone $query)->count();
        
        // Absences par type
        $absencesByType = (clone $query)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
            
        // Absences justifiées vs non justifiées
        $absencesByStatus = (clone $query)
            ->select('justifiee', DB::raw('count(*) as total'))
            ->groupBy('justifiee')
            ->pluck('total', 'justifiee')
            ->toArray();
            
        // Absences par jour
        $absencesByDay = (clone $query)
            ->select(DB::raw('DATE(date_absence) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        return response()->json([
            'total' => $totalAbsences,
            'par_type' => $absencesByType,
            'par_statut' => [
                'justifiees' => $absencesByStatus[1] ?? 0,
                'non_justifiees' => $absencesByStatus[0] ?? 0,
            ],
            'par_jour' => $absencesByDay,
            'periode' => [
                'debut' => $startDate,
                'fin' => $endDate,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }
    
}
