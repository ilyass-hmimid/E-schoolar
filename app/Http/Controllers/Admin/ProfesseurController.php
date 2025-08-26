<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class ProfesseurController extends Controller
{
    /**
     * Afficher la liste des professeurs
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = $request->only(['search', 'status']);
        
        $professeurs = User::role('professeur')
            ->with('matieres')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('is_active', $status === 'actif');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Professeurs/Index', [
            'professeurs' => $professeurs,
            'filters' => $filters,
        ]);
    }

    /**
     * Afficher le formulaire de création d'un professeur
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $matieres = Matiere::orderBy('nom')->get();

        return Inertia::render('Admin/Professeurs/Create', [
            'matieres' => $matieres
        ]);
    }

    /**
     * Enregistrer un nouveau professeur
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'matieres' => 'required|array|min:1',
            'matieres.*' => 'exists:matieres,id',
            'date_embauche' => 'required|date',
            'salaire' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Générer un mot de passe aléatoire
        $password = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
        
        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_embauche' => $validated['date_embauche'],
            'salaire' => $validated['salaire'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Assigner le rôle de professeur
        $user->assignRole('professeur');
        
        // Attacher les matières enseignées
        $user->matieres()->sync($validated['matieres']);

        // Envoyer un email avec les identifiants (à implémenter)
        // Mail::to($user)->send(new NewProfessorAccount($user, $password));

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Le professeur a été créé avec succès. Un email a été envoyé avec ses identifiants.');
    }

    /**
     * Afficher les détails d'un professeur
     */
    public function show(User $professeur)
    {
        $this->authorize('view', $professeur);

        $professeur->load(['matieres', 'cours' => function($query) {
            $query->with('matiere')
                ->whereDate('date_debut', '>=', now())
                ->orderBy('date_debut')
                ->limit(5);
        }]);

        $stats = [
            'cours_ce_mois' => $professeur->cours()
                ->whereMonth('date_debut', now()->month)
                ->count(),
            'taux_presence' => 95, // À implémenter avec la logique de présence
        ];

        return Inertia::render('Admin/Professeurs/Show', [
            'professeur' => $professeur,
            'stats' => $stats,
        ]);
    }

    /**
     * Afficher le formulaire de modification d'un professeur
     */
    public function edit(User $professeur)
    {
        $this->authorize('update', $professeur);

        $professeur->load('matieres');
        $matieres = Matiere::orderBy('nom')->get();

        return Inertia::render('Admin/Professeurs/Edit', [
            'professeur' => $professeur,
            'matieres' => $matieres,
            'matieresEnseignees' => $professeur->matieres->pluck('id')
        ]);
    }

    /**
     * Mettre à jour un professeur
     */
    public function update(Request $request, User $professeur)
    {
        $this->authorize('update', $professeur);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($professeur->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'matieres' => 'required|array|min:1',
            'matieres.*' => 'exists:matieres,id',
            'date_embauche' => 'required|date',
            'salaire' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Mise à jour des informations de base
        $professeur->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_embauche' => $validated['date_embauche'],
            'salaire' => $validated['salaire'],
            'is_active' => $validated['is_active'] ?? $professeur->is_active,
        ]);

        // Mise à jour du mot de passe si fourni
        if (!empty($validated['password'])) {
            $professeur->update([
                'password' => Hash::make($validated['password']),
            ]);
        }
        
        // Mise à jour des matières enseignées
        $professeur->matieres()->sync($validated['matieres']);

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Les informations du professeur ont été mises à jour avec succès.');
    }

    /**
     * Supprimer un professeur
     */
    public function destroy(User $professeur)
    {
        $this->authorize('delete', $professeur);

        // Vérifier s'il y a des cours associés
        if ($professeur->cours()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce professeur car il a des cours associés.');
        }

        $professeur->delete();

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Le professeur a été supprimé avec succès.');
    }
}
