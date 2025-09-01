@extends('layouts.admin')

@section('title', 'Détails du cours')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Détails du cours</h1>
            <p class="text-gray-400">Informations complètes sur le cours</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <a href="{{ route('admin.cours.edit', $cour->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> Modifier
            </a>
            <a href="{{ route('admin.cours.index') }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $cour->intitule }}</h2>
                            <p class="text-gray-400">{{ $cour->code }}</p>
                        </div>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $cour->niveau->nom }}
                        </span>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Matière</h3>
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
                                    {{ substr($cour->matiere->nom, 0, 2) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ $cour->matiere->nom }}</p>
                                    <p class="text-xs text-gray-400">Code: {{ $cour->matiere->code }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Enseignant</h3>
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
                                    {{ substr($cour->enseignant->user->prenom, 0, 1) }}{{ substr($cour->enseignant->user->nom, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ $cour->enseignant->user->prenom }} {{ $cour->enseignant->user->nom }}</p>
                                    <p class="text-xs text-gray-400">{{ $cour->enseignant->specialite ?? 'Aucune spécialité' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Volume horaire</h3>
                            <p class="text-white">{{ $cour->volume_horaire }} heures</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Crédits</h3>
                            <p class="text-white">{{ $cour->credits ?? 'Non défini' }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Description</h3>
                        <div class="prose prose-invert max-w-none">
                            {!! $cour->description ?? '<p class="text-gray-400 italic">Aucune description disponible</p>' !!}
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-dark-700 px-6 py-4 bg-dark-750">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-4">
                            <span class="inline-flex items-center text-sm text-gray-400">
                                <i class="fas fa-calendar-plus mr-1.5"></i>
                                Créé le {{ $cour->created_at->format('d/m/Y') }}
                            </span>
                            <span class="inline-flex items-center text-sm text-gray-400">
                                <i class="fas fa-edit mr-1.5"></i>
                                Modifié le {{ $cour->updated_at->format('d/m/Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cour->est_actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $cour->est_actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section des séances -->
            <div class="mt-6 bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-white">Séances prévues</h3>
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-plus mr-1.5"></i> Nouvelle séance
                    </button>
                </div>
                <div class="p-6">
                    <div class="bg-dark-700 rounded-lg p-4 text-center">
                        <i class="fas fa-calendar-day text-3xl text-gray-500 mb-2"></i>
                        <h4 class="text-gray-300 font-medium">Aucune séance programmée</h4>
                        <p class="text-sm text-gray-500 mt-1">Créez votre première séance pour ce cours</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statut et actions -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">Statut du cours</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Statut</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cour->est_actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $cour->est_actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Visibilité</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $cour->est_public ? 'Public' : 'Privé' }}
                            </span>
                        </div>
                        <div class="pt-4 mt-4 border-t border-dark-700">
                            <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-calendar-plus mr-2"></i> Planifier une séance
                            </button>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-300 bg-dark-700 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-file-export mr-2"></i> Exporter le planning
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Documents et ressources -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700">
                    <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider">Documents et ressources</h3>
                </div>
                <div class="p-4">
                    <div class="text-center py-6">
                        <i class="fas fa-file-upload text-3xl text-gray-500 mb-2"></i>
                        <p class="text-sm text-gray-400">Aucun document partagé</p>
                        <button type="button" class="mt-2 text-sm text-primary-500 hover:text-primary-400">
                            <i class="fas fa-plus mr-1"></i> Ajouter un document
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Élèves inscrits -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider">Élèves inscrits</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $cour->eleves->count() }} élèves
                    </span>
                </div>
                <div class="p-4">
                    @if($cour->eleves->count() > 0)
                        <ul class="space-y-3">
                            @foreach($cour->eleves->take(5) as $eleve)
                                <li class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($eleve->user->prenom, 0, 1) }}{{ substr($eleve->user->nom, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-white">{{ $eleve->user->prenom }} {{ $eleve->user->nom }}</p>
                                        <p class="text-xs text-gray-400">{{ $eleve->niveau->nom ?? 'Niveau non défini' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if($cour->eleves->count() > 5)
                            <div class="mt-3 text-center">
                                <a href="#" class="text-sm text-primary-500 hover:text-primary-400">
                                    Voir les {{ $cour->eleves->count() - 5 }} autres élèves
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users-slash text-2xl text-gray-500 mb-2"></i>
                            <p class="text-sm text-gray-400">Aucun élève inscrit</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scripts spécifiques à la page de détails du cours
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des onglets ou autres fonctionnalités JavaScript si nécessaire
    });
</script>
@endpush

@endsection
