<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Enums\RoleType;

class AdministrateurController extends Controller
{
    /**
     * Afficher la liste des administrateurs
     */
    public function index()
    {
        $adminRole = Role::where('name', RoleType::ADMIN->value)->first();
        $administrateurs = $adminRole ? $adminRole->users()->with('roles')->get() : collect();
        
        return view('admin.administrateurs.index', compact('administrateurs'));
    }

    /**
     * Afficher le formulaire de création d'un administrateur
     */
    public function create()
    {
        return view('admin.administrateurs.create');
    }

    /**
     * Enregistrer un nouvel administrateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole(RoleType::ADMIN->value);

        return redirect()->route('admin.administrateurs.index')
            ->with('success', 'Administrateur créé avec succès.');
    }

    /**
     * Afficher les détails d'un administrateur
     */
    public function show(User $administrateur)
    {
        $this->authorize('view', $administrateur);
        return view('admin.administrateurs.show', compact('administrateur'));
    }

    /**
     * Afficher le formulaire de modification d'un administrateur
     */
    public function edit(User $administrateur)
    {
        $this->authorize('update', $administrateur);
        return view('admin.administrateurs.edit', compact('administrateur'));
    }

    /**
     * Mettre à jour un administrateur
     */
    public function update(Request $request, User $administrateur)
    {
        $this->authorize('update', $administrateur);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $administrateur->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $administrateur->update($data);

        return redirect()->route('admin.administrateurs.index')
            ->with('success', 'Administrateur mis à jour avec succès.');
    }

    /**
     * Supprimer un administrateur
     */
    public function destroy(User $administrateur)
    {
        $this->authorize('delete', $administrateur);
        
        // Empêcher la suppression de son propre compte
        if ($administrateur->id === auth()->id()) {
            return redirect()->route('admin.administrateurs.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $administrateur->delete();

        return redirect()->route('admin.administrateurs.index')
            ->with('success', 'Administrateur supprimé avec succès.');
    }
}
