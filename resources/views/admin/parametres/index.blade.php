@extends('layouts.admin')

@section('title', 'Paramètres de l\'application')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Paramètres de l'application</h2>
            <p class="text-gray-600 mt-1">Gérez les paramètres généraux de l'application</p>
        </div>

        <form action="{{ route('admin.parametres.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom de l'établissement -->
                <div class="space-y-2">
                    <label for="nom_etablissement" class="block text-sm font-medium text-gray-700">
                        Nom de l'établissement <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom_etablissement" id="nom_etablissement"
                           value="{{ old('nom_etablissement', $parametres['nom_etablissement'] ?? '') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           required>
                    @error('nom_etablissement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email de contact -->
                <div class="space-y-2">
                    <label for="email_contact" class="block text-sm font-medium text-gray-700">
                        Email de contact <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email_contact" id="email_contact"
                           value="{{ old('email_contact', $parametres['email_contact'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           required>
                    @error('email_contact')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div class="space-y-2">
                    <label for="telephone" class="block text-sm font-medium text-gray-700">
                        Téléphone <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="telephone" id="telephone"
                           value="{{ old('telephone', $parametres['telephone'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           required>
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Adresse -->
                <div class="space-y-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-700">
                        Adresse <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="adresse" id="adresse"
                           value="{{ old('adresse', $parametres['adresse'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           required>
                    @error('adresse')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Année scolaire -->
                <div class="space-y-2">
                    <label for="annee_scolaire" class="block text-sm font-medium text-gray-700">
                        Année scolaire <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="annee_scolaire" id="annee_scolaire"
                           value="{{ old('annee_scolaire', $parametres['annee_scolaire'] ?? date('Y').'-'.(date('Y')+1)) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="Ex: 2023-2024"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Format: AAAA-AAAA</p>
                    @error('annee_scolaire')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
