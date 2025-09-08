@extends('layouts.admin')

@section('title', 'Ajouter un salaire')

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
        <h1 class="text-2xl font-bold text-gray-900">Ajouter un salaire</h1>
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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            Nouveau salaire
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
                Remplissez les champs ci-dessous pour enregistrer un nouveau salaire.
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.salaires.store') }}" method="POST">
                @csrf
                
                <!-- Inclure le formulaire partiel -->
                @include('admin.salaires._form', [
                    'salaire' => null,
                    'professeurs' => $professeurs
                ])
                
                <div class="mt-8 border-t border-gray-200 pt-5">
                    <div class="flex justify-between">
                        <a href="{{ route('admin.salaires.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Enregistrer le salaire
                        </button>
                    </div>
                </div>
            </form>
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
        
        // Le reste du code est maintenant dans le formulaire partiel
    });
</script>
@endpush

@endsection
