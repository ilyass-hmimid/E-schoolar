@extends('layouts.admin')

@section('title', 'Modifier un assistant')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Modifier l'assistant</h1>
            <p class="text-gray-400">Mettez à jour les informations de l'assistant</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <a href="{{ route('admin.assistants.show', $assistant->id) }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-eye mr-2"></i> Voir
            </a>
            <a href="{{ route('admin.assistants.index') }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
        <div class="p-6">
            <form action="{{ route('admin.assistants.update', $assistant->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations personnelles -->
                    <div class="space-y-2">
                        <label for="nom" class="block text-sm font-medium text-gray-300">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nom" name="nom" 
                               class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                               value="{{ old('nom', $assistant->nom) }}" required>
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
                               value="{{ old('prenom', $assistant->prenom) }}" required>
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
                               value="{{ old('email', $assistant->email) }}" required>
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
                               value="{{ old('telephone', $assistant->telephone) }}" required>
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
                               value="{{ old('date_naissance', $assistant->date_naissance ? $assistant->date_naissance->format('Y-m-d') : '') }}" required>
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
                                <option value="{{ $centre->id }}" {{ old('centre_id', $assistant->centre_id) == $centre->id ? 'selected' : '' }}>
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
                                  required>{{ old('adresse', $assistant->adresse) }}</textarea>
                        @error('adresse')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="photo" class="block text-sm font-medium text-gray-300">
                            Photo de profil
                        </label>
                        <div class="mt-1 flex items-center">
                            @if($assistant->photo)
                                <img src="{{ asset('storage/' . $assistant->photo) }}" alt="Photo de profil" class="h-16 w-16 rounded-full object-cover">
                            @else
                                <div class="h-16 w-16 rounded-full bg-primary-500 flex items-center justify-center text-2xl font-bold text-white">
                                    {{ substr($assistant->prenom, 0, 1) }}{{ substr($assistant->nom, 0, 1) }}
                                </div>
                            @endif
                            <button type="button" class="ml-5 bg-dark-700 py-2 px-3 border border-dark-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-300 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Changer
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-dark-700 mt-8">
                    <h3 class="text-lg font-medium text-white mb-4">Modifier le mot de passe</h3>
                    <p class="text-sm text-gray-400 mb-4">Laissez ces champs vides pour conserver le mot de passe actuel.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-300">
                                Nouveau mot de passe
                            </label>
                            <input type="password" id="password" name="password" 
                                   class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                   autocomplete="new-password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-300">
                                Confirmer le mot de passe
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                   autocomplete="new-password">
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-dark-700 mt-8">
                    <button type="button" onclick="window.location.href='{{ route('admin.assistants.show', $assistant->id) }}'" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-times mr-2"></i> Annuler
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
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
