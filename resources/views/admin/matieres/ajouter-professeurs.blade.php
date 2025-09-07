@extends('admin.layout')

@section('title', 'Ajouter des professeurs - ' . $matiere->nom)

@section('header')
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
            <a href="{{ route('admin.matieres.index') }}" class="text-indigo-600 hover:text-indigo-900">Matières</a>
            <span class="text-gray-400 mx-2">/</span>
            <a href="{{ route('admin.matieres.show', $matiere) }}" class="text-indigo-600 hover:text-indigo-900">{{ $matiere->nom }}</a>
            <span class="text-gray-400 mx-2">/</span>
            <a href="{{ route('admin.matieres.professeurs', $matiere) }}" class="text-indigo-600 hover:text-indigo-900">Professeurs</a>
            <span class="text-gray-400 mx-2">/</span>
            Ajouter des professeurs
        </h1>
    </div>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('admin.matieres.ajouter-professeurs', $matiere) }}" method="POST">
            @csrf
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div class="mb-6">
                    <p class="text-sm text-gray-500">
                        Sélectionnez les professeurs à ajouter à la matière <span class="font-medium">{{ $matiere->nom }}</span>.
                        Seuls les professeurs actuellement non assignés à cette matière sont affichés.
                    </p>
                </div>

                @if($professeurs->count() > 0)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2">
                            @foreach($professeurs as $professeur)
                                <div class="relative flex items-start py-2">
                                    <div class="flex items-center h-5">
                                        <input id="professeur-{{ $professeur->id }}" name="professeurs[]" type="checkbox" 
                                               value="{{ $professeur->id }}" 
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="professeur-{{ $professeur->id }}" class="font-medium text-gray-700">
                                            {{ $professeur->name }}
                                        </label>
                                        <p class="text-gray-500">{{ $professeur->email }}</p>
                                        <div class="mt-1">
                                            <div class="flex items-center">
                                                <input id="responsable-{{ $professeur->id }}" name="responsable" type="radio" 
                                                       value="{{ $professeur->id }}" 
                                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                                       {{ $loop->first ? 'checked' : '' }}>
                                                <label for="responsable-{{ $professeur->id }}" class="ml-2 block text-sm text-gray-700">
                                                    Définir comme responsable
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.matieres.professeurs', $matiere) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ajouter les professeurs sélectionnés
                        </button>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tous les professeurs sont déjà assignés</h3>
                        <p class="mt-1 text-sm text-gray-500">Aucun professeur supplémentaire à ajouter à cette matière.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.matieres.professeurs', $matiere) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour à la liste des professeurs
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection
