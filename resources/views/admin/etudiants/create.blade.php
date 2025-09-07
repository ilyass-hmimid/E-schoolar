@extends('admin.layout')

@section('title', 'Ajouter un nouvel étudiant')

@section('header')
    <h1 class="text-2xl font-bold">
        <a href="{{ route('admin.etudiants.index') }}" class="text-indigo-600 hover:text-indigo-900">Étudiants</a>
        <span class="text-gray-400 mx-2">/</span>
        Ajouter un étudiant
    </h1>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('admin.etudiants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Informations de base -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                        
                        <!-- Nom complet -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
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
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
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
                                    <input type="tel" name="telephone" id="telephone" value="{{ old('telephone') }}" 
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
                                           value="{{ old('date_naissance') }}" 
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
                                           value="{{ old('lieu_naissance') }}" 
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
                                    <input type="text" name="nom_pere" id="nom_pere" value="{{ old('nom_pere') }}" 
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
                                    <input type="text" name="nom_mere" id="nom_mere" value="{{ old('nom_mere') }}" 
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
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('adresse') }}</textarea>
                            </div>
                            @error('adresse')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo de profil -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Photo de profil
                            </label>
                            <div class="mt-1 flex items-center">
                                <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                                <label for="photo" class="ml-5">
                                    <span class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Changer
                                    </span>
                                    <input id="photo" name="photo" type="file" class="sr-only">
                                </label>
                            </div>
                            @error('photo')
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
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('remarques') }}</textarea>
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
                                   {{ old('est_actif', true) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="est_actif" class="font-medium text-gray-700">Étudiant actif</label>
                            <p class="text-gray-500">Désactivez cette option pour désactiver le compte de l'étudiant.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('admin.etudiants.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Enregistrer l'étudiant
                </button>
            </div>
        </form>
    </div>
@endsection
