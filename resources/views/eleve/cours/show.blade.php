@extends('layouts.app')

@section('title', 'Détails du cours')

@section('header')
<div class="flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">
        Détails du cours
    </h1>
    <a href="{{ route('eleve.cours.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
        Retour à la liste
    </a>
</div>
@endsection

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $cours->matiere->nom ?? 'Matière non définie' }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Détails du cours et informations de présence
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium 
                @if($cours->statut === 'present') bg-green-100 text-green-800
                @elseif($cours->statut === 'absent') bg-red-100 text-red-800
                @elseif($cours->statut === 'retard') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $cours->statut_libelle }}
            </span>
        </div>
    </div>
    
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Date
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $cours->date_seance->format('d/m/Y') }}
                </dd>
            </div>
            
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Heure
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $cours->heure_debut }} - {{ $cours->heure_fin }}
                </dd>
            </div>
            
            @if($cours->classe)
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Classe
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $cours->classe->nom }}
                </dd>
            </div>
            @endif
            
            @if($cours->professeur)
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Professeur
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $cours->professeur->name }}
                </dd>
            </div>
            @endif
            
            @if($cours->salle)
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Salle
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $cours->salle }}
                </dd>
            </div>
            @endif
            
            @if($cours->type_cours)
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Type de cours
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ ucfirst($cours->type_cours) }}
                </dd>
            </div>
            @endif
            
            @if($cours->commentaire)
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">
                    Commentaire
                </dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                    {{ $cours->commentaire }}
                </dd>
            </div>
            @endif
            
            @if($cours->justificatif)
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">
                    Justificatif
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    <a href="{{ Storage::url($cours->justificatif) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            Voir le justificatif
                        </div>
                    </a>
                </dd>
            </div>
            @endif
        </dl>
    </div>
    
    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end">
        @if($cours->statut === 'absent' && !$cours->justificatif)
        <a href="{{ route('eleve.absences.justifier', $cours->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Justifier cette absence
        </a>
        @endif
    </div>
</div>

@if($cours->matiere && $cours->matiere->devoirs->count() > 0)
<div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Devoirs pour cette matière
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Liste des devoirs associés à cette matière
        </p>
    </div>
    
    <div class="border-t border-gray-200">
        <ul class="divide-y divide-gray-200">
            @foreach($cours->matiere->devoirs as $devoir)
            <li class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-600 truncate">
                            {{ $devoir->titre }}
                        </p>
                        <p class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            À rendre avant le {{ $devoir->date_limite->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <a href="{{ route('eleve.devoirs.show', $devoir->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Voir le devoir
                        </a>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@if($cours->matiere && $cours->matiere->ressources->count() > 0)
<div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Ressources pour cette matière
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Documents et ressources partagés par le professeur
        </p>
    </div>
    
    <div class="border-t border-gray-200">
        <ul class="divide-y divide-gray-200">
            @foreach($cours->matiere->ressources as $ressource)
            <li class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-600 truncate">
                            {{ $ressource->titre }}
                        </p>
                        <p class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            {{ $ressource->type }} • {{ $ressource->taille_formatee }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <a href="{{ Storage::url($ressource->fichier) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Télécharger
                        </a>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@endsection
