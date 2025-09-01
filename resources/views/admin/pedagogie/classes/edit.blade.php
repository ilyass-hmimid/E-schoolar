@extends('layouts.admin')

@section('title', 'Modifier la classe')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Modifier la classe</h1>
            <p class="text-gray-400">Mettez à jour les informations de la classe {{ $classe->nom }}</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <a href="{{ route('admin.classes.show', $classe->id) }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-eye mr-2"></i> Voir
            </a>
            <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Retour
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
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-primary-500 bg-opacity-20 flex items-center justify-center text-primary-400 mr-3">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-medium text-white">Modifier les informations</h2>
                        <p class="mt-1 text-sm text-gray-400">Mettez à jour les détails de la classe</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @include('admin.pedagogie.classes.form', [
                    'method' => 'PUT',
                    'action' => route('admin.classes.update', $classe->id),
                    'classe' => $classe,
                    'niveaux' => $niveaux,
                    'professeurs' => $professeurs,
                    'salles' => $salles
                ])
            </div>
        </div>
        
        <!-- Actions supplémentaires -->
        <div class="mt-6 bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
            <div class="px-6 py-4 border-b border-dark-700 bg-gradient-to-r from-dark-800 to-dark-900">
                <h2 class="text-lg font-medium text-white">Actions supplémentaires</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-dark-700 rounded-lg">
                    <div>
                        <h3 class="text-sm font-medium text-white">Changer de professeur principal</h3>
                        <p class="text-sm text-gray-400">Sélectionnez un nouveau professeur principal pour cette classe</p>
                    </div>
                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-exchange-alt mr-1.5"></i> Changer
                    </button>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-dark-700 rounded-lg">
                    <div>
                        <h3 class="text-sm font-medium text-white">Archiver la classe</h3>
                        <p class="text-sm text-gray-400">Cette action désactivera la classe et la masquera des listes</p>
                    </div>
                    <button type="button" onclick="confirm('Êtes-vous sûr de vouloir archiver cette classe ?') && document.getElementById('archive-form').submit()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <i class="fas fa-archive mr-1.5"></i> Archiver
                    </button>
                    <form id="archive-form" action="{{ route('admin.classes.archive', $classe->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="est_active" value="0">
                    </form>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-dark-700 rounded-lg border border-red-900">
                    <div>
                        <h3 class="text-sm font-medium text-white">Supprimer la classe</h3>
                        <p class="text-sm text-gray-400">Cette action est irréversible. Toutes les données associées seront perdues.</p>
                    </div>
                    <button type="button" onclick="if(confirm('Êtes-vous certain de vouloir supprimer définitivement cette classe ? Cette action est irréversible.')) { document.getElementById('delete-form').submit(); }" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-1.5"></i> Supprimer
                    </button>
                    <form id="delete-form" action="{{ route('admin.classes.destroy', $classe->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Historique des modifications -->
        @if($classe->revisionHistory && $classe->revisionHistory->count() > 0)
            <div class="mt-6 bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700 bg-gradient-to-r from-dark-800 to-dark-900">
                    <h2 class="text-lg font-medium text-white">Historique des modifications</h2>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($classe->revisionHistory->sortByDesc('created_at')->take(5) as $history)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-dark-600" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                @if($history->key === 'created_at' && !$history->old_value)
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-dark-800">
                                                        <i class="fas fa-plus text-white text-xs"></i>
                                                    </span>
                                                @else
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-dark-800">
                                                        <i class="fas fa-pen text-white text-xs"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-300">
                                                        @if($history->key === 'created_at' && !$history->old_value)
                                                            Classe créée
                                                        @else
                                                            {{ $history->fieldName() }} modifié
                                                        @endif
                                                        <span class="font-medium text-white">
                                                            @if($history->user)
                                                                par {{ $history->user->prenom }} {{ $history->user->nom }}
                                                            @else
                                                                par un administrateur
                                                            @endif
                                                        </span>
                                                    </p>
                                                    @if($history->key !== 'created_at' || $history->old_value)
                                                        <div class="mt-1 text-xs text-gray-400">
                                                            <span class="line-through">{{ $history->oldValue() }}</span>
                                                            <span class="mx-1">→</span>
                                                            <span class="text-green-400">{{ $history->newValue() }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="whitespace-nowrap text-right text-sm text-gray-400">
                                                    <time datetime="{{ $history->created_at->toIso8601String() }}">
                                                        {{ $history->created_at->diffForHumans() }}
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @if($classe->revisionHistory->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="#" class="text-sm text-primary-500 hover:text-primary-400">
                                Voir tout l'historique
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scripts spécifiques à la page d'édition
    });
</script>
@endpush

@endsection
