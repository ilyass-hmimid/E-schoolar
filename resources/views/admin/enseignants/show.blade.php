@extends('layouts.admin')

@section('title', 'Détails de l\'enseignant')

@section('content')
<div class="bg-dark-900 rounded-xl shadow-lg overflow-hidden">
    <!-- En-tête avec boutons d'action -->
    <div class="bg-dark-800 px-6 py-4 border-b border-dark-700 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</h2>
            <p class="text-gray-400">Fiche détaillée de l'enseignant</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.enseignants.edit', $enseignant->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg">
                <i class="fas fa-edit mr-2"></i> Modifier
            </a>
            <a href="{{ route('admin.enseignants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne de gauche - Informations personnelles -->
            <div class="lg:col-span-1">
                <div class="bg-dark-800 rounded-xl p-6 shadow">
                    <div class="flex flex-col items-center">
                        <div class="h-32 w-32 rounded-full bg-primary-600 flex items-center justify-center text-4xl font-bold text-white mb-4">
                            {{ substr($enseignant->user->prenom, 0, 1) }}{{ substr($enseignant->user->nom, 0, 1) }}
                        </div>
                        <h3 class="text-xl font-semibold text-white">{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</h3>
                        <p class="text-gray-400 text-sm">{{ $enseignant->specialite }}</p>
                        
                        <div class="mt-6 w-full space-y-3">
                            <div class="flex items-center text-gray-300">
                                <i class="fas fa-envelope mr-3 text-gray-400 w-5"></i>
                                <span>{{ $enseignant->user->email }}</span>
                            </div>
                            @if($enseignant->user->telephone)
                            <div class="flex items-center text-gray-300">
                                <i class="fas fa-phone-alt mr-3 text-gray-400 w-5"></i>
                                <span>{{ $enseignant->user->telephone }}</span>
                            </div>
                            @endif
                            @if($enseignant->user->date_naissance)
                            <div class="flex items-center text-gray-300">
                                <i class="fas fa-birthday-cake mr-3 text-gray-400 w-5"></i>
                                <span>{{ $enseignant->user->date_naissance->format('d/m/Y') }} ({{ $enseignant->user->date_naissance->age }} ans)</span>
                            </div>
                            @endif
                            @if($enseignant->user->adresse)
                            <div class="flex items-start text-gray-300">
                                <i class="fas fa-map-marker-alt mr-3 text-gray-400 w-5 mt-1"></i>
                                <span>{{ $enseignant->user->adresse }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statut et informations complémentaires -->
                <div class="bg-dark-800 rounded-xl p-6 mt-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Informations professionnelles</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-400">Spécialité</p>
                            <p class="text-white">{{ $enseignant->specialite }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Diplôme</p>
                            <p class="text-white">{{ $enseignant->diplome }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Date d'embauche</p>
                            <p class="text-white">{{ $enseignant->user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite - Activités et statistiques -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Prochains cours -->
                <div class="bg-dark-800 rounded-xl p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Prochains cours</h3>
                        <a href="#" class="text-sm text-primary-400 hover:text-primary-300">Voir tout</a>
                    </div>
                    <div class="space-y-4">
                        @if(count($enseignant->cours) > 0)
                            @foreach($enseignant->cours->take(3) as $cours)
                            <div class="bg-dark-700 p-4 rounded-lg hover:bg-dark-600 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-white">{{ $cours->matiere->nom }}</h4>
                                        <p class="text-sm text-gray-400">{{ $cours->classe->niveau->nom }} - {{ $cours->classe->nom }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $cours->statut === 'confirmé' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($cours->statut) }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-400">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    {{ $cours->date_debut->format('d/m/Y') }}
                                    <i class="far fa-clock ml-4 mr-2"></i>
                                    {{ $cours->heure_debut }} - {{ $cours->heure_fin }}
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-gray-400 text-center py-4">Aucun cours programmé pour le moment</p>
                        @endif
                    </div>
                </div>

                <!-- Notes récentes -->
                <div class="bg-dark-800 rounded-xl p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Notes récentes</h3>
                        <a href="#" class="text-sm text-primary-400 hover:text-primary-300">Voir tout</a>
                    </div>
                    <div class="space-y-4">
                        @if(count($enseignant->notes) > 0)
                            @foreach($enseignant->notes->sortByDesc('created_at')->take(3) as $note)
                            <div class="bg-dark-700 p-4 rounded-lg">
                                <div class="flex justify-between">
                                    <span class="font-medium text-white">{{ $note->eleve->prenom }} {{ $note->eleve->nom }}</span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ $note->valeur }}/20
                                    </span>
                                </div>
                                <p class="text-sm text-gray-400 mt-1">{{ $note->matiere->nom }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $note->created_at->diffForHumans() }}</p>
                            </div>
                            @endforeach
                        @else
                            <p class="text-gray-400 text-center py-4">Aucune note enregistrée</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
