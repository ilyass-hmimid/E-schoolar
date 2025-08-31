<div>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestion des rôles et permissions</h1>
            <button 
                wire:click="create" 
                class="btn btn-primary"
            >
                <i class="fas fa-plus mr-2"></i> Nouveau rôle
            </button>
        </div>

        <!-- Barre de recherche -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live="search"
                    class="pl-10 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Rechercher un rôle..."
                >
            </div>
        </div>

        <!-- Tableau des rôles -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom du rôle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Permissions
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($roles as $role)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $role->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $role->users_count }} utilisateur(s)
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($role->permissions->take(3) as $permission)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $permission->name }}
                                            </span>
                                        @empty
                                            <span class="text-sm text-gray-500">Aucune permission</span>
                                        @endforelse
                                        
                                        @if($role->permissions->count() > 3)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                +{{ $role->permissions->count() - 3 }} plus
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button 
                                        wire:click="edit({{ $role->id }})" 
                                        class="text-blue-600 hover:text-blue-900 mr-3"
                                        title="Modifier"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($role->name !== 'admin')
                                        <button 
                                            wire:click="$dispatch('confirm-delete', { message: 'Êtes-vous sûr de vouloir supprimer ce rôle ?', id: {{ $role->id }} })" 
                                            class="text-red-600 hover:text-red-900"
                                            title="Supprimer"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucun rôle trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $roles->links() }}
            </div>
        </div>
    </div>

    <!-- Modal d'ajout/édition de rôle -->
    <x-dialog-modal wire:model.live="isOpen">
        <x-slot name="title">
            {{ $roleId ? 'Modifier le rôle' : 'Créer un nouveau rôle' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <!-- Nom du rôle -->
                <div>
                    <x-label for="name" value="Nom du rôle" />
                    <x-input 
                        id="name" 
                        type="text" 
                        class="mt-1 block w-full" 
                        wire:model="name" 
                        autofocus 
                        {{ $roleId ? 'readonly' : '' }}
                    />
                    <x-input-error for="name" class="mt-2" />
                </div>

                <!-- Permissions -->
                <div>
                    <x-label value="Permissions" />
                    
                    <div class="mt-2 space-y-2">
                        @foreach($allPermissions->groupBy(function($permission) {
                            return explode('.', $permission->name)[0] ?? 'Général';
                        }) as $group => $permissions)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ ucfirst($group) }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($permissions as $permission)
                                        <label class="inline-flex items-center">
                                            <input 
                                                type="checkbox" 
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                wire:model.live="selectedPermissions"
                                                value="{{ $permission->name }}"
                                            >
                                            <span class="ml-2 text-sm text-gray-600">
                                                {{ $permission->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                Annuler
            </x-secondary-button>

            <x-button class="ml-3" wire:click="store" wire:loading.attr="disabled">
                {{ $roleId ? 'Mettre à jour' : 'Créer' }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Script pour gérer la confirmation de suppression -->
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                @this.on('confirm-delete', (event) => {
                    if (confirm(event.message)) {
                        @this.delete(event.id);
                    }
                });
            });
        </script>
    @endpush
</div>
