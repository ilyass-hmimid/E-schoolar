<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClasseExport;
use App\Models\Classe;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ClasseController extends Controller
{
    /**
     * Afficher la liste des classes du professeur
     */
    public function index(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        // Construire la requête pour les classes du professeur
        $query = $user->classes()
            ->withCount('eleves')
            ->with('matiere')
            ->latest();

        // Appliquer les filtres
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('niveau')) {
            $query->where('niveau_id', $request->niveau);
        }

        if ($request->filled('annee_scolaire')) {
            $query->where('annee_scolaire', $request->annee_scolaire);
        } else {
            // Année scolaire par défaut (année en cours / année suivante)
            $currentYear = date('Y');
            $nextYear = $currentYear + 1;
            $defaultAnneeScolaire = "$currentYear-$nextYear";
            $query->where('annee_scolaire', $defaultAnneeScolaire);
        }

        // Paginer les résultats
        $classes = $query->paginate(10)->withQueryString();

        // Récupérer les niveaux pour le filtre
        $niveaux = Niveau::orderBy('nom')->get(['id', 'nom']);

        // Générer les années scolaires pour le filtre (5 dernières années)
        $currentYear = date('Y');
        $anneesScolaires = [];
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $anneesScolaires[] = ($year) . '-' . ($year + 1);
        }

        return Inertia::render('Professeur/Classes', [
            'classes' => $classes,
            'niveaux' => $niveaux,
            'anneesScolaires' => $anneesScolaires,
            'filters' => $request->only(['search', 'niveau', 'annee_scolaire'])
        ]);
    }

    /**
     * Afficher les détails d'une classe
     */
    public function show(Classe $classe)
    {
        // Vérifier que le professeur a bien accès à cette classe
        $this->authorize('view', $classe);

        // Charger les relations nécessaires
        $classe->load([
            'eleves' => function ($query) {
                $query->orderBy('nom')->orderBy('prenom');
            },
            'matiere',
            'niveau',
            'notes'
        ]);

        // Récupérer les élèves disponibles (ceux qui ne sont pas déjà dans la classe)
        $elevesIds = $classe->eleves->pluck('id')->toArray();
        $availableStudents = User::role('eleve')
            ->whereNotIn('id', $elevesIds)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get(['id', 'nom', 'prenom', 'email']);

        // Charger les notes moyennes des élèves
        $elevesAvecMoyennes = $classe->eleves->map(function ($eleve) use ($classe) {
            $moyenne = $eleve->notes()
                ->where('matiere_id', $classe->matiere_id)
                ->where('classe_id', $classe->id)
                ->avg('valeur');

            return [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'email' => $eleve->email,
                'moyenne' => $moyenne ? (float) round($moyenne, 2) : null,
            ];
        });

        // Calculer les statistiques
        $moyenneClasse = $elevesAvecMoyennes->filter(fn($e) => $e['moyenne'] !== null)
            ->avg('moyenne');

        $meilleurEleve = $elevesAvecMoyennes->filter(fn($e) => $e['moyenne'] !== null)
            ->sortByDesc('moyenne')
            ->first();

        return Inertia::render('Professeur/Classes/Show', [
            'classe' => [
                'id' => $classe->id,
                'nom' => $classe->nom,
                'matiere' => [
                    'id' => $classe->matiere->id,
                    'nom' => $classe->matiere->nom,
                ],
                'niveau' => [
                    'id' => $classe->niveau->id,
                    'nom' => $classe->niveau->nom,
                ],
                'annee_scolaire' => $classe->annee_scolaire,
                'effectif_max' => $classe->effectif_max,
                'effectif_actuel' => $classe->eleves->count(),
            ],
            'eleves' => $elevesAvecMoyennes,
            'availableStudents' => $availableStudents,
            'stats' => [
                'moyenneClasse' => $moyenneClasse ? (float) round($moyenneClasse, 2) : null,
                'meilleurEleve' => $meilleurEleve ?: null,
                'effectifTotal' => $classe->eleves->count(),
                'effectifMax' => $classe->effectif_max,
                'tauxRemplissage' => $classe->effectif_max 
                    ? round(($classe->eleves->count() / $classe->effectif_max) * 100, 1)
                    : 100
            ]
        ]);
    }

    /**
     * Afficher le formulaire de création d'une classe
     */
    public function create()
    {
        $this->authorize('create', Classe::class);

        $niveaux = Niveau::orderBy('nom')->get();
        $matieres = Auth::user()->matieres;

        // Année scolaire par défaut (année en cours / année suivante)
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $defaultAnneeScolaire = "$currentYear-$nextYear";

        return Inertia::render('Professeur/Classes/Create', [
            'niveaux' => $niveaux,
            'matieres' => $matieres,
            'defaultAnneeScolaire' => $defaultAnneeScolaire,
        ]);
    }

    /**
     * Enregistrer une nouvelle classe
     */
    public function store(Request $request)
    {
        $this->authorize('create', Classe::class);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'matiere_id' => 'required|exists:matieres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'annee_scolaire' => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
            'description' => 'nullable|string',
            'effectif_max' => 'nullable|integer|min:1'
        ]);

        // Vérifier si la classe existe déjà pour ce professeur, cette matière et cette année
        $existingClasse = $request->user()->classes()
            ->where('matiere_id', $validated['matiere_id'])
            ->where('niveau_id', $validated['niveau_id'])
            ->where('annee_scolaire', $validated['annee_scolaire'])
            ->first();

        if ($existingClasse) {
            return back()->withErrors([
                'matiere_id' => 'Vous avez déjà une classe pour cette matière, ce niveau et cette année scolaire.'
            ])->withInput();
        }

        $classe = $request->user()->classes()->create($validated);

        return redirect()->route('professeur.classes.show', $classe)
            ->with('success', 'La classe a été créée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'une classe
     */
    public function edit(Classe $classe)
    {
        $this->authorize('update', $classe);

        $niveaux = Niveau::orderBy('nom')->get(['id', 'nom']);
        $matieres = Matiere::orderBy('nom')->get(['id', 'nom']);

        return Inertia::render('Professeur/Classes/Edit', [
            'classe' => $classe,
            'niveaux' => $niveaux,
            'matieres' => $matieres,
        ]);
    }

    /**
     * Mettre à jour une classe
     */
    public function update(Request $request, Classe $classe)
    {
        $this->authorize('update', $classe);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'matiere_id' => 'required|exists:matieres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'annee_scolaire' => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
            'description' => 'nullable|string',
            'effectif_max' => 'nullable|integer|min:1'
        ]);

        // Vérifier si une autre classe existe déjà avec ces paramètres
        $existingClasse = $request->user()->classes()
            ->where('id', '!=', $classe->id)
            ->where('matiere_id', $validated['matiere_id'])
            ->where('niveau_id', $validated['niveau_id'])
            ->where('annee_scolaire', $validated['annee_scolaire'])
            ->first();

        if ($existingClasse) {
            return back()->withErrors([
                'matiere_id' => 'Vous avez déjà une classe pour cette matière, ce niveau et cette année scolaire.'
            ])->withInput();
        }

        $classe->update($validated);

        return redirect()->route('professeur.classes.show', $classe)
            ->with('success', 'La classe a été mise à jour avec succès.');
    }

    /**
     * Supprimer une classe
     */
    public function destroy(Classe $classe)
    {
        $this->authorize('delete', $classe);

        // Vérifier qu'il n'y a pas d'élèves dans la classe
        if ($classe->eleves()->count() > 0) {
            return back()->withErrors([
                'message' => 'Impossible de supprimer une classe contenant des élèves.'
            ]);
        }

        $classe->delete();

        return redirect()->route('professeur.classes.index')
            ->with('success', 'La classe a été supprimée avec succès.');
    }

    /**
     * Ajouter un élève à une classe
     */
    public function addEleve(Request $request, Classe $classe)
    {
        $this->authorize('update', $classe);

        $validated = $request->validate([
            'eleve_id' => 'required|exists:users,id',
        ]);

        // Vérifier que l'utilisateur est bien un élève
        $eleve = User::findOrFail($validated['eleve_id']);
        if (!$eleve->hasRole('eleve')) {
            return response()->json([
                'message' => 'L\'utilisateur sélectionné n\'est pas un élève.'
            ], 422);
        }

        // Vérifier que l'élève n'est pas déjà dans la classe
        if ($classe->eleves()->where('user_id', $eleve->id)->exists()) {
            return response()->json([
                'message' => 'Cet élève est déjà dans cette classe.'
            ], 422);
        }

        // Vérifier l'effectif maximum si défini
        if ($classe->effectif_max && $classe->eleves()->count() >= $classe->effectif_max) {
            return response()->json([
                'message' => 'La classe a atteint son effectif maximum.'
            ], 422);
        }

        $classe->eleves()->attach($eleve->id);

        // Charger les données mises à jour de l'élève avec la moyenne
        $eleve = $classe->eleves()->find($eleve->id);
        $moyenne = $eleve->notes()
            ->where('matiere_id', $classe->matiere_id)
            ->where('classe_id', $classe->id)
            ->avg('valeur');

        return response()->json([
            'message' => 'L\'élève a été ajouté à la classe avec succès.',
            'eleve' => [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'email' => $eleve->email,
                'moyenne' => $moyenne ? (float) round($moyenne, 2) : null,
            ]
        ]);
    }

    /**
     * Retirer un élève d'une classe
     */
    public function removeEleve(Classe $classe, User $eleve)
    {
        $this->authorize('update', $classe);

        // Vérifier que l'élève est bien dans la classe
        if (!$classe->eleves()->where('user_id', $eleve->id)->exists()) {
            return response()->json([
                'message' => 'Cet élève n\'est pas dans cette classe.'
            ], 404);
        }

        // Supprimer les notes de l'élève pour cette classe
        $eleve->notes()
            ->where('matiere_id', $classe->matiere_id)
            ->where('classe_id', $classe->id)
            ->delete();

        $classe->eleves()->detach($eleve->id);

        return response()->json([
            'message' => 'L\'élève a été retiré de la classe avec succès.'
        ]);
    }

    /**
     * Exporter les données de la classe au format Excel
     */
    public function export(Classe $classe)
    {
        $this->authorize('view', $classe);
        
        $fileName = 'classe-' . str_slug($classe->nom) . '-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new ClasseExport($classe), $fileName);
    }
}
