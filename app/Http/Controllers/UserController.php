<?php

namespace App\Http\Controllers;

use App\Enums\RoleType;
use App\Models\User;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $authorizationService;

    public function __construct(AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
        $this->middleware('permission:manage_users')->except(['profile', 'updateProfile']);
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index()
    {
        $users = User::with('professeur')
            ->latest()
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $this->authorizationService->getUserRoleLabel($user),
                    'role_badge_class' => $this->authorizationService->getUserRoleBadgeClass($user),
                    'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : null,
                    'updated_at' => $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : null,
                ];
            });

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => RoleType::forSelect(),
            'can' => [
                'create' => Auth::user()->can('manage_users'),
                'edit' => Auth::user()->can('manage_users'),
                'delete' => Auth::user()->can('manage_users'),
            ]
        ]);
    }

    /**
     * Afficher le formulaire de création d'un utilisateur
     */
    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => RoleType::forSelect(),
        ]);
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|integer|in:' . implode(',', array_column(RoleType::cases(), 'value')),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => (int)$user->role,
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : null,
                'updated_at' => $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : null,
            ],
            'roles' => RoleType::forSelect(),
        ]);
    }

    /**
     * Mettre à jour un utilisateur existant
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|integer|in:' . implode(',', array_column(RoleType::cases(), 'value')),
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if (Auth::id() === $user->id) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function profile()
    {
        $user = Auth::user();
        
        return Inertia::render('Profile/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $this->authorizationService->getUserRoleLabel($user),
                'role_badge_class' => $this->authorizationService->getUserRoleBadgeClass($user),
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : null,
            ]
        ]);
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => ['required', 'current_password'],
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
