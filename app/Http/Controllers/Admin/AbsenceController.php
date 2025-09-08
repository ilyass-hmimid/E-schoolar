<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Absence;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Cours;
use App\Enums\RoleType;
use App\Events\AbsenceCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Contrôleur pour la gestion des absences
 * 
 * Ce contrôleur gère toutes les opérations liées aux absences dans l'administration.
 * Il supporte à la fois les réponses JSON pour les API et le rendu de vues (Blade/Inertia).
 */

class AbsenceController extends BaseAdminController
{
    /**
     * Validation rules for absence operations
     *
     * @var array
     */
    protected $validationRules = [
        'store' => [
            'eleve_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'motif' => 'nullable|string|max:1000',
            'commentaire' => 'nullable|string|max:1000',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'statut' => 'required|in:non_justifiee,en_attente,justifiee',
            'heures_manquees' => 'required|numeric|min:0.5|max:8',
        ],
        'update' => [
            'eleve_id' => 'sometimes|required|exists:users,id',
            'matiere_id' => 'sometimes|required|exists:matieres,id',
            'professeur_id' => 'sometimes|required|exists:users,id',
            'date_absence' => 'sometimes|required|date|before_or_equal:today',
            'heure_debut' => 'sometimes|required|date_format:H:i',
            'heure_fin' => 'sometimes|required|date_format:H:i|after:heure_debut',
            'motif' => 'nullable|string|max:1000',
            'commentaire' => 'nullable|string|max:1000',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'statut' => 'sometimes|required|in:non_justifiee,en_attente,justifiee',
            'heures_manquees' => 'sometimes|required|numeric|min:0.5|max:8',
        ]
    ];

