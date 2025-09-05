@extends('layouts.app')

@section('title', 'Modifier la classe')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Modifier la classe</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Modifiez les informations de la classe ci-dessous.
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour à la liste
                </a>
                <form action="{{ route('admin.classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-2"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('admin.classes.update', $classe) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom de la classe</label>
                                <input type="text" name="nom" id="nom" value="{{ old('nom', $classe->nom) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('nom')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="niveau" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Niveau</label>
                                <select id="niveau" name="niveau" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="6ème" {{ old('niveau', $classe->niveau) == '6ème' ? 'selected' : '' }}>6ème</option>
                                    <option value="5ème" {{ old('niveau', $classe->niveau) == '5ème' ? 'selected' : '' }}>5ème</option>
                                    <option value="4ème" {{ old('niveau', $classe->niveau) == '4ème' ? 'selected' : '' }}>4ème</option>
                                    <option value="3ème" {{ old('niveau', $classe->niveau) == '3ème' ? 'selected' : '' }}>3ème</option>
                                    <option value="2nde" {{ old('niveau', $classe->niveau) == '2nde' ? 'selected' : '' }}>2nde</option>
                                    <option value="1ère" {{ old('niveau', $classe->niveau) == '1ère' ? 'selected' : '' }}>1ère</option>
                                    <option value="Tle" {{ old('niveau', $classe->niveau) == 'Tle' ? 'selected' : '' }}>Terminale</option>
                                </select>
                                @error('niveau')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $classe->description) }}</textarea>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="professeur_principal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Professeur principal</label>
                                <select id="professeur_principal_id" name="professeur_principal_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Sélectionnez un professeur</option>
                                    @foreach($professeurs as $professeur)
                                        <option value="{{ $professeur->id }}" {{ old('professeur_principal_id', $classe->professeur_principal_id) == $professeur->id ? 'selected' : '' }}>
                                            {{ $professeur->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('professeur_principal_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                        <div class="flex justify-end">
                            <a href="{{ route('admin.classes.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                                Annuler
                            </a>
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Mettre à jour
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
