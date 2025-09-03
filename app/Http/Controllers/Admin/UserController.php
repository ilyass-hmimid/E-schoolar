<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index()
    {
        // Le composant Livewire gère maintenant la logique d'affichage
        return view('admin.users.index');
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Le composant Livewire gère maintenant la logique de création
        return view('admin.users.create');
    }

    /**
     * Créer un nouvel utilisateur (utilisé par l'API ou les requêtes non-Livewire)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|string|in:' . implode(',', [
                RoleType::ADMIN->value,
                RoleType::PROFESSEUR->value,
                RoleType::ASSISTANT->value,
                RoleType::ELEVE->value
            ]),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'date_naissance' => 'nullable|required_if:role,' . RoleType::ELEVE->value . '|date',
            'niveau_id' => 'nullable|exists:niveaux,id',
        ]);

        try {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'date_naissance' => $validated['date_naissance'] ?? null,
                'niveau_id' => $validated['niveau_id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'utilisateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création de l\'utilisateur.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un utilisateur spécifique
     */
    public function show(User $user)
    {
        $user->load([
            'classes', 
            'matieres',
            'niveau' // Charger la relation niveau
        ]);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(User $user)
    {
        // Le composant Livewire gère maintenant la logique d'édition
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur (utilisé par l'API ou les requêtes non-Livewire)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => 'required|string|in:' . implode(',', [
                RoleType::ADMIN->value,
                RoleType::PROFESSEUR->value,
                RoleType::ASSISTANT->value,
                RoleType::ELEVE->value
            ]),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'date_naissance' => 'nullable|required_if:role,' . RoleType::ELEVE->value . '|date',
            'niveau_id' => 'nullable|exists:niveaux,id',
        ]);

        try {
            // Préparer les données à mettre à jour
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $validated['is_active'] ?? $user->is_active,
                'date_naissance' => $validated['date_naissance'] ?? null,
                'niveau_id' => $validated['niveau_id'] ?? null,
            ];

            // Mettre à jour le mot de passe seulement s'il est fourni
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'utilisateur', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        try {
            // Vérifier si c'est une requête AJAX
            if (request()->ajax() || request()->wantsJson()) {
                // Empêcher la suppression de son propre compte
                if ($user->id === auth()->id()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
                    ], 403);
                }

                // Vérifier s'il s'agit du dernier administrateur
                if ($user->role === RoleType::ADMIN->value) {
                    $adminCount = User::where('role', RoleType::ADMIN->value)->count();
                    if ($adminCount <= 1) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Impossible de supprimer le dernier administrateur.'
                        ], 403);
                    }
                }

                $user->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur supprimé avec succès.'
                ]);
            }

            // Pour les requêtes non-AJAX, rediriger avec un message
            $user->delete();
            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Utilisateur supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'utilisateur', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression de l\'utilisateur.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
    }
}
