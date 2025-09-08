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

    <form action="{{ route('admin.eleves.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
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
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Nom de famille">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Prénom -->
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Prénom">
                    @error('prenom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="email@exemple.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-300 mb-1">Téléphone <span class="text-red-500">*</span></label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="06XXXXXXXX">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                
                <!-- Date de naissance -->
                <div>
                    <label for="date_naissance" class="block text-sm font-medium text-gray-300 mb-1">Date de naissance <span class="text-red-500">*</span></label>
                    <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance') }}" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('date_naissance')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Lieu de naissance -->
                <div>
                    <label for="lieu_naissance" class="block text-sm font-medium text-gray-300 mb-1">Lieu de naissance <span class="text-red-500">*</span></label>
                    <input type="text" name="lieu_naissance" id="lieu_naissance" value="{{ old('lieu_naissance') }}" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Ville de naissance">
                    @error('lieu_naissance')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-300 mb-1">Adresse <span class="text-red-500">*</span></label>
                    <textarea name="adresse" id="adresse" rows="2" required
                              class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="Adresse complète">{{ old('adresse') }}</textarea>
                    @error('adresse')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Niveau -->
                <div>
                    <label for="niveau_id" class="block text-sm font-medium text-gray-300 mb-1">Niveau <span class="text-red-500">*</span></label>
                    <select name="niveau_id" id="niveau_id" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent appearance-none"
                           style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'><path stroke=\'%234b5563\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/></svg>'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;">
                        <option value="">Sélectionner un niveau</option>
                        @foreach($niveaux as $id => $nom)
                            <option value="{{ $id }}" {{ old('niveau_id') == $id ? 'selected' : '' }} class="text-gray-900">
                                {{ $nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('niveau_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Filière -->
                <div>
                    <label for="filiere_id" class="block text-sm font-medium text-gray-300 mb-1">Filière <span class="text-red-500">*</span></label>
                    <select name="filiere_id" id="filiere_id" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent appearance-none"
                           style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'><path stroke=\'%234b5563\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/></svg>'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;">
                        <option value="">Sélectionner une filière</option>
                        @foreach($filieres as $id => $nom)
                            <option value="{{ $id }}" {{ old('filiere_id') == $id ? 'selected' : '' }} class="text-gray-900">
                                {{ $nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('filiere_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Nom du père -->
                <div>
                    <label for="nom_pere" class="block text-sm font-medium text-gray-300 mb-1">Nom du père <span class="text-red-500">*</span></label>
                    <input type="text" name="nom_pere" id="nom_pere" value="{{ old('nom_pere') }}" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Nom complet du père">
                    @error('nom_pere')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Téléphone du père -->
                <div>
                    <label for="telephone_pere" class="block text-sm font-medium text-gray-300 mb-1">Téléphone du père <span class="text-red-500">*</span></label>
                    <input type="text" name="telephone_pere" id="telephone_pere" value="{{ old('telephone_pere') }}" required
                           class="w-full bg-white border border-gray-600 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="06XXXXXXXX">
                    @error('telephone_pere')
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
        const phoneInputs = ['telephone', 'telephone_pere'];
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
