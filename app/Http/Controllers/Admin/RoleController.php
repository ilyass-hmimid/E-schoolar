<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle créé avec succès');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        
        $permissions = isset($validated['permissions']) 
            ? Permission::whereIn('id', $validated['permissions'])->get() 
            : [];
            
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle mis à jour avec succès');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer le rôle administrateur');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle supprimé avec succès');
    }

    public function getRolesJson()
    {
        $roles = Role::select('id', 'name')->get();
        return response()->json($roles);
    }
}