    /**
     * Gestionnaire de réponse commun
     *
     * @param mixed $data Les données à passer à la vue
     * @param string $view Le chemin de la vue (Inertia) ou le nom (Blade)
     * @param array $additionalData Données supplémentaires pour les réponses JSON
     * @return mixed
     * @throws \RuntimeException Si la vue n'existe pas
     * @throws \Illuminate\Http\JsonResponse Pour les réponses JSON
     */
    protected function respond($data, $view, $additionalData = [])
    {
        // Réponses JSON
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => $additionalData
            ]);
        }

        // Vérifier si Inertia est disponible
        $inertiaClass = 'Inertia\\Inertia';
        $responseClass = 'Inertia\\Response';
        
        if (class_exists($inertiaClass) && class_exists($responseClass)) {
            try {
                return $inertiaClass::render($view, $data);
            } catch (\Exception $e) {
                // En cas d'erreur avec Inertia, on bascule sur Blade
                if (app()->bound('log')) {
                    app('log')->warning("Échec du rendu Inertia: " . $e->getMessage());
                }
            }
        }
        
        // Fallback sur les vues Blade
        $bladeView = $this->resolveBladeView($view);
        
        if (!view()->exists($bladeView)) {
            throw new \RuntimeException("La vue [{$bladeView}] est introuvable.");
        }
        
        return view($bladeView, $data);
    }
    /**
     * Gère les réponses d'erreur de manière centralisée
     *
     * @param string $message
     * @param bool $isJson
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function errorResponse($message, $isJson = false)
    {
        if ($isJson) {
            return response()->json(['message' => $message], 422);
        }
        return back()->with('error', $message)->withInput();
    }

    /**
     * Convertit un chemin de vue Inertia en chemin de vue Blade
     *
     * @param string $view
     * @return string
     */
    protected function resolveBladeView($view)
    {
        // Convertit les chemins Inertia en notation point pour Blade
        $view = str_replace('/', '.', strtolower($view));
        
        // Supprime le préfixe 'admin.' s'il existe
        if (str_starts_with($view, 'admin.')) {
            $view = substr($view, 6);
        }
        
        return 'admin.' . $view;
    }

    /**
     * Affiche la liste des absences
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Absence::class);

        $query = Absence::with(['eleve:id,name,email', 'matiere:id,nom', 'professeur:id,name']);
        
        // Récupération des classes pour le filtre
        $classes = Classe::orderBy('nom')->get();
        
        // Apply role-based filtering
        $user = auth()->user();
        if ($user->role === RoleType::PROFESSEUR) {
            $query->where('professeur_id', $user->id);
        } elseif ($user->role === RoleType::ELEVE) {
            $query->where('eleve_id', $user->id);
        }
        
        // Apply filters
        $filters = $request->only(['search', 'eleve_id', 'professeur_id', 'matiere_id', 'statut', 'date_debut', 'date_fin']);
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('eleve', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if (!empty($filters['eleve_id'])) {
            $query->where('eleve_id', $filters['eleve_id']);
        }
        
        if (!empty($filters['professeur_id'])) {
            $query->where('professeur_id', $filters['professeur_id']);
        }
        
        if (!empty($filters['matiere_id'])) {
            $query->where('matiere_id', $filters['matiere_id']);
        }
        
        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }
        
        if (!empty($filters['date_debut'])) {
            $query->whereDate('date_absence', '>=', $filters['date_debut']);
        }
        
        if (!empty($filters['date_fin'])) {
            $query->whereDate('date_absence', '<=', $filters['date_fin']);
        }
        
        // Sorting
        $sort = $request->input('sort', 'date_absence');
        $direction = $request->input('direction', 'desc');
        $validSorts = ['date_absence', 'created_at', 'heures_manquees', 'statut'];
        
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest('date_absence');
        }
        
        // Pagination
        $perPage = $request->input('per_page', request()->wantsJson() ? 15 : 20);
        $absences = $query->paginate($perPage);
        
        // For API responses
        if ($request->wantsJson()) {
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
        
        // For web responses
        $eleves = User::where('role', RoleType::ELEVE)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
            
        $professeurs = User::where('role', RoleType::PROFESSEUR)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
            
        $matieres = Matiere::orderBy('nom')->get(['id', 'nom']);
        
        // Calculate statistics for web view using a separate query
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'justified' => $statsQuery->clone()->where('statut', 'justifiee')->count(),
            'pending' => $statsQuery->clone()->where('statut', 'en_attente')->count(),
            'unjustified' => $statsQuery->clone()->where('statut', 'non_justifiee')->count(),
        ];
        
        // Prepare response data
        $data = [
            'absences' => $absences,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'classes' => $classes, // Ajout de la variable classes
            'eleves' => $eleves,
            'professeurs' => $professeurs,
            'matieres' => $matieres,
            'stats' => $stats,
            'can' => [
                'create' => $user->can('create', Absence::class),
                'update' => $user->can('update', Absence::class),
                'delete' => $user->can('delete', Absence::class),
            ]
        ];
        
        return $this->respond($data, 'Admin/Absences/Index');
    }

    /**
     * Affiche le formulaire de création d'une nouvelle absence
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Absence::class);
        
        $eleves = User::where('role', RoleType::ELEVE)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
            
        $professeurs = User::where('role', RoleType::PROFESSEUR)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
            
        $matieres = Matiere::orderBy('nom')->get(['id', 'nom']);
        
        // Récupération des cours avec les relations nécessaires
        $cours = Cours::with(['matiere', 'professeur'])
            ->orderBy('created_at', 'desc') // Utilisation de created_at au lieu de date_debut
            ->get();
        
        $data = [
            'eleves' => $eleves,
            'professeurs' => $professeurs,
            'matieres' => $matieres,
            'cours' => $cours,
            'defaults' => [
                'date_absence' => now()->format('Y-m-d'),
                'heure_debut' => '08:00',
                'heure_fin' => '09:00',
                'heures_manquees' => 1,
            ]
        ];
        
        return $this->respond($data, 'Admin/Absences/Create');
}

    /**
     * Enregistre une nouvelle absence dans la base de données
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', Absence::class);
        
        // Validation de la requête
        $validated = $request->validate($this->validationRules['store']);
        
        // Gestion du téléchargement du fichier si présent
        if ($request->hasFile('justificatif')) {
            $path = $request->file('justificatif')->store('justificatifs', 'public');
            $validated['justificatif_path'] = $path;
    }
    
    // Calculate hours if not provided
    if (!isset($validated['heures_manquees'])) {
        $start = Carbon::parse($validated['heure_debut']);
        $end = Carbon::parse($validated['heure_fin']);
        $validated['heures_manquees'] = $end->diffInHours($start);
    }
    
        // Vérification de l'élève
        $eleve = User::with('matieres')->findOrFail($validated['eleve_id']);
        if ($eleve->role !== RoleType::ELEVE || $eleve->status !== 'actif') {
            return $this->errorResponse(
                'Seuls les élèves actifs peuvent être marqués absents.',
                $request->wantsJson()
            );
        }
        
        // Vérification du professeur
        $professeur = User::findOrFail($validated['professeur_id']);
        if ($professeur->role !== RoleType::PROFESSEUR || $professeur->status !== 'actif') {
            return $this->errorResponse(
                'Seuls les professeurs actifs peuvent enregistrer des absences.',
                $request->wantsJson()
            );
        }
        
        // Vérification de la matière et de l'inscription de l'élève
        $matiere = Matiere::findOrFail($validated['matiere_id']);
        if (!$eleve->matieres->contains($matiere->id)) {
            return $this->errorResponse(
                'L\'élève n\'est pas inscrit à cette matière.',
                $request->wantsJson()
            );
        }
    
        try {
            DB::beginTransaction();
            
            $absence = Absence::create($validated);
            
            // Événement de création d'absence (pour notifications, etc.)
            event(new AbsenceCreated($absence));
            
            DB::commit();
            
            // Réponse API
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Absence enregistrée avec succès',
                    'data' => $absence->load(['eleve', 'matiere', 'professeur'])
                ], 201);
            }
            
            // Réponse Web
            return redirect()->route('admin.absences.index')
                ->with('success', 'Absence enregistrée avec succès');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de l\'absence: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'enregistrement de l\'absence.'
                ], 500);
            }
            
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'absence.')
                ->withInput();
        }
    }

    /**
     * Display the specified absence.
     *
{{ ... }}
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show(Absence $absence)
    {
        $this->authorize('view', $absence);
        
        // Load relationships
        $absence->load([
            'eleve:id,name,email', 
            'matiere:id,nom', 
            'professeur:id,name',
            'enregistrePar:id,name'
        ]);
        
        // Handle API response
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $absence
            ]);
        }
        
        // Handle web response
        return $this->respond($absence, 'Admin/Absences/Show', [
            'can' => [
                'update' => auth()->user()->can('update', $absence),
                'delete' => auth()->user()->can('delete', $absence)
            ]
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une absence
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\View\View
     */
    /**
     * Show the form for editing the specified absence.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(Absence $absence)
    {
        $this->authorize('update', $absence);
        
        // Load relationships
        $absence->load(['eleve', 'matiere', 'professeur']);
        
        // Get active students, teachers, and subjects for the form
        $eleves = User::where('role', RoleType::ELEVE)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
            
        $professeurs = User::where('role', RoleType::PROFESSEUR)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
            
        $matieres = Matiere::orderBy('nom')->get(['id', 'nom']);
        
        // Prepare data for the view
        $data = [
            'absence' => $absence,
            'eleves' => $eleves,
            'professeurs' => $professeurs,
            'matieres' => $matieres,
            'statuts' => [
                'non_justifiee' => 'Non justifiée',
                'justifiee' => 'Justifiée',
                'en_attente' => 'En attente de validation'
            ]
        ];
        
        // Handle API response
        if (request()->wantsJson()) {
            return response()->json(['data' => $data]);
        }
        
        // Handle web response
        return $this->respond($data, 'Admin/Absences/Edit');
    }

    /**
     * Met à jour une absence
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Update the specified absence in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Absence $absence)
    {
        $this->authorize('update', $absence);
        
        // Define validation rules
        $rules = [
            'eleve_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:users,id',
            'date_absence' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'motif' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string',
            'statut' => 'required|in:non_justifiee,justifiee,en_attente',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
        
        // Validate the request
        $validated = $request->validate($rules);
        
        // Handle file upload if present
        if ($request->hasFile('justificatif')) {
            // Delete old file if exists
            if ($absence->justificatif_path) {
                Storage::disk('public')->delete($absence->justificatif_path);
            }
            
            // Store new file
            $path = $request->file('justificatif')->store('justificatifs', 'public');
            $validated['justificatif_path'] = $path;
            
            // Update status if a justificatif is uploaded
            if ($validated['statut'] === 'non_justifiee') {
                $validated['statut'] = 'en_attente';
            }
        }
        
        // Calculate duration in minutes
        $debut = Carbon::parse($validated['date_absence'] . ' ' . $validated['heure_debut']);
        $fin = Carbon::parse($validated['date_absence'] . ' ' . $validated['heure_fin']);
        $validated['duree_minutes'] = $fin->diffInMinutes($debut);
        
        // Check if student is active
        $eleve = User::with('matieres')->findOrFail($validated['eleve_id']);
        if ($eleve->role !== RoleType::ELEVE || $eleve->status !== 'actif') {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Seuls les élèves actifs peuvent être marqués absents.'
                ], 422);
            }
            return back()->with('error', 'Seuls les élèves actifs peuvent être marqués absents.');
        }
        
        // Check if teacher is active
        $professeur = User::findOrFail($validated['professeur_id']);
        if ($professeur->role !== RoleType::PROFESSEUR || $professeur->status !== 'actif') {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Seuls les professeurs actifs peuvent enregistrer des absences.'
                ], 422);
            }
            return back()->with('error', 'Seuls les professeurs actifs peuvent enregistrer des absences.');
        }
        
        // Check if subject exists and student is enrolled
        $matiere = Matiere::findOrFail($validated['matiere_id']);
        if (!$eleve->matieres->contains($matiere->id)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'L\'élève n\'est pas inscrit à cette matière.'
                ], 422);
            }
            return back()->with('error', 'L\'élève n\'est pas inscrit à cette matière.');
        }
        
        // Update the absence
        $absence->update($validated);
        
        // Handle API response
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Absence mise à jour avec succès',
                'data' => $absence->load(['eleve', 'matiere', 'professeur'])
            ]);
        }
        
        // Handle web response
        $redirectRoute = $request->input('_redirect', route('admin.absences.show', $absence));
        return redirect($redirectRoute)
            ->with('success', 'Absence mise à jour avec succès');
    }

    /**
     * Justifie une absence
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Mark an absence as justified with an optional file upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function justifier(Request $request, Absence $absence)
    {
        $this->authorize('update', $absence);
        
        $validated = $request->validate([
            'justificatif' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        // Handle file upload
        if ($request->hasFile('justificatif')) {
            // Delete old file if exists
            if ($absence->justificatif_path) {
                Storage::disk('public')->delete($absence->justificatif_path);
            }
            
            // Store new file
            $path = $request->file('justificatif')->store('justificatifs', 'public');
            
            // Update absence
            $absence->update([
                'statut' => 'justifiee',
                'justificatif_path' => $path,
                'commentaire' => $validated['commentaire'] ?? $absence->commentaire,
                'justifie_le' => now(),
                'justifie_par' => auth()->id(),
            ]);
            
            // Handle API response
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Absence justifiée avec succès',
                    'data' => $absence->load(['eleve', 'matiere', 'professeur'])
                ]);
            }
            
            // Handle web response
            return redirect()
                ->route('admin.absences.show', $absence)
                ->with('success', 'Absence justifiée avec succès');
        }
        
        // If no file was uploaded but the method was called
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Aucun justificatif fourni',
            ], 422);
        }
        
        return back()->with('error', 'Aucun justificatif fourni');
    }

    /**
     * Marque une absence comme non justifiée
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Mark an absence as unjustified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function nonJustifier(Request $request, Absence $absence)
    {
        $this->authorize('update', $absence);
        
        // If already marked as non-justified
        if ($absence->statut === 'non_justifiee') {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Cette absence est déjà marquée comme non justifiée',
                ], 422);
            }
            return back()->with('info', 'Cette absence est déjà marquée comme non justifiée.');
        }
        
        // Update the absence status
        $absence->update([
            'statut' => 'non_justifiee',
            'justifie_le' => null,
            'justifie_par' => null,
            'traite_par' => auth()->id(),
            'date_traitement' => now(),
        ]);
        
        // Handle API response
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Absence marquée comme non justifiée avec succès',
                'data' => $absence->load(['eleve', 'matiere', 'professeur'])
            ]);
        }
        
        // Handle web response
        return back()->with('success', 'L\'absence a été marquée comme non justifiée.');
    }

    /**
     * Exporte les absences au format CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $query = $this->getFilteredAbsences($request);
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=absences_' . now()->format('Y-m-d') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($query) {
            $handle = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($handle, [
                'ID',
                'Élève',
                'Classe',
                'Matière',
                'Professeur',
                'Date absence',
                'Heure début',
                'Heure fin',
                'Heures manquées',
                'Statut',
                'Motif',
                'Date de création'
            ]);

            // Données
            $query->chunk(200, function($absences) use ($handle) {
                foreach ($absences as $absence) {
                    fputcsv($handle, [
                        $absence->id,
                        $absence->eleve->name,
                        $absence->eleve->classe->nom ?? 'N/A',
                        $absence->matiere->nom,
                        $absence->professeur->name,
                        $absence->date_absence->format('d/m/Y'),
                        $absence->heure_debut,
                        $absence->heure_fin,
                        $absence->heures_manquees,
                        $this->getStatusBadge($absence->statut),
                        $absence->motif,
                        $absence->created_at->format('d/m/Y H:i')
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Filtre les absences selon les critères de recherche
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getFilteredAbsences(Request $request)
    {
        $query = Absence::with(['eleve', 'matiere', 'professeur', 'eleve.classe'])
            ->orderBy('date_absence', 'desc')
            ->orderBy('heure_debut', 'desc');

        // Filtre par élève
        if ($request->filled('eleve_id')) {
            $query->where('eleve_id', $request->eleve_id);
        }

        // Filtre par matière
        if ($request->filled('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        // Filtre par professeur
        if ($request->filled('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }

        // Filtre par date de début
        if ($request->filled('date_debut')) {
            $query->where('date_absence', '>=', $request->date_debut);
        }

        // Filtre par date de fin
        if ($request->filled('date_fin')) {
            $query->where('date_absence', '<=', $request->date_fin);
        }

        // Filtre par statut
        if ($request->filled('statut') && $request->statut !== 'tous') {
            $query->where('statut', $request->statut);
        }

        // Filtre par motif
        if ($request->filled('motif')) {
            $query->where('motif', 'like', '%' . $request->motif . '%');
        }

        // Filtre par classe
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        return $query;
    }

    /**
     * Convertit le statut en libellé lisible
     *
     * @param  string  $status
     * @return string
     */
    protected function getStatusBadge($status)
    {
        $statuses = [
            'non_justifiee' => 'Non justifiée',
            'en_attente' => 'En attente',
            'justifiee' => 'Justifiée',
            'refusee' => 'Refusée',
            'validee' => 'Validée'
        ];
        
        return $statuses[$status] ?? $status;
    }
    
    /**
     * Supprime une absence
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Remove the specified absence from storage.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Absence $absence)
    {
        $this->authorize('delete', $absence);
        
        // Delete the justificatif file if it exists
        if ($absence->justificatif_path) {
            Storage::disk('public')->delete($absence->justificatif_path);
        }
        
        // Store the absence data for the response
        $absenceData = $absence->load(['eleve', 'matiere', 'professeur']);
        
        // Delete the absence
        $absence->delete();
        
        // Handle API response
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Absence supprimée avec succès',
                'data' => $absenceData
            ]);
        }
        
        // Handle web response
        return redirect()
            ->route('admin.absences.index')
            ->with('success', 'Absence supprimée avec succès');
    }
    
    /**
     * Affiche le rapport des absences
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    /**
     * Generate an absence report with filtering options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function rapport(Request $request)
    {
        $this->authorize('viewAny', Absence::class);
        
        // Get filter parameters
        $filters = [
            'eleve_id' => $request->input('eleve_id'),
            'matiere_id' => $request->input('matiere_id'),
            'professeur_id' => $request->input('professeur_id'),
            'statut' => $request->input('statut'),
            'date_debut' => $request->input('date_debut'),
            'date_fin' => $request->input('date_fin'),
            'classe_id' => $request->input('classe_id'),
        ];
        
        // Start building the query
        $query = Absence::with(['eleve', 'matiere', 'professeur']);
        
        // Apply filters
        if (!empty($filters['eleve_id'])) {
            $query->where('eleve_id', $filters['eleve_id']);
        }
        
        if (!empty($filters['matiere_id'])) {
            $query->where('matiere_id', $filters['matiere_id']);
        }
        
        if (!empty($filters['professeur_id'])) {
            $query->where('professeur_id', $filters['professeur_id']);
        }
        
        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }
        
        if (!empty($filters['date_debut'])) {
            $query->whereDate('date_absence', '>=', $filters['date_debut']);
        }
        
        if (!empty($filters['date_fin'])) {
            $query->whereDate('date_absence', '<=', $filters['date_fin']);
        }
        
        // Filter by class if specified
        if (!empty($filters['classe_id'])) {
            $query->whereHas('eleve', function($q) use ($filters) {
                $q->where('classe_id', $filters['classe_id']);
            });
        }
        
        // Get the results with pagination
        $absences = $query->orderBy('date_absence', 'desc')
                         ->orderBy('heure_debut', 'desc')
                         ->paginate(25);
        
        // Get filter options
        $eleves = User::where('role', RoleType::ELEVE)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name']);
            
        $professeurs = User::where('role', RoleType::PROFESSEUR)
            ->where('status', 'actif')
            ->orderBy('name')
            ->get(['id', 'name']);
            
        $matieres = Matiere::orderBy('nom')->get(['id', 'nom']);
        
        $classes = Classe::orderBy('nom')->get(['id', 'nom']);
        
        // Prepare data for the view
        $data = [
            'absences' => $absences,
            'eleves' => $eleves,
            'professeurs' => $professeurs,
            'matieres' => $matieres,
            'classes' => $classes,
            'filters' => $filters,
            'statuts' => [
                'non_justifiee' => 'Non justifiée',
                'justifiee' => 'Justifiée',
                'en_attente' => 'En attente de validation'
            ]
        ];
        
        // Handle API response
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $data
            ]);
        }
        
        // Handle web response
        return $this->respond($data, 'Admin/Absences/Rapport');
    }
}
