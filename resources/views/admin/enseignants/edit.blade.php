@extends('layouts.admin')

@section('title', 'Modifier un enseignant')

@section('content')
<div class="bg-dark-900 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Modifier l'enseignant</h2>
        <a href="{{ route('admin.enseignants.show', $enseignant->id) }}" class="text-gray-400 hover:text-white">
            <i class="fas fa-arrow-left mr-1"></i> Retour au profil
        </a>
    </div>

    <div class="bg-dark-800 rounded-xl p-6">
        <form action="{{ route('admin.enseignants.update', $enseignant->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Informations personnelles -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-white mb-4">Informations personnelles</h3>
                    
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $enseignant->user->nom) }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('nom') border-red-500 @enderror">
                        @error('nom')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $enseignant->user->prenom) }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('prenom') border-red-500 @enderror">
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $enseignant->user->email) }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-300 mb-1">Téléphone</label>
                        <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $enseignant->user->telephone) }}"
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('telephone') border-red-500 @enderror">
                        @error('telephone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_naissance" class="block text-sm font-medium text-gray-300 mb-1">Date de naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance', $enseignant->user->date_naissance ? $enseignant->user->date_naissance->format('Y-m-d') : '') }}"
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('date_naissance') border-red-500 @enderror">
                        @error('date_naissance')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-300 mb-1">Adresse</label>
                        <textarea name="adresse" id="adresse" rows="2"
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('adresse') border-red-500 @enderror">{{ old('adresse', $enseignant->user->adresse) }}</textarea>
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
                        <input type="text" name="specialite" id="specialite" value="{{ old('specialite', $enseignant->specialite) }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('specialite') border-red-500 @enderror">
                        @error('specialite')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="diplome" class="block text-sm font-medium text-gray-300 mb-1">Diplôme le plus élevé <span class="text-red-500">*</span></label>
                        <input type="text" name="diplome" id="diplome" value="{{ old('diplome', $enseignant->diplome) }}" required
                            class="w-full bg-dark-700 border border-dark-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('diplome') border-red-500 @enderror">
                        @error('diplome')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-6">
                        <h3 class="text-lg font-medium text-white mb-4">Réinitialisation du mot de passe</h3>
                        <div class="bg-dark-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-300 mb-3">Laissez ces champs vides pour conserver le mot de passe actuel.</p>
                            
                            <div class="mb-3">
                                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Nouveau mot de passe</label>
                                <input type="password" name="password" id="password"
                                    class="w-full bg-dark-600 border border-dark-500 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full bg-dark-600 border border-dark-500 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-dark-700">
                <a href="{{ route('admin.enseignants.show', $enseignant->id) }}" class="px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-dark-700 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Enregistrer les modifications
                </button>
            </div>
        </form>

        <!-- Section de suppression -->
        <div class="mt-8 pt-6 border-t border-dark-700">
            <h3 class="text-lg font-medium text-red-400 mb-4">Zone dangereuse</h3>
            <div class="bg-red-900 bg-opacity-20 border border-red-800 rounded-lg p-4">
                <p class="text-sm text-red-300 mb-4">
                    La suppression d'un enseignant est irréversible. Toutes les données associées (cours, notes, etc.) seront également supprimées.
                </p>
                <form action="{{ route('admin.enseignants.destroy', $enseignant->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet enseignant ? Cette action est irréversible.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        <i class="fas fa-trash-alt mr-2"></i> Supprimer cet enseignant
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
