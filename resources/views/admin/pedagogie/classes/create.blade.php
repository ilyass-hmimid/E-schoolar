@extends('layouts.admin')

@section('title', 'Créer une classe')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Créer une nouvelle classe</h1>
            <p class="text-gray-400">Remplissez les informations pour créer une nouvelle classe</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- Messages d'erreur -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-900 bg-opacity-30 border-l-4 border-red-500">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-200">
                            Il y a {{ $errors->count() }} erreur(s) dans le formulaire
                        </h3>
                        <div class="mt-2 text-sm text-red-300">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Carte du formulaire -->
        <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
            <div class="px-6 py-4 border-b border-dark-700 bg-gradient-to-r from-dark-800 to-dark-900">
                <h2 class="text-lg font-medium text-white">Informations de la classe</h2>
                <p class="mt-1 text-sm text-gray-400">Renseignez les détails de la nouvelle classe</p>
            </div>
            
            <div class="p-6">
                @include('admin.pedagogie.classes.form', [
                    'method' => 'POST',
                    'action' => route('admin.classes.store'),
                    'niveaux' => $niveaux,
                    'professeurs' => $professeurs,
                    'salles' => $salles
                ])
            </div>
        </div>
        
        <!-- Informations complémentaires -->
        <div class="mt-6 bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700 p-6">
            <h3 class="text-lg font-medium text-white mb-4">Conseils pour créer une classe</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-500 bg-opacity-20 flex items-center justify-center text-primary-400">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-white">Nom de la classe</h4>
                            <p class="mt-1 text-sm text-gray-400">Utilisez un nom clair et descriptif, par exemple "Terminale S1" ou "Première ES2".</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-500 bg-opacity-20 flex items-center justify-center text-primary-400">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-white">Année scolaire</h4>
                            <p class="mt-1 text-sm text-gray-400">Sélectionnez l'année scolaire concernée. Exemple: 2023-2024.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-500 bg-opacity-20 flex items-center justify-center text-primary-400">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-white">Professeur principal</h4>
                            <p class="mt-1 text-sm text-gray-400">Le professeur principal peut être ajouté ultérieurement si nécessaire.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-500 bg-opacity-20 flex items-center justify-center text-primary-400">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-white">Élèves</h4>
                            <p class="mt-1 text-sm text-gray-400">Vous pourrez ajouter des élèves après avoir créé la classe.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scripts spécifiques à la page de création
    });
</script>
@endpush

@endsection
