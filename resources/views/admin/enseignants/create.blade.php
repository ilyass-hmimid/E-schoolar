@extends('layouts.admin')

@section('title', 'Ajouter un enseignant')

@section('content')
<div class="bg-dark-900 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Ajouter un nouvel enseignant</h2>
        <a href="{{ route('admin.enseignants.index') }}" class="text-gray-400 hover:text-white">
            <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-dark-800 rounded-xl p-6">
        <form action="{{ route('admin.enseignants.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Informations personnelles -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-white mb-4">Informations personnelles</h3>
                    
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('nom') border-red-500 @enderror">
                        @error('nom')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('prenom') border-red-500 @enderror">
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-300 mb-1">Téléphone</label>
                        <input type="tel" name="telephone" id="telephone" value="{{ old('telephone') }}"
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('telephone') border-red-500 @enderror">
                        @error('telephone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_naissance" class="block text-sm font-medium text-gray-300 mb-1">Date de naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance') }}"
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('date_naissance') border-red-500 @enderror">
                        @error('date_naissance')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-300 mb-1">Adresse</label>
                        <textarea name="adresse" id="adresse" rows="2"
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('adresse') border-red-500 @enderror">{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-white mb-4">Informations professionnelles</h3>
                    
                    <div>
                        <label for="specialite" class="block text-sm font-medium text-gray-300 mb-1">Spécialité <span class="text-red-500">*</span></label>
                        <input type="text" name="specialite" id="specialite" value="{{ old('specialite') }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('specialite') border-red-500 @enderror">
                        @error('specialite')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="diplome" class="block text-sm font-medium text-gray-300 mb-1">Diplôme le plus élevé <span class="text-red-500">*</span></label>
                        <input type="text" name="diplome" id="diplome" value="{{ old('diplome') }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('diplome') border-red-500 @enderror">
                        @error('diplome')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-6">
                        <h3 class="text-lg font-medium text-white mb-4">Informations de connexion</h3>
                        <p class="text-sm text-gray-400 mb-4">Un email sera envoyé à l'enseignant avec ses informations de connexion. Le mot de passe par défaut sera "password".</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-dark-700">
                <a href="{{ route('admin.enseignants.index') }}" class="px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-dark-700 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Enregistrer l'enseignant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
