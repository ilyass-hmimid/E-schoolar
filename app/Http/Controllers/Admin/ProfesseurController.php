<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Salaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProfesseurController extends Controller
{
    /**
     * Affiche la liste des professeurs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $professeurs = User::where('role', 'professeur')
            ->withCount('matieresEnseignees')
            ->latest()
            ->paginate(15);
            
        return view('admin.professeurs.index', compact('professeurs'));
    }

    /**
     * Affiche le formulaire de création d'un professeur
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $matieres = Matiere::orderBy('nom')->get();
        return view('admin.professeurs.create', compact('matieres'));
    }

    /**
     * Enregistre un nouveau professeur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
            'date_naissance' => 'required|date|before:today',
            'date_embauche' => 'required|date|before_or_equal:today',
            'pourcentage_remuneration' => 'required|numeric|min:0|max:100',
            'matieres' => 'required|array|min:1',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Créer l'utilisateur
        $professeur = User::create([
            'name' => $validated['nom'] . ' ' . $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Mot de passe par défaut
            'role' => 'professeur',
            'status' => 'actif',
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
            'date_embauche' => $validated['date_embauche'],
            'pourcentage_remuneration' => $validated['pourcentage_remuneration'],
        ]);

        // Associer le professeur aux matières avec la date de début
        $matieresData = [];
        foreach ($validated['matieres'] as $matiereId) {
            $matieresData[$matiereId] = [
                'est_responsable' => true,
                'date_debut' => now(),
            ];
        }
        $professeur->matieresEnseignees()->sync($matieresData);

        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Professeur créé avec succès. Le mot de passe par défaut est "password".');
    }

    /**
     * Affiche les détails d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    public function show(User $professeur)
    {
        $this->authorize('view', $professeur);
        
        $professeur->load([
            'matieresEnseignees',
            'salaires' => function($query) {
                return $query->orderBy('periode_debut', 'desc')->take(6);
            },
            'absences' => function($query) {
                return $query->with('eleve')->latest()->take(10);
            },
        ]);
        
        // Calculer les statistiques
        $stats = [
            'total_eleves' => $professeur->eleves()->count(),
            'salaire_moyen' => $professeur->salaires()->avg('montant') ?? 0,
            'absences_30j' => $professeur->absences()
                ->where('date_absence', '>=', now()->subDays(30))
                ->count(),
        ];
        
        return view('admin.professeurs.show', compact('professeur', 'stats'));
    }

    /**
     * Affiche le formulaire de modification d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    public function edit(User $professeur)
    {
        $this->authorize('update', $professeur);
        
        $matieres = Matiere::orderBy('nom')->get();
        $professeur->load('matieresEnseignees');
        
        return view('admin.professeurs.edit', compact('professeur', 'matieres'));
    }

    /**
     * Met à jour les informations d'un professeur
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $professeur)
    {
        $this->authorize('update', $professeur);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($professeur->id),
            ],
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
            'date_naissance' => 'required|date|before:today',
            'date_embauche' => 'required|date|before_or_equal:today',
            'pourcentage_remuneration' => 'required|numeric|min:0|max:100',
            'status' => ['required', 'in:actif,inactif'],
            'matieres' => 'required|array|min:1',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Mettre à jour le professeur
        $professeur->update([
            'name' => $validated['nom'] . ' ' . $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'date_naissance' => $validated['date_naissance'],
            'date_embauche' => $validated['date_embauche'],
            'pourcentage_remuneration' => $validated['pourcentage_remuneration'],
            'status' => $validated['status'],
        ]);

        // Mettre à jour les matières du professeur
        $matieresData = [];
        foreach ($validated['matieres'] as $matiereId) {
            $matieresData[$matiereId] = [
                'est_responsable' => true,
                'date_debut' => now(),
            ];
        }
        $professeur->matieresEnseignees()->sync($matieresData);

        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Informations du professeur mises à jour avec succès.');
    }

    /**
     * Affiche le formulaire de changement de mot de passe
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    public function editPassword(User $professeur)
    {
        $this->authorize('update', $professeur);
        
        return view('admin.professeurs.password', compact('professeur'));
    }

    /**
     * Met à jour le mot de passe du professeur
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, User $professeur)
    {
        $this->authorize('update', $professeur);
        
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $professeur->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Désactive un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(User $professeur)
    {
        $this->authorize('delete', $professeur);
        
        $professeur->update(['status' => 'inactif']);
        
        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Le professeur a été désactivé avec succès.');
    }

    /**
     * Réactive un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(User $professeur)
    {
        $this->authorize('update', $professeur);
        
        $professeur->update(['status' => 'actif']);
        
        return redirect()->route('admin.professeurs.show', $professeur)
            ->with('success', 'Le professeur a été réactivé avec succès.');
    }

    /**
     * Affiche l'historique des salaires d'un professeur
     *
     * @param  \App\Models\User  $professeur
     * @return \Illuminate\View\View
     */
    public function salaires(User $professeur)
    {
        $this->authorize('view', $professeur);
        
        $salaires = $professeur->salaires()
            ->orderBy('periode_debut', 'desc')
            ->paginate(12);
            
        return view('admin.professeurs.salaires', compact('professeur', 'salaires'));
    }

    /**
     * Affiche les détails d'un salaire
     *
     * @param  \App\Models\User  $professeur
     * @param  \App\Models\Salaire  $salaire
     * @return \Illuminate\View\View
     */
    public function showSalaire(User $professeur, Salaire $salaire)
    {
        $this->authorize('view', $professeur);
        
        if ($salaire->professeur_id !== $professeur->id) {
            abort(404);
        }
        
        return view('admin.professeurs.salaire-show', compact('professeur', 'salaire'));
    }
}
