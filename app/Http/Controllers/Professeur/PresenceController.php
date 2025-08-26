<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Http\Traits\OptimizedQueries;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Presence;
use App\Services\PresenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PresenceController extends Controller
{
    use OptimizedQueries;
    
    /**
     * Le service de gestion des présences
     *
     * @var PresenceService
     */
    protected $presenceService;
    
    /**
     * Crée une nouvelle instance du contrôleur
     *
     * @param PresenceService $presenceService
     */
    public function __construct(PresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
        
        // Appliquer le middleware d'authentification et de rôle
        $this->middleware(['auth', 'role:professeur']);
    }
    
    /**
     * Affiche la liste des présences
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Inertia\Response
     */
    public function index(Request $request)
    {
        // Récupérer les filtres de la requête
        $filters = $request->only([
            'etudiant_id', 'matiere_id', 'classe_id', 
            'date_debut', 'date_fin', 'statut'
        ]);
        
        // Ajouter l'ID du professeur connecté aux filtres
        $filters['professeur_id'] = Auth::id();
        
        // Récupérer les présences paginées avec mise en cache
        $presences = $this->presenceService->getPaginatedPresences($filters);
        
        // Si la requête est une requête AJAX ou une API
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($presences);
        }
        
        // Pour une requête classique, retourner la vue Inertia
        return inertia('Professeur/Presences/Index', [
            'presences' => $presences,
            'filters' => $filters,
            'classes' => $this->cached('classes_list', function() {
                return Classe::select('id', 'nom')->get();
            }, 1440), // Mise en cache pour 24h
            'matieres' => $this->cached('matieres_list', function() {
                return Matiere::select('id', 'nom')->get();
            }, 1440), // Mise en cache pour 24h
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'une présence
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('Professeur/Presences/Create', [
            'classes' => $this->cached('classes_list', function() {
                return Classe::select('id', 'nom')->get();
            }, 1440), // Mise en cache pour 24h
            'matieres' => $this->cached('matieres_list', function() {
                return Matiere::select('id', 'nom')->get();
            }, 1440), // Mise en cache pour 24h
        ]);
    }
    
    /**
     * Enregistre une nouvelle présence
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'classe_id' => 'required|exists:classes,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'statut' => 'required|in:present,absent,retard,justifie',
            'remarques' => 'nullable|string|max:500',
        ]);
        
        // Utiliser le service pour créer la présence
        $result = $this->presenceService->marquerPresences([
            'etudiants' => [
                $validated['etudiant_id'] => [
                    'statut' => $validated['statut'],
                    'remarques' => $validated['remarques'] ?? null,
                ]
            ],
            'matiere_id' => $validated['matiere_id'],
            'classe_id' => $validated['classe_id'],
            'date_seance' => $validated['date_seance'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
        ], Auth::user());
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }
        
        if ($result['success']) {
            return redirect()->route('professeur.presences.index')
                ->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
    
    /**
     * Affiche les détails d'une présence
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $presence = $this->cached('presence_' . $id, function() use ($id) {
            return Presence::with(['etudiant', 'matiere', 'classe', 'professeur'])
                ->findOrFail($id);
        }, 60); // Mise en cache pendant 1 heure
        
        return inertia('Professeur/Presences/Show', [
            'presence' => $presence
        ]);
    }
    
    /**
     * Met à jour une présence existante
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'classe_id' => 'required|exists:classes,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'statut' => 'required|in:present,absent,retard,justifie',
            'remarques' => 'nullable|string|max:500',
        ]);
        
        // Utiliser le service pour mettre à jour la présence
        $result = $this->presenceService->updatePresence($id, [
            'statut' => $validated['statut'],
            'remarques' => $validated['remarques'] ?? null,
        ]);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }
        
        if ($result['success']) {
            return redirect()->route('professeur.presences.show', $id)
                ->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
    
    /**
     * Supprime une présence
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Utiliser le service pour supprimer la présence
        $result = $this->presenceService->deletePresence($id);
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($result);
        }
        
        if ($result['success']) {
            return redirect()->route('professeur.presences.index')
                ->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
    
    /**
     * Récupère les étudiants d'une classe pour une matière donnée
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEtudiantsByClasseMatiere(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
        ]);
        
        $cacheKey = 'etudiants_classe_' . $request->classe_id . '_matiere_' . $request->matiere_id;
        
        $etudiants = $this->cached($cacheKey, function() use ($request) {
            return Etudiant::where('classe_id', $request->classe_id)
                ->with(['user:id,nom,prenom'])
                ->select('id', 'user_id', 'numero_etudiant')
                ->get()
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'user_id' => $etudiant->user_id,
                        'numero_etudiant' => $etudiant->numero_etudiant,
                        'nom_complet' => $etudiant->user->nom_complet ?? 'Inconnu',
                    ];
                });
        }, 60); // Mise en cache pendant 1 heure
        
        return response()->json($etudiants);
    }
    
    /**
     * Affiche le formulaire de marquage des présences pour une classe et une matière
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function showMarquerPresences(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'date_seance' => 'required|date',
        ]);
        
        $classe = Classe::findOrFail($request->classe_id);
        $matiere = Matiere::findOrFail($request->matiere_id);
        
        // Récupérer les étudiants de la classe avec mise en cache
        $etudiants = $this->cached('etudiants_classe_' . $classe->id, function() use ($classe) {
            return $classe->etudiants()
                ->with(['user:id,nom,prenom'])
                ->select('id', 'user_id', 'numero_etudiant')
                ->get()
                ->map(function($etudiant) {
                    return [
                        'id' => $etudiant->id,
                        'user_id' => $etudiant->user_id,
                        'numero_etudiant' => $etudiant->numero_etudiant,
                        'nom_complet' => $etudiant->user->nom_complet ?? 'Inconnu',
                        'statut' => 'present', // Valeur par défaut
                        'remarques' => null,
                    ];
                });
        }, 60); // Mise en cache pendant 1 heure
        
        return inertia('Professeur/Presences/Marquer', [
            'classe' => $classe,
            'matiere' => $matiere,
            'etudiants' => $etudiants,
            'date_seance' => $request->date_seance,
            'heure_debut' => $request->heure_debut ?? '08:00',
            'heure_fin' => $request->heure_fin ?? '10:00',
        ]);
    }
    
    /**
     * Enregistre les présences pour une liste d'étudiants
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function marquerPresences(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'etudiants' => 'required|array|min:1',
            'etudiants.*.id' => 'required|exists:etudiants,id',
            'etudiants.*.statut' => 'required|in:present,absent,retard,justifie',
            'etudiants.*.remarques' => 'nullable|string|max:500',
            'etudiants.*.duree_retard' => 'required_if:etudiants.*.statut,retard|nullable|integer|min:1',
            'etudiants.*.justificatif' => 'required_if:etudiants.*.statut,justifie|nullable|string|max:255',
        ]);
        
        // Préparer les données pour le service
        $data = [
            'classe_id' => $validated['classe_id'],
            'matiere_id' => $validated['matiere_id'],
            'date_seance' => $validated['date_seance'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'etudiants' => []
        ];
        
        foreach ($validated['etudiants'] as $etudiant) {
            $data['etudiants'][$etudiant['id']] = [
                'statut' => $etudiant['statut'],
                'remarques' => $etudiant['remarques'] ?? null,
                'duree_retard' => $etudiant['duree_retard'] ?? null,
                'justificatif' => $etudiant['justificatif'] ?? null,
            ];
        }
        
        // Utiliser le service pour enregistrer les présences
        $result = $this->presenceService->marquerPresences($data, Auth::user());
        
        return response()->json($result);
    }
    
    /**
     * Affiche les statistiques des présences
     *
     * @param Request $request
     * @return \Inertia\Response|\Illuminate\Http\JsonResponse
     */
    public function getStatistiques(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'classe_id' => 'nullable|exists:classes,id',
            'matiere_id' => 'nullable|exists:matieres,id',
        ]);
        
        // Ajouter l'ID du professeur connecté aux filtres
        $validated['professeur_id'] = Auth::id();
        
        // Récupérer les statistiques avec mise en cache
        $statistiques = $this->presenceService->getStatistics($validated);
        
        // Si c'est une requête AJAX, retourner les données en JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($statistiques);
        }
        
        // Sinon, retourner la vue Inertia avec les données
        return inertia('Professeur/Presences/Statistiques', [
            'statistiques' => $statistiques,
            'filters' => $validated,
            'classes' => $this->cached('classes_list', function() {
                return Classe::select('id', 'nom')->get();
            }, 1440), // Mise en cache pour 24h
            'matieres' => $this->cached('matieres_list', function() {
                return Matiere::select('id', 'nom')->get();
            }, 1440), // Mise en cache pour 24h
        ]);
    }
}
