<div>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Modifier l'utilisateur: {{ $name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form wire:submit.prevent="update">
                @include('partials.errors')
                <!-- Section Informations de base -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Informations de base</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom complet -->
                        <div class="form-group">
                            <label for="name" class="form-label">Nom complet <span class="text-red-500">*</span></label>
                            <input type="text" id="name" wire:model="name" class="form-control" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" wire:model="email" class="form-control" required>
                        </div>

                        <!-- Mot de passe -->
                        <div class="form-group">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" id="password" wire:model="password" class="form-control">
                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour ne pas modifier</p>
                        </div>

                        <!-- Confirmation du mot de passe -->
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control">
                        </div>

                        <!-- Rôle -->
                        <div class="form-group">
                            <label for="role" class="form-label">Rôle <span class="text-red-500">*</span></label>
                            <select id="role" wire:model="role" class="form-select" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role['value'] }}" {{ $role['value'] === $role ? 'selected' : '' }}>{{ $role['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="form-group flex items-center">
                            <input type="checkbox" id="is_active" wire:model="is_active" class="form-checkbox h-5 w-5 text-blue-600">
                            <label for="is_active" class="ml-2 text-gray-700">Compte actif</label>
                        </div>
                    </div>
                </div>

                <!-- Section Informations de contact -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Informations de contact</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Téléphone -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" id="phone" wire:model="phone" class="form-control">
                        </div>

                        <!-- Adresse -->
                        <div class="form-group">
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text" id="address" wire:model="address" class="form-control">
                        </div>

                        <!-- Ville -->
                        <div class="form-group">
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" id="city" wire:model="city" class="form-control">
                        </div>

                        <!-- Code postal -->
                        <div class="form-group">
                            <label for="postal_code" class="form-label">Code postal</label>
                            <input type="text" id="postal_code" wire:model="postal_code" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Section Informations personnelles -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Informations personnelles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date de naissance -->
                        <div class="form-group">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" id="date_naissance" wire:model="date_naissance" class="form-control">
                        </div>

                        <!-- Lieu de naissance -->
                        <div class="form-group">
                            <label for="lieu_naissance" class="form-label">Lieu de naissance</label>
                            <input type="text" id="lieu_naissance" wire:model="lieu_naissance" class="form-control">
                        </div>

                        <!-- CIN -->
                        <div class="form-group">
                            <label for="cin" class="form-label">CIN</label>
                            <input type="text" id="cin" wire:model="cin" class="form-control">
                        </div>

                        <!-- CNE (pour les élèves) -->
                        @if($role === 'eleve')
                        <div class="form-group">
                            <label for="cne" class="form-label">CNE</label>
                            <input type="text" id="cne" wire:model="cne" class="form-control">
                        </div>
                        @endif

                        <!-- Sexe -->
                        <div class="form-group">
                            <label class="form-label">Sexe</label>
                            <div class="mt-2">
                                <label class="inline-flex items-center mr-4">
                                    <input type="radio" class="form-radio" value="M" wire:model="sexe">
                                    <span class="ml-2">Masculin</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" value="F" wire:model="sexe">
                                    <span class="ml-2">Féminin</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Photo de profil -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Photo de profil</h2>
                    <div class="flex items-center">
                        <div class="mr-6">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" alt="Photo de profil" class="h-32 w-32 rounded-full object-cover">
                            @elseif($existingPhoto)
                                <img src="{{ $existingPhoto }}" alt="Photo de profil actuelle" class="h-32 w-32 rounded-full object-cover">
                            @else
                                <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Aucune photo</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" id="photo" wire:model="photo" class="hidden" accept="image/*">
                            <label for="photo" class="btn btn-secondary cursor-pointer">
                                <i class="fas fa-upload mr-2"></i> Changer la photo
                            </label>
                            <p class="text-sm text-gray-500 mt-2">Taille maximale : 2MB. Formats acceptés : JPG, PNG, GIF</p>
                        </div>
                    </div>
                </div>

                <!-- Section Bio -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">À propos</h2>
                    <div class="form-group">
                        <label for="bio" class="form-label">Biographie</label>
                        <textarea id="bio" wire:model="bio" rows="4" class="form-textarea" placeholder="Quelques mots sur vous..."></textarea>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-between items-center pt-6 border-t">
                    <button type="button" class="btn btn-danger" wire:click="confirmDelete">
                        <i class="fas fa-trash mr-2"></i> Supprimer
                    </button>
                    
                    <div class="space-x-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <x-confirmation-modal wire:model="confirmingUserDeletion">
        <x-slot name="title">
            Supprimer l'utilisateur
        </x-slot>

        <x-slot name="content">
            Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                Annuler
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteUser" wire:loading.attr="disabled">
                Supprimer l'utilisateur
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Gestion des notifications
            window.addEventListener('user-updated', (event) => {
                // Afficher une notification
                const toast = window.Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                
                toast.fire({
                    icon: event.detail.type,
                    title: event.detail.message
                });
            });
        });
    </script>
    @endpush
</div>
