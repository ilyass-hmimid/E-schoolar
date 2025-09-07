<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MatiereController extends Controller
{
    /**
     * Affiche la liste des matières
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $matieres = Matiere::withCount(['eleves', 'professeurs'])
            ->orderBy('nom')
            ->paginate(15);
            
        return view('admin.matieres.index', compact('matieres'));
    }

    /**
     * Affiche le formulaire de création d'une matière
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.matieres.create');
    }

    /**
     * Enregistre une nouvelle matière
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:matieres,nom',
            'description' => 'nullable|string|max:1000',
            'prix_mensuel' => 'required|numeric|min:0',
            'couleur' => 'required|string|size:7', // Format #RRGGBB
            'est_active' => 'boolean',
        ]);
        
        // Générer un slug à partir du nom
        $validated['slug'] = Str::slug($validated['nom']);
        
        // Vérifier que le slug est unique
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Matiere::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }
        
        // Créer la matière
        $matiere = Matiere::create($validated);
        
        return redirect()->route('admin.matieres.show', $matiere)
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
    public function edit(Matiere $matiere)
    {
        return view('admin.matieres.edit', compact('matiere'));
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
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('matieres', 'nom')->ignore($matiere->id),
            ],
            'description' => 'nullable|string|max:1000',
            'prix_mensuel' => 'required|numeric|min:0',
            'couleur' => 'required|string|size:7', // Format #RRGGBB
            'est_active' => 'boolean',
        ]);
        
        // Mettre à jour la matière
        $matiere->update($validated);
        
        return redirect()->route('admin.matieres.show', $matiere)
            ->with('success', 'Mise à jour de la matière effectuée avec succès.');
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
     * Désactive une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desactiver(Matiere $matiere)
    {
        $matiere->update(['est_active' => false]);
        
        return back()->with('success', 'La matière a été désactivée avec succès.');
    }

    /**
     * Active une matière
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activer(Matiere $matiere)
    {
        $matiere->update(['est_active' => true]);
        
        return back()->with('success', 'La matière a été activée avec succès.');
    }
}
