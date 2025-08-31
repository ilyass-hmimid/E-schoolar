<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Enseignement;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Traits\OptimizedQueries;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AbsenceController extends Controller
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
     * Affiche la liste des absences avec des requêtes optimisées
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'matiere', 'date_debut', 'date_fin', 'justifiee']);
        $cacheKey = 'absences_prof_' . Auth::id() . '_' . md5(json_encode($filters));
        
        // Utilisation de rememberForever avec un tag pour pouvoir l'invalider plus tard si nécessaire
        $matieres = cache()->remember('matieres_prof_' . Auth::id(), now()->addDay(), function() {
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
        });
        
        // Récupérer les absences avec des requêtes optimisées
        $absences = cache()->remember($cacheKey, now()->addHours(2), function() use ($filters, $matieres) {
            $query = Absence::query()
                ->select([
                    'absences.id',
                    'absences.etudiant_id',
                    'absences.matiere_id',
                    'absences.date_absence',
                    'absences.heure_debut',
                    'absences.heure_fin',
                    'absences.type',
                    'absences.justifiee',
                    'absences.created_at',
                    'absences.updated_at',
                    'etudiants.user_id',
                    'users.nom',
                    'users.prenom',
                    'matieres.nom as matiere_nom'
                ])
                ->join('etudiants', 'absences.etudiant_id', '=', 'etudiants.id')
                ->join('users', 'etudiants.user_id', '=', 'users.id')
                ->join('matieres', 'absences.matiere_id', '=', 'matieres.id')
                ->where('absences.professeur_id', Auth::id())
                ->withCasts([
                    'date_absence' => 'date:Y-m-d',
                    'heure_debut' => 'datetime:H:i',
                    'heure_fin' => 'datetime:H:i',
                    'created_at' => 'datetime:d/m/Y H:i',
                    'updated_at' => 'datetime:d/m/Y H:i'
                ]);
            
            // Filtrage par recherche
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('users.nom', 'like', "%{$search}%")
                      ->orWhere('users.prenom', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%");
                });
            }
            
            // Filtrage par matière
            if (!empty($filters['matiere'])) {
                $query->where('absences.matiere_id', $filters['matiere']);
            }
            
            // Filtrage par plage de dates
            if (!empty($filters['date_debut'])) {
                $query->whereDate('absences.date_absence', '>=', $filters['date_debut']);
            }
            
            if (!empty($filters['date_fin'])) {
                $query->whereDate('absences.date_absence', '<=', $filters['date_fin']);
            }
            
            // Filtrage par statut de justification
            if (isset($filters['justifiee']) && $filters['justifiee'] !== '') {
                $query->where('absences.justifiee', (bool)$filters['justifiee']);
            }
            
            // Optimisation: Utilisation de l'index composite sur (professeur_id, date_absence)
            return $query->orderBy('absences.date_absence', 'desc')
                        ->orderBy('absences.heure_debut', 'desc')
                        ->paginate($this->perPage)
                        ->withQueryString()
                        ->through(function ($absence) {
                            return [
                                'id' => $absence->id,
                                'etudiant' => [
                                    'id' => $absence->etudiant_id,
                                    'user_id' => $absence->user_id,
                                    'nom' => $absence->nom,
                                    'prenom' => $absence->prenom,
                                    'nom_complet' => "{$absence->prenom} {$absence->nom}",
                                ],
                                'matiere' => [
                                    'id' => $absence->matiere_id,
                                    'nom' => $absence->matiere_nom,
                                ],
                                'date_absence' => $absence->date_absence,
                                'heure_debut' => $absence->heure_debut,
                                'heure_fin' => $absence->heure_fin,
                                'type' => $absence->type,
                                'justifiee' => $absence->justifiee,
                                'created_at' => $absence->created_at,
                                'updated_at' => $absence->updated_at,
                            ];
                        });
        });

        return Inertia::render('Professeur/Absences/Index', [
            'absences' => $absences,
            'filters' => $filters,
            'matieres' => $matieres,
        ]);
    }

    /**
     * Affiche le formulaire de création d'une absence
     */
    public function create()
    {
        // Récupérer les matières enseignées par le professeur avec mise en cache
        $matieres = $this->cached('matieres_prof_' . Auth::id(), function() {
            return Auth::user()->matieresEnseignees()
                ->orderBy('nom')
                ->get(['id', 'nom']);
        }, $this->cacheTtl);
        
        // Récupérer les étudiants des classes du professeur avec mise en cache
        $etudiants = $this->cached('etudiants_prof_' . Auth::id(), function() {
            return Etudiant::whereHas('inscriptions', function($query) {
                    $query->whereIn('classe_id', function($q) {
                        $q->select('classe_id')
                          ->from('enseignements')
                          ->where('professeur_id', Auth::id());
                    });
                })
                ->with('user:id,nom,prenom')
                ->orderBy('nom')
                ->get(['id', 'user_id', 'numero_etudiant'])
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'nom_complet' => $etudiant->user->nom_complet ?? 'Inconnu',
                        'numero_etudiant' => $etudiant->numero_etudiant
                    ];
                });
        }, $this->cacheTtl);

        return Inertia::render('Professeur/Absences/Create', [
            'matieres' => $matieres,
            'etudiants' => $etudiants,
        ]);
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:absence,retard',
            'duree_retard' => 'required_if:type,retard|integer|min:1|max:240|nullable',
            'motif' => 'required|string|max:255',
            'justifiee' => 'boolean',
            'justification' => 'required_if:justifiee,true|string|nullable',
        ]);
        
        // Vérifier que la matière est bien enseignée par le professeur
        $matiere = $this->cached('matiere_' . $validated['matiere_id'], function() use ($validated) {
            return Auth::user()->matieresEnseignees()
                ->where('matieres.id', $validated['matiere_id'])
                ->first();
        }, $this->cacheTtl);
        
        if (!$matiere) {
            return back()->withErrors(['matiere_id' => 'Vous n\'enseignez pas cette matière.']);
        }
        
        // Vérifier que l'étudiant est bien dans une classe où le professeur enseigne cette matière
        $etudiantValide = $this->cached('etudiant_' . $validated['etudiant_id'] . '_matiere_' . $validated['matiere_id'], function() use ($validated) {
            return Etudiant::where('id', $validated['etudiant_id'])
                ->whereHas('inscriptions', function($query) use ($validated) {
                    $query->whereIn('classe_id', function($q) use ($validated) {
                        $q->select('classe_id')
                          ->from('enseignements')
                          ->where('matiere_id', $validated['matiere_id'])
                          ->where('professeur_id', Auth::id());
                    });
                })
                ->exists();
        }, $this->cacheTtl);
        
        if (!$etudiantValide) {
            return back()->withErrors(['etudiant_id' => 'Cet étudiant ne suit pas cette matière avec vous.']);
        }
        
        // Si ce n'est pas un retard, on supprime la durée du retard
        if ($validated['type'] !== 'retard') {
            $validated['duree_retard'] = null;
        } else {
            // Valider que la durée du retard ne dépasse pas la durée du cours
            $debut = Carbon::parse($validated['heure_debut']);
            $fin = Carbon::parse($validated['heure_fin']);
            $dureeCours = $fin->diffInMinutes($debut);
            
            if ($validated['duree_retard'] > $dureeCours) {
                return back()->withErrors(['duree_retard' => 'La durée du retard ne peut pas dépasser la durée du cours.']);
            }
        }
        
        // Si l'absence n'est pas justifiée, on supprime la justification
        if (empty($validated['justifiee'])) {
            $validated['justification'] = null;
            $validated['justifiee'] = false;
        } else {
            $validated['justifiee'] = true;
        }
        
        // Vérifier les doublons
        $doublon = Absence::where('etudiant_id', $validated['etudiant_id'])
            ->where('matiere_id', $validated['matiere_id'])
            ->where('date_absence', $validated['date_absence'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('heure_debut', [$validated['heure_debut'], $validated['heure_fin']])
                      ->orWhereBetween('heure_fin', [$validated['heure_debut'], $validated['heure_fin']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('heure_debut', '<=', $validated['heure_debut'])
                            ->where('heure_fin', '>=', $validated['heure_fin']);
                      });
            })
            ->exists();
            
        if ($doublon) {
            return back()->withErrors(['general' => 'Une absence existe déjà pour cet étudiant sur ce créneau horaire.']);
        }
        
        // Créer l'absence dans une transaction
        try {
            \DB::beginTransaction();
            
            $absence = new Absence($validated);
            $absence->professeur_id = Auth::id();
            $absence->save();
            
            // Invalider les caches concernés
            $this->invalidateAbsenceCaches();
            
            \DB::commit();

            return redirect()->route('professeur.absences.index')
                ->with('success', 'L\'absence a été enregistrée avec succès.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la création de l\'absence: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'absence.');
        }
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show($id)
    {
        $cacheKey = 'absence_' . $id . '_prof_' . Auth::id();
        $absence = $this->cached($cacheKey, function() use ($id) {
            return Absence::with([
                'etudiant.user:id,nom,prenom',
                'matiere:id,nom',
                'professeur.user:id,nom,prenom'
            ])
            ->where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();
        }, 60); // Mise en cache pour 1 heure

        return Inertia::render('Professeur/Absences/Show', [
            'absence' => $absence
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'une absence
     */
    public function edit($id)
    {
        $cacheKey = 'absence_edit_' . $id . '_prof_' . Auth::id();
        
        // Récupérer l'absence avec mise en cache
        $absence = $this->cached($cacheKey, function() use ($id) {
            return Absence::with(['etudiant.user:id,nom,prenom', 'matiere:id,nom'])
                ->where('id', $id)
                ->where('professeur_id', Auth::id())
                ->firstOrFail();
        }, 30); // Mise en cache pour 30 minutes
        
        // Récupérer les matières avec mise en cache
        $matieres = $this->cached('matieres_prof_' . Auth::id(), function() {
            return Auth::user()->matieresEnseignees()
                ->orderBy('nom')
                ->get(['id', 'nom']);
        }, $this->cacheTtl);
        
        // Récupérer les étudiants avec mise en cache
        $etudiants = $this->cached('etudiants_matiere_' . $absence->matiere_id . '_prof_' . Auth::id(), function() use ($absence) {
            return Etudiant::whereHas('inscriptions', function($query) use ($absence) {
                    $query->whereIn('classe_id', function($q) use ($absence) {
                        $q->select('classe_id')
                          ->from('enseignements')
                          ->where('matiere_id', $absence->matiere_id)
                          ->where('professeur_id', Auth::id());
                    });
                })
                ->orWhere('id', $absence->etudiant_id) // Inclure l'étudiant actuel
                ->with('user:id,nom,prenom')
                ->orderBy('nom')
                ->get(['id', 'user_id', 'numero_etudiant'])
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'nom_complet' => $etudiant->user->nom_complet ?? 'Inconnu',
                        'numero_etudiant' => $etudiant->numero_etudiant
                    ];
                });
        }, $this->cacheTtl);

        return Inertia::render('Professeur/Absences/Edit', [
            'absence' => $absence,
            'matieres' => $matieres,
            'etudiants' => $etudiants,
        ]);
    }

    /**
     * Met à jour une absence existante
     */
    public function update(Request $request, $id)
    {
        $absence = Absence::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'date_absence' => 'required|date|before_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:absence,retard',
            'duree_retard' => 'required_if:type,retard|integer|min:1|max:240|nullable',
            'motif' => 'required|string|max:255',
            'justifiee' => 'boolean',
            'justification' => 'required_if:justifiee,true|string|nullable',
        ]);
        
        // Vérifier que la matière est bien enseignée par le professeur
        if (!Auth::user()->matieresEnseignees()->where('matieres.id', $validated['matiere_id'])->exists()) {
            return back()->withErrors(['matiere_id' => 'Vous n\'enseignez pas cette matière.']);
        }
        
        // Si ce n'est pas un retard, on supprime la durée du retard
        if ($validated['type'] !== 'retard') {
            $validated['duree_retard'] = null;
        } else {
            // Valider que la durée du retard ne dépasse pas la durée du cours
            $debut = Carbon::parse($validated['heure_debut']);
            $fin = Carbon::parse($validated['heure_fin']);
            $dureeCours = $fin->diffInMinutes($debut);
            
            if ($validated['duree_retard'] > $dureeCours) {
                return back()->withErrors(['duree_retard' => 'La durée du retard ne peut pas dépasser la durée du cours.']);
            }
        }
        
        // Si l'absence n'est pas justifiée, on supprime la justification
        if (empty($validated['justifiee'])) {
            $validated['justification'] = null;
            $validated['justifiee'] = false;
        } else {
            $validated['justifiee'] = true;
        }
        
        // Vérifier les doublons (sauf l'absence actuelle)
        $doublon = Absence::where('id', '!=', $id)
            ->where('etudiant_id', $absence->etudiant_id)
            ->where('matiere_id', $validated['matiere_id'])
            ->where('date_absence', $validated['date_absence'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('heure_debut', [$validated['heure_debut'], $validated['heure_fin']])
                      ->orWhereBetween('heure_fin', [$validated['heure_debut'], $validated['heure_fin']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('heure_debut', '<=', $validated['heure_debut'])
                            ->where('heure_fin', '>=', $validated['heure_fin']);
                      });
            })
            ->exists();
            
        if ($doublon) {
            return back()->withErrors(['general' => 'Une autre absence existe déjà pour cet étudiant sur ce créneau horaire.']);
        }
        
        // Mettre à jour dans une transaction
        try {
            \DB::beginTransaction();
            
            $absence->update($validated);
            
            // Invalider les caches concernés
            $this->invalidateAbsenceCaches($absence->id);
            
            \DB::commit();

            return redirect()->route('professeur.absences.index')
                ->with('success', 'L\'absence a été mise à jour avec succès.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la mise à jour de l\'absence: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de l\'absence.');
        }
    }

    /**
     * Supprime une absence
     */
    public function destroy($id)
    {
        $absence = Absence::where('id', $id)
            ->where('professeur_id', Auth::id())
            ->firstOrFail();
        
        try {
            \DB::beginTransaction();
            
            $absenceId = $absence->id;
            $absence->delete();
            
            // Invalider les caches concernés
            $this->invalidateAbsenceCaches($absenceId);
            
            \DB::commit();
            
            return redirect()->route('professeur.absences.index')
                ->with('success', 'L\'absence a été supprimée avec succès.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la suppression de l\'absence: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'absence.');
        }
    }
}
