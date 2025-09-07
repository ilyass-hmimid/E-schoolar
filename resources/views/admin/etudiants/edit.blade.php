@extends('admin.layout')

@section('title', 'Modifier l\'étudiant : ' . $etudiant->name)

@section('header')
    <h1 class="text-2xl font-bold">
        <a href="{{ route('admin.etudiants.index') }}" class="text-indigo-600 hover:text-indigo-900">Étudiants</a>
        <span class="text-gray-400 mx-2">/</span>
        <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="text-indigo-600 hover:text-indigo-900">{{ $etudiant->name }}</a>
        <span class="text-gray-400 mx-2">/</span>
        Modifier
    </h1>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('admin.etudiants.update', $etudiant) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Informations de base -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                        
                        <!-- Photo de profil -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Photo de profil
                            </label>
                            <div class="flex items-center">
                                <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if($etudiant->photo)
                                        <img src="{{ asset('storage/' . $etudiant->photo) }}" alt="{{ $etudiant->name }}" class="h-full w-full object-cover">
                                    @else
                                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <label for="photo" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Changer la photo
                                    </label>
                                    <input id="photo" name="photo" type="file" class="sr-only">
                                    @if($etudiant->photo)
                                        <div class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="supprimer_photo" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50">
                                                <span class="ml-2 text-sm text-gray-600">Supprimer la photo actuelle</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('photo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Nom complet -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name', $etudiant->name) }}" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="email" name="email" id="email" value="{{ old('email', $etudiant->email) }}" 
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                           required>
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-700">
                                    Téléphone
                                </label>
                                <div class="mt-1">
                                    <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $etudiant->telephone) }}" 
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('telephone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <!-- Date de naissance -->
                            <div>
                                <label for="date_naissance" class="block text-sm font-medium text-gray-700">
                                    Date de naissance
                                </label>
                                <div class="mt-1">
                                    <input type="date" name="date_naissance" id="date_naissance" 
                                           value="{{ old('date_naissance', optional($etudiant->date_naissance)->format('Y-m-d')) }}" 
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('date_naissance')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lieu de naissance -->
                            <div>
                                <label for="lieu_naissance" class="block text-sm font-medium text-gray-700">
                                    Lieu de naissance
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="lieu_naissance" id="lieu_naissance" 
                                           value="{{ old('lieu_naissance', $etudiant->lieu_naissance) }}" 
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('lieu_naissance')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Informations des parents -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations des parents</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom du père -->
                            <div>
                                <label for="nom_pere" class="block text-sm font-medium text-gray-700">
                                    Nom du père
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="nom_pere" id="nom_pere" value="{{ old('nom_pere', $etudiant->nom_pere) }}" 
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('nom_pere')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nom de la mère -->
                            <div>
                                <label for="nom_mere" class="block text-sm font-medium text-gray-700">
                                    Nom de la mère
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="nom_mere" id="nom_mere" value="{{ old('nom_mere', $etudiant->nom_mere) }}" 
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('nom_mere')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations supplémentaires</h3>
                        
                        <!-- Adresse -->
                        <div class="mb-6">
                            <label for="adresse" class="block text-sm font-medium text-gray-700">
                                Adresse
                            </label>
                            <div class="mt-1">
                                <textarea id="adresse" name="adresse" rows="3" 
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('adresse', $etudiant->adresse) }}</textarea>
                            </div>
                            @error('adresse')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remarques -->
                        <div>
                            <label for="remarques" class="block text-sm font-medium text-gray-700">
                                Remarques
                            </label>
                            <div class="mt-1">
                                <textarea id="remarques" name="remarques" rows="3" 
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('remarques', $etudiant->remarques) }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Toutes informations supplémentaires concernant l'étudiant.
                            </p>
                            @error('remarques')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="est_actif" name="est_actif" type="checkbox" 
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                   {{ old('est_actif', $etudiant->est_actif) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="est_actif" class="font-medium text-gray-700">Étudiant actif</label>
                            <p class="text-gray-500">Désactivez cette option pour désactiver le compte de l'étudiant.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>

    <!-- Section pour le mot de passe -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Changer le mot de passe
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Laissez ces champs vides pour conserver le mot de passe actuel.
            </p>
        </div>
        <form action="{{ route('admin.etudiants.update-password', $etudiant) }}" method="POST" class="px-4 py-5 sm:p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                        <div class="mt-1">
                            <input type="password" name="password" id="password" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                        <div class="mt-1">
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Mettre à jour le mot de passe
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Section de suppression -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Zone dangereuse
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Les actions dans cette section sont irréversibles.
            </p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="text-md font-medium text-gray-900">Supprimer cet étudiant</h4>
                    <p class="text-sm text-gray-500">
                        Cette action est irréversible. Toutes les données associées à cet étudiant seront définitivement supprimées.
                    </p>
                </div>
                <form action="{{ route('admin.etudiants.destroy', $etudiant) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet étudiant ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
