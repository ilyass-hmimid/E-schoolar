<?php

namespace App\Http\Livewire\Admin\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class RolesList extends Component
{
    use WithPagination;

    public $name;
    public $permissions = [];
    public $roleId;
    public $isOpen = false;
    public $search = '';
    public $selectedPermissions = [];
    public $allPermissions = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'selectedPermissions' => 'array',
    ];

    public function mount()
    {
        $this->allPermissions = Permission::orderBy('name')->get();
    }

    public function render()
    {
        $roles = Role::when($this->search, function($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->with('permissions')
        ->orderBy('name')
        ->paginate(10);

        return view('livewire.admin.roles.roles-list', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->roleId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        try {
            $role = Role::updateOrCreate(
                ['id' => $this->roleId],
                ['name' => $this->name, 'guard_name' => 'web']
            );

            $role->syncPermissions($this->selectedPermissions);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $this->roleId ? 'Rôle mis à jour avec succès' : 'Rôle créé avec succès'
            ]);

            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde du rôle', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la sauvegarde du rôle.'
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            // Empêcher la suppression du rôle admin
            if ($role->name === 'admin') {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Le rôle admin ne peut pas être supprimé.'
                ]);
                return;
            }

            // Vérifier si le rôle est utilisé par des utilisateurs
            if ($role->users()->count() > 0) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Ce rôle est attribué à des utilisateurs et ne peut pas être supprimé.'
                ]);
                return;
            }

            $role->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Rôle supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du rôle', [
                'role_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression du rôle.'
            ]);
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->reset([
            'name',
            'selectedPermissions',
            'roleId',
        ]);
    }
}
