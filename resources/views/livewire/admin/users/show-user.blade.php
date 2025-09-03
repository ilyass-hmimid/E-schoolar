<div>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Détails de l'utilisateur : {{ $user->name }}
            </h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <!-- User Avatar -->
                <div class="md:w-1/3 p-6 border-b md:border-b-0 md:border-r border-gray-200">
                    <div class="text-center">
                        <div class="relative mx-auto w-32 h-32 rounded-full overflow-hidden bg-gray-100">
                            <img 
                                src="{{ $user->photo_url ?? asset('images/default-avatar.png') }}" 
                                alt="{{ $user->name }}"
                                class="w-full h-full object-cover"
                            >
                        </div>
                        
                        <h2 class="mt-4 text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                        
                        <div class="mt-2">
                            <span class="px-3 py-1 text-sm rounded-full {{ 
                                $user->role === 'admin' ? 'bg-purple-100 text-purple-800' :
                                ($user->role === 'professeur' ? 'bg-blue-100 text-blue-800' :
                                ($user->role === 'assistant' ? 'bg-green-100 text-green-800' :
                                'bg-yellow-100 text-yellow-800'))
                            }}">
                                {{ $user->role_label }}
                            </span>
                            
                            <span class="ml-2 px-3 py-1 text-sm rounded-full {{ 
                                $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        
                        <div class="mt-6 space-y-2">
                            <a 
                                href="{{ route('admin.users.edit', $user->id) }}" 
                                class="btn btn-primary w-full"
                            >
                                <i class="fas fa-edit mr-2"></i> Modifier le profil
                            </a>
                            
                            <button 
                                wire:click="confirmUserDeletion"
                                class="btn btn-danger w-full"
                            >
                                <i class="fas fa-trash mr-2"></i> Supprimer l'utilisateur
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- User Details -->
                <div class="md:w-2/3 p-6">
                    <div class="space-y-6">
                        <!-- Informations personnelles -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                                    Informations personnelles
                                </h3>
                            </div>
                            <div class="p-6">
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="py-2">
                                        <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                    </div>
                                    <div class="py-2">
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:underline">
                                                {{ $user->email }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div class="py-2">
                                        <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->phone ?? 'Non renseigné' }}
                                            @if($user->phone)
                                                <a href="tel:{{ $user->phone }}" class="ml-2 text-blue-600 hover:underline">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="py-2">
                                        <dt class="text-sm font-medium text-gray-500">Date d'inscription</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->created_at->format('d/m/Y H:i') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        
                        <!-- Informations supplémentaires -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                    Informations supplémentaires
                                </h3>
                            </div>
                            <div class="p-6">
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($user->date_naissance)
                                        <div class="py-2">
                                            <dt class="text-sm font-medium text-gray-500">Date de naissance</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($user->date_naissance)->format('d/m/Y') }}
                                                ({{ \Carbon\Carbon::parse($user->date_naissance)->age }} ans)
                                            </dd>
                                        </div>
                                    @endif
                                    
                                    @if($user->lieu_naissance)
                                        <div class="py-2">
                                            <dt class="text-sm font-medium text-gray-500">Lieu de naissance</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $user->lieu_naissance }}</dd>
                                        </div>
                                    @endif
                                    
                                    @if($user->cin)
                                        <div class="py-2">
                                            <dt class="text-sm font-medium text-gray-500">CIN</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $user->cin }}</dd>
                                        </div>
                                    @endif
                                    
                                    @if($user->cne && $user->role === 'eleve')
                                        <div class="py-2">
                                            <dt class="text-sm font-medium text-gray-500">CNE</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $user->cne }}</dd>
                                        </div>
                                    @endif
                                    
                                    @if($user->sexe)
                                        <div class="py-2">
                                            <dt class="text-sm font-medium text-gray-500">Sexe</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                {{ $user->sexe === 'M' ? 'Masculin' : 'Féminin' }}
                                            </dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                        
                            </div>
                        @endif
                        
                        <!-- Informations professionnelles (pour les profs/assistants) -->
                        @if(in_array($user->role, ['professeur', 'assistant']) && ($user->profession || $user->societe || $user->fonction))
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                                        Informations professionnelles
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($user->profession)
                                            <div class="py-2">
                                                <dt class="text-sm font-medium text-gray-500">Profession</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $user->profession }}</dd>
                                            </div>
                                        @endif
                                        
                                        @if($user->societe)
                                            <div class="py-2">
                                                <dt class="text-sm font-medium text-gray-500">Société</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $user->societe }}</dd>
                                            </div>
                                        @endif
                                        
                                        @if($user->fonction)
                                            <div class="py-2">
                                                <dt class="text-sm font-medium text-gray-500">Fonction</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $user->fonction }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Informations académiques (pour les élèves) -->
                        @if($user->role === 'eleve' && ($user->niveau || $user->filiere || $user->etablissement))
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <i class="fas fa-graduation-cap mr-2 text-blue-600"></i>
                                        Informations académiques
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($user->niveau)
                                            <div class="py-2">
                                                <dt class="text-sm font-medium text-gray-500">Niveau</dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    @if(is_object($user->niveau) && isset($user->niveau->nom))
                                                        {{ $user->niveau->nom }}
                                                    @else
                                                        {{ $user->niveau }}
                                                    @endif
                                                </dd>
                                            </div>
                                        @endif
                                        
                                        @if($user->filiere)
                                            <div class="py-2">
                                                <dt class="text-sm font-medium text-gray-500">Filière</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $user->filiere->nom ?? $user->filiere }}</dd>
                                            </div>
                                        @endif
                                        
                                        @if($user->etablissement)
                                            <div class="py-2">
                                                <dt class="text-sm font-medium text-gray-500">Établissement</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $user->etablissement }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Bio -->
                        @if($user->bio)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <i class="fas fa-address-card mr-2 text-blue-600"></i>
                                        À propos
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $user->bio }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <x-confirmation-modal wire:model="confirmingUserDeletion">
        <x-slot name="title">
            Supprimer l'utilisateur
        </x-slot>

        <x-slot name="content">
            Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.
            
            @if($user && $user->role === 'admin')
                <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Cet utilisateur est un administrateur. Assurez-vous qu'il reste au moins un administrateur actif.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
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
</div>
