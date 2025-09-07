@extends('layouts.admin')

@section('title', 'Ajouter un élève')

@section('content')
<div class="bg-dark-800 rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Ajouter un nouvel élève</h2>
        <a href="{{ route('admin.eleves.index') }}" class="text-gray-400 hover:text-white">
            <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.eleves.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-dark-700 rounded-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-user-circle mr-2"></i> Informations de base
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Nom de famille">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Prénom -->
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Prénom">
                    @error('prenom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="email@exemple.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-300 mb-1">Téléphone <span class="text-red-500">*</span></label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="06XXXXXXXX">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Classe -->
                <div>
                    <label for="classe_id" class="block text-sm font-medium text-gray-300 mb-1">Classe <span class="text-red-500">*</span></label>
                    <select name="classe_id" id="classe_id" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Sélectionner une classe</option>
                        @php
                            $classesList = \\App\\Models\\Classe::select('id', 'nom')->orderBy('nom')->get();
                        @endphp
                        @foreach($classesList as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 pt-4">
            <a href="{{ route('admin.eleves.index') }}" class="px-4 py-2 border border-gray-600 rounded-md text-gray-300 hover:bg-gray-700 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                <i class="fas fa-save mr-2"></i> Enregistrer l'élève
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Formater automatiquement le numéro de téléphone
        const phoneInputs = ['telephone'];
        phoneInputs.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) value = value.substring(0, 10);
                    e.target.value = value;
                });
            }
        });
        
        // Mettre en majuscule les noms et prénoms
        const nameInputs = ['nom', 'prenom'];
        nameInputs.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase();
                });
            }
        });
    });
</script>
@endpush
@endsection
