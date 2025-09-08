<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MatiereController extends BaseAdminController
{
    /**
     * Affiche la liste des matières
     *
     * @return \Illuminate\View\View
     */
    /**
     * Affiche la liste des matières groupées par niveau
     *
     * @return \Illuminate\View\View
     */
    /**
     * Affiche la liste des matières avec filtrage et tri
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Matiere::withCount(['eleves', 'professeurs']);

        // Filtrage par recherche
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtrage par niveau
        if ($request->has('niveau') && $request->niveau !== '') {
            $query->where('niveau', $request->niveau);
        }

        // Filtrage par statut
        if ($request->has('actif') && $request->actif !== 'tous') {
            $query->where('est_active', $request->actif === 'actif');
        }

        // Tri
        $sortField = $request->get('sort', 'nom');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Récupération des matières
        $matieres = $query->get();

        // Si la requête est une requête AJAX, on renvoie les données en JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'matieres' => $matieres,
                'niveaux' => Matiere::getNiveauxDisponibles()
            ]);
        }

        // Pour une requête normale, on renvoie la vue avec les données
        return view('admin.matieres.index', [
            'matieres' => $matieres,
            'niveaux' => Matiere::getNiveauxDisponibles()
        ]);
    }

    /**
     * Affiche le formulaire de création d'une matière
     *
     * @return \Illuminate\View\View
     */
    /**
     * Affiche le formulaire de création d'une matière
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $niveaux = Matiere::getNiveauxDisponibles();
        $matieresFixes = Matiere::getMatieresFixes();
        
        return view('admin.matieres.create', compact('niveaux', 'matieresFixes'));
    }

    /**
     * Enregistre une nouvelle matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Enregistre une nouvelle matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom',
            'description' => 'nullable|string',
            'niveau' => ['required', 'string', Rule::in(array_keys(Matiere::getNiveauxDisponibles()))],
            'prix_mensuel' => 'required|numeric|min:0',
            'prix_trimestriel' => 'nullable|numeric|min:0',
            'couleur' => 'required|string|size:7',
            'icone' => 'nullable|string|max:50',
            'est_active' => 'boolean',
            'est_fixe' => 'boolean'
        ]);

        // Créer un slug à partir du nom
        $validated['slug'] = Str::slug($validated['nom']);
        
        // Si le prix trimestriel n'est pas défini, on le calcule automatiquement (3 mois avec 10% de réduction)
        if (!isset($validated['prix_trimestriel']) || $validated['prix_trimestriel'] === null) {
            $validated['prix_trimestriel'] = $validated['prix_mensuel'] * 3 * 0.9; // 10% de réduction
        }

        // Vérifier si une matière avec le même nom et niveau existe déjà
        $existingMatiere = Matiere::where('nom', $validated['nom'])
            ->where('niveau', $validated['niveau'])
            ->exists();
            
        if ($existingMatiere) {
            return back()
                ->withInput()
                ->with('error', 'Une matière avec ce nom et ce niveau existe déjà.');
        }

        // Créer la matière
        $matiere = Matiere::create($validated);

        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière créée avec succès.');
    }

    /**
     * Affiche les détails d'une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\View\View
     */
    public function show(Matiere $matiere)
    {
        $matiere->load(['eleves' => function($query) {
            $query->orderBy('name');
        }, 'professeurs' => function($query) {
            $query->orderBy('name');
        }]);
        
        // Statistiques
        $stats = [
            'nombre_eleves' => $matiere->eleves->count(),
            'nombre_professeurs' => $matiere->professeurs->count(),
            'revenu_mensuel' => $matiere->eleves->count() * $matiere->prix_mensuel,
        ];
        
        return view('admin.matieres.show', compact('matiere', 'stats'));
    }

    /**
     * Affiche le formulaire de modification d'une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\View\View
     */
    /**
     * Affiche le formulaire de modification d'une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\View\View
     */
    public function edit(Matiere $matiere)
    {
        $niveaux = Matiere::getNiveauxDisponibles();
        $matieresFixes = Matiere::getMatieresFixes();
        
        return view('admin.matieres.edit', compact('matiere', 'niveaux', 'matieresFixes'));
    }

    /**
     * Met à jour une matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('matieres')->ignore($matiere->id)],
            'description' => 'nullable|string',
            'niveau' => ['required', 'string', Rule::in(array_keys(Matiere::getNiveauxDisponibles()))],
            'prix_mensuel' => 'required|numeric|min:0',
            'prix_trimestriel' => 'nullable|numeric|min:0',
            'couleur' => 'required|string|size:7',
            'icone' => 'nullable|string|max:50',
            'est_active' => 'boolean',
            'est_fixe' => 'boolean'
        ]);

        // Si c'est une matière fixe, on s'assure qu'elle reste active
        if ($matiere->est_fixe) {
            $validated['est_active'] = true;
        }

        // Si le prix trimestriel n'est pas défini, on le calcule automatiquement (3 mois avec 10% de réduction)
        if (!isset($validated['prix_trimestriel']) || $validated['prix_trimestriel'] === null) {
            $validated['prix_trimestriel'] = $validated['prix_mensuel'] * 3 * 0.9; // 10% de réduction
        }

        // Vérifier si une autre matière avec le même nom et niveau existe déjà
        $existingMatiere = Matiere::where('nom', $validated['nom'])
            ->where('niveau', $validated['niveau'])
            ->where('id', '!=', $matiere->id)
            ->exists();
            
        if ($existingMatiere) {
            return back()
                ->withInput()
                ->with('error', 'Cette matière existe déjà pour ce niveau.');
        }
        
        // Mettre à jour la matière avec tous les champs validés
        $matiere->update($validated);
        
        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière mise à jour avec succès.');
    }

    /**
     * Affiche la liste des élèves inscrits à une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\View\View
     */
    public function eleves(Matiere $matiere)
    {
        $eleves = $matiere->eleves()
            ->orderBy('name')
            ->paginate(20);
            
        return view('admin.matieres.eleves', compact('matiere', 'eleves'));
    }

    /**
     * Affiche la liste des professeurs d'une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\View\View
     */
    public function professeurs(Matiere $matiere)
    {
        $professeurs = $matiere->professeurs()
            ->orderBy('name')
            ->paginate(20);
            
        return view('admin.matieres.professeurs', compact('matiere', 'professeurs'));
    }

    /**
     * Affiche le formulaire d'ajout d'élèves à une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\View\View
     */
    public function ajouterElevesForm(Matiere $matiere)
    {
        $eleves = User::where('role', 'eleve')
            ->where('status', 'actif')
            ->whereDoesntHave('matieres', function($query) use ($matiere) {
                $query->where('matiere_id', $matiere->id);
            })
            ->orderBy('name')
            ->get();
            
        return view('admin.matieres.ajouter-eleves', compact('matiere', 'eleves'));
    }

    /**
     * Ajoute des élèves à une matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ajouterEleves(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'eleves' => 'required|array',
            'eleves.*' => 'exists:users,id',
        ]);
        
        // Vérifier que les élèves sont bien des élèves actifs
        $eleves = User::whereIn('id', $validated['eleves'])
            ->where('role', 'eleve')
            ->where('status', 'actif')
            ->pluck('id');
            
        // Ajouter les élèves à la matière
        $matiere->eleves()->syncWithoutDetaching($eleves);
        
        return redirect()->route('admin.matieres.eleves', $matiere)
            ->with('success', count($eleves) . ' élèves ont été ajoutés à la matière.');
    }

    /**
     * Supprime un élève d'une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @param  \App\Models\User  $eleve
     * @return \Illuminate\Http\RedirectResponse
     */
    public function supprimerEleve(Matiere $matiere, User $eleve)
    {
        if ($eleve->role !== 'eleve') {
            return back()->with('error', 'Seuls les élèves peuvent être retirés d\'une matière.');
        }
        
        $matiere->eleves()->detach($eleve->id);
        
        return back()->with('success', 'L\'élève a été retiré de la matière avec succès.');
    }

    /**
     * Affiche le formulaire d'ajout de professeurs à une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\View\View
     */
    public function ajouterProfesseursForm(Matiere $matiere)
    {
        $professeurs = User::where('role', 'professeur')
            ->where('status', 'actif')
            ->whereDoesntHave('matieresEnseignees', function($query) use ($matiere) {
                $query->where('matiere_id', $matiere->id);
            })
            ->orderBy('name')
            ->get();
            
        return view('admin.matieres.ajouter-professeurs', compact('matiere', 'professeurs'));
    }

    /**
     * Ajoute des professeurs à une matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ajouterProfesseurs(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'professeurs' => 'required|array',
            'professeurs.*' => 'exists:users,id',
        ]);
        
        // Vérifier que les professeurs sont bien des professeurs actifs
        $professeurs = User::whereIn('id', $validated['professeurs'])
            ->where('role', 'professeur')
            ->where('status', 'actif')
            ->get();
            
        // Ajouter les professeurs à la matière
        $professeursData = [];
        foreach ($professeurs as $professeur) {
            $professeursData[$professeur->id] = [
                'est_responsable' => true,
                'date_debut' => now(),
            ];
        }
        
        $matiere->professeurs()->syncWithoutDetaching($professeursData);
        
        return redirect()->route('admin.matieres.professeurs', $matiere)
            ->with('success', count($professeurs) . ' professeurs ont été ajoutés à la matière.');
    }

    /**
     * Supprime un professeur d'une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function supprimerProfesseur(Matiere $matiere, User $professeur)
    {
        if ($professeur->role !== 'professeur') {
            return back()->with('error', 'Seuls les professeurs peuvent être retirés d\'une matière.');
        }
        
        $matiere->professeurs()->detach($professeur->id);
        
        return back()->with('success', 'Le professeur a été retiré de la matière avec succès.');
    }
    
    /**
     * Supprime une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Matiere $matiere)
    {
        // Ne pas permettre la suppression des matières fixes
        if ($matiere->isMatiereFixe()) {
            return back()->with('error', 'Impossible de supprimer une matière fixe du système.');
        }
        
        // Vérifier si la matière est utilisée avant de supprimer
        if ($matiere->eleves()->count() > 0 || $matiere->professeurs()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette matière car elle est utilisée.');
        }
        
        $matiere->delete();
        
        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière supprimée avec succès.');
    }

    /**
     * Désactive une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function desactiver(Matiere $matiere)
    {
        if ($matiere->est_fixe) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de désactiver une matière fixe.'
                ], 403);
            }
            return back()->with('error', 'Impossible de désactiver une matière fixe.');
        }
        
        $matiere->update(['est_active' => false]);
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Matière désactivée avec succès.'
            ]);
        }
        
        return back()->with('success', 'Matière désactivée avec succès.');
    }

    /**
     * Activer la matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function activer(Matiere $matiere)
    {
        $matiere->update(['est_active' => true]);
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Matière activée avec succès.'
            ]);
        }
        
        return back()->with('success', 'Matière activée avec succès.');
    }
}
