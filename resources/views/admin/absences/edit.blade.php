@extends('layouts.admin')

@section('title', 'Modifier une absence')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modifier une absence</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                        <i class="fas fa-home mr-2"></i>
                        Tableau de bord
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('admin.absences.index') }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                            Absences
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            Modifier l'absence
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Modification de l'absence
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Modifiez les informations de cette absence.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.absences.update', $absence) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Informations sur l'élève -->
                    <div class="sm:col-span-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Informations sur l'élève</h4>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $absence->eleve->photo_url ?? asset('images/default-avatar.png') }}" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $absence->eleve->nom_complet }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $absence->eleve->classe->nom ?? 'Classe non définie' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur le cours -->
                    <div class="sm:col-span-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Détails du cours</h4>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Matière</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absence->cours->matiere->nom ?? 'Non spécifiée' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Professeur</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absence->cours->professeur->nom_complet ?? 'Non spécifié' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date du cours</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absence->cours->date_cours->format('d/m/Y H:i') ?? 'Non spécifiée' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date de l'absence</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absence->date_absence->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Justificatif -->
                    <div class="sm:col-span-6">
                        <label for="justificatif" class="block text-sm font-medium text-gray-700">
                            Justificatif
                        </label>
                        <div class="mt-1">
                            <textarea id="justificatif" name="justificatif" rows="4"
                                class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('justificatif', $absence->justificatif) }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Détails sur le justificatif (facultatif).
                        </p>
                        @error('justificatif')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut de justification -->
                    <div class="sm:col-span-6">
                        <div class="flex items-center">
                            <input id="justifiee" name="justifiee" type="checkbox" value="1"
                                {{ old('justifiee', $absence->justifiee) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="justifiee" class="ml-2 block text-sm text-gray-700">
                                Marquer comme justifiée
                            </label>
                        </div>
                        @error('justifiee')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-5">
                    <div class="flex justify-between">
                        <a href="{{ route('admin.absences.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                        </a>
                        <div class="space-x-3">
                            <button type="submit" name="action" value="save" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
