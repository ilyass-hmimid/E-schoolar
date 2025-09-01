@extends('layouts.admin')

@section('title', 'Ajouter un assistant')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Ajouter un assistant</h1>
            <p class="text-gray-400">Remplissez le formulaire pour ajouter un nouvel assistant</p>
        </div>
        <a href="{{ route('admin.assistants.index') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
        <div class="p-6">
            <form action="{{ route('admin.assistants.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations personnelles -->
                    <div class="space-y-2">
                        <label for="nom" class="block text-sm font-medium text-gray-300">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nom" name="nom" 
                               class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                               value="{{ old('nom') }}" required>
                        @error('nom')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="prenom" class="block text-sm font-medium text-gray-300">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="prenom" name="prenom" 
                               class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                               value="{{ old('prenom') }}" required>
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-300">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" 
                               class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="telephone" class="block text-sm font-medium text-gray-300">
                            Téléphone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="telephone" name="telephone" 
                               class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                               value="{{ old('telephone') }}" required>
                        @error('telephone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="date_naissance" class="block text-sm font-medium text-gray-300">
                            Date de naissance <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_naissance" name="date_naissance" 
                               class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                               value="{{ old('date_naissance') }}" required>
                        @error('date_naissance')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="centre_id" class="block text-sm font-medium text-gray-300">
                            Centre <span class="text-red-500">*</span>
                        </label>
                        <select id="centre_id" name="centre_id" 
                                class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                required>
                            <option value="">Sélectionnez un centre</option>
                            @foreach($centres as $centre)
                                <option value="{{ $centre->id }}" {{ old('centre_id') == $centre->id ? 'selected' : '' }}>
                                    {{ $centre->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('centre_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="adresse" class="block text-sm font-medium text-gray-300">
                            Adresse <span class="text-red-500">*</span>
                        </label>
                        <textarea id="adresse" name="adresse" rows="2"
                                  class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                  required>{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="photo" class="block text-sm font-medium text-gray-300">
                            Photo de profil
                        </label>
                        <div class="mt-1 flex items-center">
                            <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-dark-700">
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <button type="button" class="ml-5 bg-dark-700 py-2 px-3 border border-dark-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-300 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Changer
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-dark-700 mt-8">
                    <h3 class="text-lg font-medium text-white mb-4">Informations de connexion</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-300">
                                Mot de passe <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" 
                                   class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                   required>
                            @error('password')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-300">
                                Confirmer le mot de passe <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                   required>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-dark-700 mt-8">
                    <button type="reset" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-undo mr-2"></i> Réinitialiser
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script pour la prévisualisation de la photo de profil
    document.addEventListener('DOMContentLoaded', function() {
        // Ajoutez ici les scripts nécessaires pour la gestion de la photo de profil
    });
</script>
@endpush

@endsection
