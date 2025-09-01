@extends('layouts.admin')

@section('title', 'Créer un nouveau cours')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Créer un nouveau cours</h1>
            <p class="text-gray-400">Remplissez le formulaire pour ajouter un nouveau cours</p>
        </div>
        <a href="{{ route('admin.cours.index') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-dark-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
        <div class="p-6">
            <form action="{{ route('admin.cours.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Titre -->
                    <div class="space-y-2">
                        <label for="titre" class="block text-sm font-medium text-gray-300">
                            Titre du cours <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" id="titre" name="titre" 
                                   class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                   value="{{ old('titre') }}" required>
                        </div>
                        @error('titre')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durée -->
                    <div class="space-y-2">
                        <label for="duree" class="block text-sm font-medium text-gray-300">
                            Durée (en minutes) <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="number" id="duree" name="duree" 
                                   class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                   value="{{ old('duree') }}" min="1" required>
                        </div>
                        @error('duree')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix -->
                    <div class="space-y-2">
                        <label for="prix" class="block text-sm font-medium text-gray-300">
                            Prix (en MAD) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 sm:text-sm">MAD</span>
                            </div>
                            <input type="number" id="prix" name="prix" 
                                   class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 pl-12 pr-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                   value="{{ old('prix') }}" min="0" step="0.01" required>
                        </div>
                        @error('prix')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Niveau -->
                    <div class="space-y-2">
                        <label for="niveau_id" class="block text-sm font-medium text-gray-300">
                            Niveau <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select id="niveau_id" name="niveau_id" 
                                    class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                    required>
                                <option value="">Sélectionnez un niveau</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                        {{ $niveau->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('niveau_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Matière -->
                    <div class="space-y-2">
                        <label for="matiere_id" class="block text-sm font-medium text-gray-300">
                            Matière <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select id="matiere_id" name="matiere_id" 
                                    class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                                    required>
                                <option value="">Sélectionnez une matière</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                        {{ $matiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('matiere_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-300">
                            Description
                        </label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
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
    document.addEventListener('DOMContentLoaded', function() {
        // Scripts supplémentaires si nécessaire
    });
</script>
@endpush

@endsection
