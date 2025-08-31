<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Gestion des Utilisateurs</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un Utilisateur
            </a>
        </div>
        
        <div class="card-body">
            <!-- Filtres et recherche -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Rechercher..."
                            wire:model.live.debounce.300ms="search"
                        >
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-control" wire:model.live="roleFilter">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" wire:model.live="perPage">
                        <option value="10">10 par page</option>
                        <option value="25">25 par page</option>
                        <option value="50">50 par page</option>
                        <option value="100">100 par page</option>
                    </select>
                </div>
            </div>

            <!-- Tableau des utilisateurs -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nom
                                @if($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th>Email</th>
                            <th>Rôles</th>
                            <th>Dernière connexion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-danger"
                                            onclick="confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?') || event.stopImmediatePropagation()"
                                            wire:click="$dispatch('deleteUser', { userId: {{ $user->id }} })"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucun utilisateur trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                @this.on('deleteUser', async (event) => {
                    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                        try {
                            const response = await fetch(`/admin/users/${event.userId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            
                            if (response.ok) {
                                Livewire.dispatch('userDeleted');
                                alert('Utilisateur supprimé avec succès');
                            } else {
                                const data = await response.json();
                                throw new Error(data.message || 'Erreur lors de la suppression');
                            }
                        } catch (error) {
                            console.error('Erreur:', error);
                            alert(error.message || 'Une erreur est survenue');
                        }
                    }
                });
            });
        </script>
    @endpush
</div>
