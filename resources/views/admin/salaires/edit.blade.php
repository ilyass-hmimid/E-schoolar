@extends('layouts.admin')

@section('title', 'Modifier un salaire')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .required:after {
        content: ' *';
        color: #e53e3e;
    }
    .salaire-field {
        transition: all 0.3s ease;
    }
    .salaire-field:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 1px rgba(66, 153, 225, 0.5);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modifier le salaire</h1>
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
                        <a href="{{ route('admin.salaires.index') }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                            Salaires
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('admin.salaires.show', $salaire) }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                            {{ $salaire->professeur->nom_complet }} - {{ \Carbon\Carbon::parse($salaire->periode)->format('m/Y') }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            Modifier
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Informations sur le salaire
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Modifiez les champs ci-dessous pour mettre à jour ce salaire.
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.salaires.update', $salaire) }}" method="POST">
                @csrf
                @method('PUT')
                
                @include('admin.salaires._form', [
                    'salaire' => $salaire,
                    'professeurs' => collect([$salaire->professeur]) // Passer une collection avec le professeur actuel
                ])
                
                <div class="mt-8 border-t border-gray-200 pt-5">
                    <div class="flex justify-between">
                        <a href="{{ route('admin.salaires.show', $salaire) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </a>
                        <div class="space-x-2">
                            <button type="submit" name="action" value="save" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Enregistrer les modifications
                            </button>
                            <button type="submit" name="action" value="save_and_show" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Enregistrer et afficher
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Section de suppression -->
            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Zone dangereuse
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>
                                    La suppression de ce salaire est irréversible. Toutes les données associées seront définitivement supprimées.
                                </p>
                                <div class="mt-4">
                                    <form action="{{ route('admin.salaires.destroy', $salaire) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce salaire ? Cette action est irréversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <i class="fas fa-trash mr-2"></i>
                                            Supprimer définitivement
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation de flatpickr pour les champs de date
        flatpickr("input[type=date]", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });
</script>
@endpush

@endsection
