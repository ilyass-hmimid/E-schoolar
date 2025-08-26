<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Constructeur du contrôleur
     * Vérifie que l'utilisateur est un administrateur pour toutes les méthodes
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Affiche la liste des utilisateurs
     */
    public function index()
    {
        $users = User::with(['niveau:id,nom', 'filiere:id,nom'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role' => $user->role->value,
                    'role_label' => $user->role->label(),
                    'is_active' => $user->is_active,
                    'niveau' => $user->niveau ? $user->niveau->nom : null,
                    'niveau_id' => $user->niveau_id,
                    'filiere' => $user->filiere ? $user->filiere->nom : null,
                    'filiere_id' => $user->filiere_id,
                    'somme_a_payer' => $user->somme_a_payer,
                    'date_debut' => $user->date_debut ? $user->date_debut->format('Y-m-d') : null,
                    'created_at' => $user->created_at->format('d/m/Y'),
                    'avatar' => $user->avatar,
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users
        ]);
    }

    /**
     * Affiche le formulaire de création d'utilisateur
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => RoleType::forSelect(),
            'niveaux' => \App\Models\Niveau::actifs()->get(['id', 'nom']),
            'filieres' => \App\Models\Filiere::actifs()->get(['id', 'nom']),
        ]);
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(RoleType::cases())],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
            'somme_a_payer' => 'nullable|numeric|min:0',
            'date_debut' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Création de l'utilisateur
        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'niveau_id' => $validated['niveau_id'],
            'filiere_id' => $validated['filiere_id'],
            'somme_a_payer' => $validated['somme_a_payer'] ?? 0,
            'date_debut' => $validated['date_debut'],
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        $user->save();
        
        // Assigner le rôle à l'utilisateur
        $roleName = RoleType::from($validated['role'])->name;
        $user->assignRole($roleName);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche un utilisateur spécifique
     */
    public function show(User $user)
    {
        $user->load(['niveau:id,nom', 'filiere:id,nom']);
        
        return Inertia::render('Admin/Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'is_active' => $user->is_active,
                'niveau' => $user->niveau ? $user->niveau->nom : null,
                'niveau_id' => $user->niveau_id,
                'filiere' => $user->filiere ? $user->filiere->nom : null,
                'filiere_id' => $user->filiere_id,
                'somme_a_payer' => $user->somme_a_payer,
                'date_debut' => $user->date_debut ? $user->date_debut->format('Y-m-d') : null,
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'avatar' => $user->avatar,
            ]
        ]);
    }

    /**
     * Affiche le formulaire de modification d'utilisateur
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role->value,
                'is_active' => $user->is_active,
                'niveau_id' => $user->niveau_id,
                'filiere_id' => $user->filiere_id,
                'somme_a_payer' => $user->somme_a_payer,
                'date_debut' => $user->date_debut ? $user->date_debut->format('Y-m-d') : null,
            ],
            'roles' => RoleType::forSelect(),
            'niveaux' => \App\Models\Niveau::actifs()->get(['id', 'nom']),
            'filieres' => \App\Models\Filiere::actifs()->get(['id', 'nom']),
        ]);
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(RoleType::cases())],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
            'somme_a_payer' => 'nullable|numeric|min:0',
            'date_debut' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'niveau_id' => $validated['niveau_id'],
            'filiere_id' => $validated['filiere_id'],
            'somme_a_payer' => $validated['somme_a_payer'] ?? 0,
            'date_debut' => $validated['date_debut'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        
        // Mettre à jour le rôle avec Spatie Permission
        $roleName = RoleType::from($validated['role'])->name;
        $user->syncRoles([$roleName]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de l'utilisateur connecté
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Vérifier si l'utilisateur a le droit de supprimer des utilisateurs
        if (!auth()->user()->can('delete users')) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }

        // Vérifier s'il s'agit du dernier administrateur
        if ($user->role === RoleType::ADMIN->value) {
            $adminCount = User::where('role', RoleType::ADMIN->value)->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Impossible de supprimer le dernier administrateur.');
            }
        }

        try {
            // Journaliser la suppression
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log('Utilisateur supprimé : ' . $user->name);

            // Supprimer les rôles de l'utilisateur
            $user->roles()->detach();
            
            // Supprimer l'utilisateur
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur supprimé avec succès.');
                
        } catch (\Exception $e) {
            // Journaliser l'erreur
            \Log::error('Erreur lors de la suppression de l\'utilisateur: ' . $e->getMessage());
            
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
    }

    /**
     * API: Récupère les utilisateurs pour les sélecteurs
     */
    public function apiIndex(Request $request)
    {
        $query = User::query();

        // Filtre par rôle
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filtre par statut
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->select(['id', 'name', 'email', 'role'])
            ->orderBy('name')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->label(),
                ];
            });

        return response()->json($users);
    }

    /**
     * API: Récupère les statistiques des utilisateurs
     */
    public function apiStats()
    {
        $stats = [
            'total' => User::count(),
            'admins' => User::admins()->count(),
            'professeurs' => User::professeurs()->count(),
            'assistants' => User::assistants()->count(),
            'eleves' => User::eleves()->count(),
            'actifs' => User::actifs()->count(),
            'inactifs' => User::where('is_active', false)->count(),
        ];

        return response()->json($stats);
    }
}
