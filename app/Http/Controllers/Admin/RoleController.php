<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Afficher la liste des rôles
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Afficher le formulaire de création d'un rôle
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Enregistrer un nouveau rôle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role = Role::create(['name' => $validated['name']]);
            
            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rôle créé avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du rôle', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du rôle.');
        }
    }

    /**
     * Afficher un rôle spécifique
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Afficher le formulaire d'édition d'un rôle
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $role->load('permissions');
        
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Mettre à jour un rôle
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role->update(['name' => $validated['name']]);
            
            $permissions = isset($validated['permissions']) 
                ? Permission::whereIn('id', $validated['permissions'])->get()
                : [];
                
            $role->syncPermissions($permissions);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rôle mis à jour avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du rôle', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du rôle.');
        }
    }

    /**
     * Supprimer un rôle
     */
    public function destroy(Role $role)
    {
        try {
            // Empêcher la suppression du rôle admin
            if ($role->name === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Le rôle admin ne peut pas être supprimé.'
                ], 403);
            }

            // Vérifier si le rôle est utilisé par des utilisateurs
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce rôle est attribué à des utilisateurs et ne peut pas être supprimé.'
                ], 403);
            }

            $role->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rôle supprimé avec succès.'
                ]);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rôle supprimé avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du rôle', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression du rôle.'
                ], 500);
            }

            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression du rôle.');
        }
    }

    /**
     * Obtenir les rôles au format JSON (pour les requêtes AJAX)
     */
    public function getRolesJson()
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get()
            ->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name'),
                    'users_count' => $role->users()->count(),
                ];
            });

        return response()->json($roles);
    }
}
