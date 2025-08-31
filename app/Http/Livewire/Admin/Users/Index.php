<?php

namespace App\Http\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'sortField',
        'sortDirection',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $query = User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->roleFilter);
                });
            });
            
        $users = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        $roles = Role::all();
            
        return view('livewire.admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
