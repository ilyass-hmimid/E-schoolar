<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Enseignement;
use App\Enums\RoleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences avec filtres
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Absence::with(['etudiant:id,name,email', 'matiere:id,nom', 'professeur:id,name', 'assistant:id,name']);

        // Filtres selon le rôle
        if ($user->role === RoleType::PROFESSEUR) {
            $query->where('professeur_id', $user->id);
        } elseif ($user->role === RoleType::ASSISTANT) {
            $query->where('assistant_id', $user->id);
        } elseif ($user->role === RoleType::ELEVE) {
            $query->where('etudiant_id', $user->id);
        }

        // Filtres de recherche
        $filters = $request->only(['search', 'matiere_id', 'type', 'justifiee', 'date_debut', 'date_fin']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('etudiant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if (isset($filters['justifiee']) && $filters['justifiee'] !== '') {
            $query->where('justifiee', $filters['justifiee']);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_absence', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_absence', '<=', $request->date_fin);
        }

        $absences = $query->orderBy('date_absence', 'desc')->paginate(15);

        // Statistiques
        $stats = [
            'total_absences' => $query->count(),
            'absences_justifiees' => $query->where('justifiee', true)->count(),
            'absences_non_justifiees' => $query->where('justifiee', false)->count(),
            'retards' => $query->where('type', 'retard')->count(),
        ];

        return Inertia::render('Absences/Index', [
            'absences' => $absences,
            'stats' => $stats,
            'filters' => $filters,
            'matieres' => Matiere::actifs()->get(['id', 'nom']),
            'canCreate' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT, RoleType::PROFESSEUR]),
            'canJustify' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT]),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT, RoleType::PROFESSEUR])) {
            abort(403, 'Accès non autorisé');
        }

        $eleves = User::eleves()->actifs()->get(['id', 'name', 'email', 'niveau_id', 'filiere_id']);
        $matieres = Matiere::actifs()->get(['id', 'nom']);

        return Inertia::render('Absences/Create', [
            'eleves' => $eleves,
            'matieres' => $matieres,
            'types_absence' => [
                'absence' => 'Absence',
                'retard' => 'Retard',
                'sortie_anticipée' => 'Sortie anticipée',
            ],
        ]);
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT, RoleType::PROFESSEUR])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'date_absence' => 'required|date',
            'type' => 'required|in:absence,retard,sortie_anticipée',
            'duree_retard' => 'nullable|integer|min:1|max:480', // en minutes
            'motif' => 'nullable|string|max:500',
            'justifiee' => 'boolean',
            'justification' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $absence = Absence::create([
                'etudiant_id' => $validated['etudiant_id'],
                'matiere_id' => $validated['matiere_id'],
                'professeur_id' => $user->role === RoleType::PROFESSEUR ? $user->id : null,
                'assistant_id' => $user->role === RoleType::ASSISTANT ? $user->id : null,
                'date_absence' => $validated['date_absence'],
                'type' => $validated['type'],
                'duree_retard' => $validated['duree_retard'],
                'motif' => $validated['motif'],
                'justifiee' => $validated['justifiee'] ?? false,
                'justification' => $validated['justification'],
            ]);

            DB::commit();

            return redirect()->route('absences.index')
                ->with('success', 'Absence enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show(Absence $absence)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->role === RoleType::ELEVE && $absence->etudiant_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if ($user->role === RoleType::PROFESSEUR && $absence->professeur_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if ($user->role === RoleType::ASSISTANT && $absence->assistant_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $absence->load(['etudiant:id,name,email,phone', 'matiere:id,nom', 'professeur:id,name', 'assistant:id,name']);

        return Inertia::render('Absences/Show', [
            'absence' => $absence,
            'canEdit' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT]),
            'canJustify' => in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT]) && !$absence->justifiee,
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une absence
     */
    public function edit(Absence $absence)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $absence->load(['etudiant:id,name,email', 'matiere:id,nom']);

        return Inertia::render('Absences/Edit', [
            'absence' => $absence,
            'types_absence' => [
                'absence' => 'Absence',
                'retard' => 'Retard',
                'sortie_anticipée' => 'Sortie anticipée',
            ],
        ]);
    }

    /**
     * Met à jour une absence existante
     */
    public function update(Request $request, Absence $absence)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'type' => 'required|in:absence,retard,sortie_anticipée',
            'duree_retard' => 'nullable|integer|min:1|max:480',
            'motif' => 'nullable|string|max:500',
            'justifiee' => 'boolean',
            'justification' => 'nullable|string|max:1000',
        ]);

        $absence->update($validated);

        return redirect()->route('absences.show', $absence)
            ->with('success', 'Absence mise à jour avec succès.');
    }

    /**
     * Supprime une absence
     */
    public function destroy(Absence $absence)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $absence->delete();
        
        return redirect()->route('absences.index')
            ->with('success', 'Absence supprimée avec succès.');
    }

    /**
     * Marque une absence comme justifiée
     */
    public function justifier(Request $request, Absence $absence)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'justification' => 'required|string|max:1000',
        ]);

        $absence->update([
            'justifiee' => true,
            'justification' => $validated['justification'],
        ]);

        return back()->with('success', 'Absence marquée comme justifiée.');
    }

    /**
     * Marque une absence comme non justifiée
     */
    public function nonJustifier(Absence $absence)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }

        $absence->update([
            'justifiee' => false,
            'justification' => null,
        ]);

        return back()->with('success', 'Absence marquée comme non justifiée.');
    }

    /**
     * Récupère les statistiques d'absences pour un étudiant
     */
    public function statistiquesEtudiant(User $etudiant, Request $request)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->role === RoleType::ELEVE && $etudiant->id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $debut = $request->input('debut') ? Carbon::parse($request->input('debut')) : now()->startOfMonth();
        $fin = $request->input('fin') ? Carbon::parse($request->input('fin')) : now()->endOfMonth();
        
        $absences = Absence::where('etudiant_id', $etudiant->id)
            ->whereBetween('date_absence', [$debut, $fin])
            ->with(['matiere:id,nom'])
            ->get();

        $statistiques = [
            'total_absences' => $absences->where('type', 'absence')->count(),
            'total_retards' => $absences->where('type', 'retard')->count(),
            'absences_justifiees' => $absences->where('justifiee', true)->count(),
            'absences_non_justifiees' => $absences->where('justifiee', false)->count(),
            'total_heures_retard' => $absences->where('type', 'retard')->sum('duree_retard') / 60,
            'par_matiere' => $absences->groupBy('matiere.nom')->map(function ($group) {
                return [
                    'total' => $group->count(),
                    'justifiees' => $group->where('justifiee', true)->count(),
                    'non_justifiees' => $group->where('justifiee', false)->count(),
                ];
            }),
        ];
        
        return Inertia::render('Absences/StatistiquesEtudiant', [
            'etudiant' => [
                'id' => $etudiant->id,
                'name' => $etudiant->name,
                'email' => $etudiant->email,
                'niveau' => $etudiant->niveau ? $etudiant->niveau->nom : 'Non défini',
                'filiere' => $etudiant->filiere ? $etudiant->filiere->nom : 'Non défini',
            ],
            'statistiques' => $statistiques,
            'filtres' => [
                'debut' => $debut->format('Y-m-d'),
                'fin' => $fin->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Génère un rapport d'absences
     */
    public function genererRapport(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, [RoleType::ADMIN, RoleType::ASSISTANT])) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'matiere_id' => 'nullable|exists:matieres,id',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);
        
        $query = Absence::with(['etudiant:id,name,email', 'matiere:id,nom'])
            ->whereBetween('date_absence', [$validated['date_debut'], $validated['date_fin']]);

        if ($validated['matiere_id']) {
            $query->where('matiere_id', $validated['matiere_id']);
        }

        if ($validated['niveau_id']) {
            $query->whereHas('etudiant', function ($q) use ($validated) {
                $q->where('niveau_id', $validated['niveau_id']);
            });
        }

        if ($validated['filiere_id']) {
            $query->whereHas('etudiant', function ($q) use ($validated) {
                $q->where('filiere_id', $validated['filiere_id']);
            });
        }

        $absences = $query->get();

        $rapport = [
            'periode' => [
                'debut' => $validated['date_debut'],
                'fin' => $validated['date_fin'],
            ],
            'statistiques' => [
                'total_absences' => $absences->where('type', 'absence')->count(),
                'total_retards' => $absences->where('type', 'retard')->count(),
                'absences_justifiees' => $absences->where('justifiee', true)->count(),
                'absences_non_justifiees' => $absences->where('justifiee', false)->count(),
            ],
            'par_etudiant' => $absences->groupBy('etudiant.name')->map(function ($group) {
                return [
                    'total_absences' => $group->where('type', 'absence')->count(),
                    'total_retards' => $group->where('type', 'retard')->count(),
                    'absences_justifiees' => $group->where('justifiee', true)->count(),
                    'absences_non_justifiees' => $group->where('justifiee', false)->count(),
                ];
            }),
            'par_matiere' => $absences->groupBy('matiere.nom')->map(function ($group) {
                return [
                    'total_absences' => $group->where('type', 'absence')->count(),
                    'total_retards' => $group->where('type', 'retard')->count(),
                    'absences_justifiees' => $group->where('justifiee', true)->count(),
                    'absences_non_justifiees' => $group->where('justifiee', false)->count(),
                ];
            }),
        ];
        
        return response()->json($rapport);
    }
}
