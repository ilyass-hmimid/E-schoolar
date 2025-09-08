<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Traits\OptimizedQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class NoteController extends Controller
{
    use OptimizedQueries;
    
    /**
     * Nombre de minutes pour mettre en cache les requêtes
     *
     * @var int
     */
    protected $cacheTtl = 1440; // 24 heures
    
    /**
     * Nombre d'éléments par page pour la pagination
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * Affiche la liste des notes avec des requêtes optimisées
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'matiere', 'classe', 'date_debut', 'date_fin']);
        $cacheKey = 'notes_prof_' . Auth::id() . '_' . md5(json_encode($filters));
        
        // Récupérer les matières enseignées avec mise en cache
        $matieres = $this->cached('matieres_notes_prof_' . Auth::id(), function() {
            return Auth::user()->matieresEnseignees()
                ->select(['id', 'nom', 'code'])
                ->orderBy('nom')
                ->get()
                ->map(function($matiere) {
                    return [
                        'id' => $matiere->id,
                        'nom' => $matiere->nom,
                        'code' => $matiere->code
                    ];
                });
        }, $this->cacheTtl);
        
        // Récupérer les notes avec des requêtes optimisées
        $notes = $this->cached($cacheKey, function() use ($filters) {
            $query = Note::query()
                ->select([
                    'notes.id',
                    'notes.etudiant_id',
                    'notes.matiere_id',
                    'notes.type_note',
                    'notes.note as valeur',
                    'notes.coefficient',
                    'notes.date_evaluation',
                    'notes.commentaire',
                    'notes.created_at',
                    'notes.updated_at',
                    'etudiants.user_id',
                    'etudiants.classe_id',
                    'users.nom',
                    'users.prenom',
                    'matieres.nom as matiere_nom',
                    'matieres.code as matiere_code',
                    'classes.nom as classe_nom'
                ])
                ->join('etudiants', 'notes.etudiant_id', '=', 'etudiants.id')
                ->join('users', 'etudiants.user_id', '=', 'users.id')
                ->join('matieres', 'notes.matiere_id', '=', 'matieres.id')
                ->leftJoin('classes', 'etudiants.classe_id', '=', 'classes.id')
                ->where('notes.professeur_id', Auth::id())
                ->withCasts([
                    'date_evaluation' => 'date:Y-m-d',
                    'note' => 'float',
                    'coefficient' => 'float',
                    'created_at' => 'datetime:d/m/Y H:i',
                    'updated_at' => 'datetime:d/m/Y H:i'
                ]);
            
            // Filtrage par recherche
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('users.nom', 'like', "%{$search}%")
                      ->orWhere('users.prenom', 'like', "%{$search}%")
                      ->orWhere('matieres.nom', 'like', "%{$search}%")
                      ->orWhere('notes.commentaire', 'like', "%{$search}%");
                });
            }
            
            // Filtrage par matière
            if (!empty($filters['matiere'])) {
                $query->where('notes.matiere_id', $filters['matiere']);
            }
            
            // Filtrage par classe
            if (!empty($filters['classe'])) {
                $query->where('etudiants.classe_id', $filters['classe']);
            }
            
            // Filtrage par plage de dates
            if (!empty($filters['date_debut'])) {
                $query->whereDate('notes.date_evaluation', '>=', $filters['date_debut']);
            }
            
            if (!empty($filters['date_fin'])) {
                $query->whereDate('notes.date_evaluation', '<=', $filters['date_fin']);
            }
            
            return $query->orderBy('notes.date_evaluation', 'desc')
                        ->orderBy('notes.created_at', 'desc')
                        ->paginate($this->perPage)
                        ->withQueryString()
                        ->through(function ($note) {
                            return [
                                'id' => $note->id,
                                'etudiant' => [
                                    'id' => $note->etudiant_id,
                                    'user_id' => $note->user_id,
                                    'nom' => $note->nom,
                                    'prenom' => $note->prenom,
                                    'nom_complet' => "{$note->prenom} {$note->nom}",
                                    'classe' => [
                                        'id' => $note->classe_id,
                                        'nom' => $note->classe_nom
                                    ]
                                ],
                                'matiere' => [
                                    'id' => $note->matiere_id,
                                    'nom' => $note->matiere_nom,
                                    'code' => $note->matiere_code
                                ],
                                'type_note' => $note->type_note,
                                'valeur' => (float) $note->note,
                                'coefficient' => (float) $note->coefficient,
                                'date_evaluation' => $note->date_evaluation,
                                'commentaire' => $note->commentaire,
                                'created_at' => $note->created_at,
                                'updated_at' => $note->updated_at,
                            ];
                        });
        }, 30); // Mise en cache pour 30 minutes
        
        return Inertia::render('Professeur/Notes/Index', [
            'notes' => $notes,
            'matieres' => $matieres,
            'filters' => $filters
        ]);
    }

    /**
     * Affiche le formulaire de création d'une note
     */
    public function create()
    {
        $matieres = Auth::user()->matieresEnseignees()
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return Inertia::render('Professeur/Notes/Create', [
            'matieres' => $matieres,
        ]);
    }

    /**
     * Enregistre une nouvelle note
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type_note' => 'required|in:devoir,composition,examen,participation',
            'note' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:0.1|max:5',
            'date_evaluation' => 'required|date',
            'commentaire' => 'nullable|string|max:500',
        ]);
        
        // Vérifier que la matière est bien enseignée par le professeur
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $validated['matiere_id'])->exists()) {
            return back()->withErrors(['matiere_id' => 'Vous n\'enseignez pas cette matière.']);
        }
        
        // Ajouter l'ID du professeur
        $validated['professeur_id'] = Auth::id();
        
        // Créer la note
        $note = Note::create($validated);
        
        return redirect()->route('professeur.notes.index')
            ->with('success', 'La note a été enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une note
     */
    public function show($id)
    {
        $note = Note::with(['etudiant', 'matiere', 'professeur'])
            ->where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('Professeur/Notes/Show', [
            'note' => $note
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une note
     */
    public function edit($id)
    {
        $note = Note::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();
            
        $matieres = Auth::user()->matieresEnseignees()
            ->orderBy('nom')
            ->get(['id', 'nom']);
            
        $etudiants = Etudiant::whereHas('inscriptions', function($query) use ($note) {
                $query->whereIn('classe_id', function($q) use ($note) {
                    $q->select('classe_id')
                      ->from('enseignements')
                      ->where('matiere_id', $note->matiere_id)
                      ->where('professeur_id', Auth::id());
                });
            })
            ->orWhere('id', $note->etudiant_id)
            ->orderBy('nom')
            ->get(['id', 'nom', 'prenom']);

        return Inertia::render('Professeur/Notes/Edit', [
            'note' => $note,
            'matieres' => $matieres,
            'etudiants' => $etudiants,
        ]);
    }

    /**
     * Met à jour une note existante
     */
    public function update(Request $request, $id)
    {
        $note = Note::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type_note' => 'required|in:devoir,composition,examen,participation',
            'note' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:0.1|max:5',
            'date_evaluation' => 'required|date',
            'commentaire' => 'nullable|string|max:500',
        ]);
        
        // Vérifier que la matière est bien enseignée par le professeur
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $validated['matiere_id'])->exists()) {
            return back()->withErrors(['matiere_id' => 'Vous n\'enseignez pas cette matière.']);
        }
        
        $note->update($validated);
        
        return redirect()->route('professeur.notes.index')
            ->with('success', 'La note a été mise à jour avec succès.');
    }
    
    /**
     * Supprime une note
     */
    public function destroy($id)
    {
        $note = Note::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();
            
        $note->delete();
        
        return redirect()->route('professeur.notes.index')
            ->with('success', 'La note a été supprimée avec succès.');
    }

    /**
     * Récupère les étudiants pour une matière donnée
     */
    public function getEtudiantsByMatiere($matiereId)
    {
        // Vérifier que le professeur enseigne bien cette matière
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $matiereId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à voir les étudiants de cette matière.'
            ], 403);
        }

        // Récupérer les étudiants qui suivent cette matière
        $etudiants = Etudiant::with(['user:id,nom,prenom'])
            ->select('id', 'user_id', 'numero_etudiant')
            ->orderBy('nom')
            ->get()
            ->map(function ($etudiant) {
                return [
                    'id' => $etudiant->id,
                    'user_id' => $etudiant->user_id,
                    'numero_etudiant' => $etudiant->numero_etudiant,
                    'nom_complet' => $etudiant->user->prenom . ' ' . $etudiant->user->nom,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $etudiants
        ]);
    }

    /**
     * Calcule la moyenne d'un étudiant dans une matière
     *
     * @param int $etudiantId
     * @param int $matiereId
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculerMoyenne($etudiantId, $matiereId)
    {
        // Vérifier que l'étudiant existe
        $etudiant = Etudiant::findOrFail($etudiantId);
        
        // Vérifier que la matière existe et est enseignée par le professeur
        $matiere = Matiere::where('id', $matiereId)
            ->whereHas('professeurs', function($query) {
                $query->where('professeur_id', Auth::id());
            })
            ->firstOrFail();
        
        // Récupérer toutes les notes de l'étudiant dans cette matière
        $notes = Note::where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->where('professeur_id', Auth::id())
            ->get();
        
        if ($notes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune note trouvée pour cet étudiant dans cette matière.'
            ], 404);
        }
        
        // Calculer la moyenne pondérée
        $totalPondere = 0;
        $totalCoefficients = 0;
        
        foreach ($notes as $note) {
            $totalPondere += $note->note * $note->coefficient;
            $totalCoefficients += $note->coefficient;
        }
        
        $moyenne = $totalCoefficients > 0 ? $totalPondere / $totalCoefficients : 0;
        
        return response()->json([
            'success' => true,
            'data' => [
                'etudiant_id' => $etudiant->id,
                'etudiant_nom' => $etudiant->nom_complet ?? ($etudiant->prenom . ' ' . $etudiant->nom),
                'matiere_id' => $matiere->id,
                'matiere_nom' => $matiere->nom,
                'nombre_notes' => $notes->count(),
                'moyenne' => round($moyenne, 2),
                'notes' => $notes->map(function($note) {
                    return [
                        'id' => $note->id,
                        'type' => $note->type_note,
                        'valeur' => $note->note,
                        'coefficient' => $note->coefficient,
                        'date' => $note->date_evaluation->format('d/m/Y'),
                        'commentaire' => $note->commentaire
                    ];
                })
            ]
        ]);
    }
}
