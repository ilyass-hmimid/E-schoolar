@extends('layouts.app')

@section('title', 'Mes Cours')

@section('header')
<div class="flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">
        Mes Cours
    </h1>
</div>
@endsection

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Liste de mes cours
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Consultez votre emploi du temps et les détails de vos cours.
        </p>
    </div>
    
    <div class="border-t border-gray-200">
        @if($cours->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($cours as $presence)
                    <li class="hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ $presence->matiere->nom ?? 'Matière non définie' }}
                                        </p>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($presence->statut === 'present') bg-green-100 text-green-800
                                            @elseif($presence->statut === 'absent') bg-red-100 text-red-800
                                            @elseif($presence->statut === 'retard') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $presence->statut_libelle }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $presence->date_seance->format('d/m/Y') }}
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $presence->heure_debut }} - {{ $presence->heure_fin }}
                                        </div>
                                        @if($presence->classe)
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                            </svg>
                                            {{ $presence->classe->nom }}
                                        </div>
                                        @endif
                                        @if($presence->professeur)
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $presence->professeur->name }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="{{ route('eleve.cours.show', $presence->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Voir les détails
                                    </a>
                                </div>
                            </div>
                            @if($presence->commentaire)
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    <span class="font-medium">Commentaire :</span> {{ $presence->commentaire }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $cours->links('pagination::simple-tailwind') }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de 
                            <span class="font-medium">{{ $cours->firstItem() }}</span>
                            à 
                            <span class="font-medium">{{ $cours->lastItem() }}</span>
                            sur 
                            <span class="font-medium">{{ $cours->total() }}</span>
                            résultats
                        </p>
                    </div>
                    <div>
                        {{ $cours->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun cours trouvé</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Vous n'avez pas encore de cours prévus.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
