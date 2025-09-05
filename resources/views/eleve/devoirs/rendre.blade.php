@extends('layouts.eleve')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('eleve.devoirs.show', $devoir->id) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour au devoir
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Rendre le devoir
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ $devoir->titre }} - {{ $devoir->matiere->nom }}
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <form action="{{ route('eleve.devoirs.rendre', $devoir->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('partials.errors')
                
                <div class="mb-6">
                    <label for="contenu" class="block text-sm font-medium text-gray-700 mb-1">
                        Votre travail
                    </label>
                    <textarea id="contenu" name="contenu" rows="8" 
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md p-2"
                        placeholder="Décrivez votre travail ici..." required></textarea>
                </div>
                
                <div class="mb-6">
                    <label for="fichier" class="block text-sm font-medium text-gray-700 mb-1">
                        Fichier joint (optionnel)
                    </label>
                    <div class="mt-1 flex items-center">
                        <input type="file" id="fichier" name="fichier" 
                            class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Formats acceptés: PDF, DOC, DOCX, ODT, TXT. Taille maximale: 10MB
                    </p>
                </div>
                
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3
                        ">
                            <h3 class="text-sm font-medium text-blue-800">
                                Date limite de rendu: {{ $devoir->date_limite->format('d/m/Y à H:i') }}
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Assurez-vous de soumettre votre travail avant cette date. Les retards peuvent affecter votre note.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <a href="{{ route('eleve.devoirs.show', $devoir->id) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Annuler
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Soumettre le devoir
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Détails du devoir -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Détails du devoir
            </h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Titre
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $devoir->titre }}
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Matière
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $devoir->matiere->nom }}
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Description
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {!! nl2br(e($devoir->description)) !!}
                    </dd>
                </div>
                @if($devoir->piecesJointes->count() > 0)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Fichiers joints
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                @foreach($devoir->piecesJointes as $fichier)
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a2.5 2.5 0 11-5 0V7a1 1 0 012 0v4a.5.5 0 101 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-2 flex-1 w-0 truncate">
                                                {{ $fichier->nom_fichier }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <a href="{{ route('telecharger.fichier', $fichier->chemin) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                                Télécharger
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection
