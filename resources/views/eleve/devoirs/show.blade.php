@extends('layouts.app')

@section('title', $devoir->titre)

@section('header')
<div class="flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">
        {{ $devoir->titre }}
    </h1>
    <a href="{{ route('eleve.devoirs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
        Retour
    </a>
</div>
@endsection

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $devoir->titre }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $devoir->matiere->nom ?? 'Matière non spécifiée' }}
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium 
                @if($devoirRendu) bg-green-100 text-green-800">
                    Rendu
                @elseif($devoir->date_limite->isPast())
                    bg-red-100 text-red-800">
                    En retard
                @else
                    bg-blue-100 text-blue-800">
                    À rendre
                @endif
            </span>
        </div>
    </div>
    
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500">Date limite</p>
                <p class="mt-1 text-sm font-medium text-gray-900">
                    {{ $devoir->date_limite->format('d/m/Y à H:i') }}
                </p>
            </div>
            
            @if($devoir->points)
            <div>
                <p class="text-sm text-gray-500">Points</p>
                <p class="mt-1 text-sm font-medium text-gray-900">
                    {{ $devoir->points }} points
                </p>
            </div>
            @endif
            
            @if($devoir->description)
            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500">Description</p>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                    {{ $devoir->description }}
                </p>
            </div>
            @endif
            
            @if($devoir->fichier_consigne)
            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500">Fichier de consigne</p>
                <a href="{{ Storage::url($devoir->fichier_consigne) }}" 
                   target="_blank" 
                   class="mt-1 inline-flex items-center text-indigo-600 hover:text-indigo-900 text-sm">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Télécharger
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@if($devoirRendu)
<div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg font-medium text-gray-900">Votre rendu</h3>
    </div>
    
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500">Date de soumission</p>
                <p class="mt-1 text-sm text-gray-900">
                    {{ $devoirRendu->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Statut</p>
                <p class="mt-1 text-sm font-medium 
                    {{ $devoirRendu->est_en_retard ? 'text-red-600' : 'text-green-600' }}">
                    {{ $devoirRendu->est_en_retard ? 'Soumis en retard' : 'Soumis à temps' }}
                </p>
            </div>
            
            @if($devoirRendu->fichier_rendu)
            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500">Votre fichier</p>
                <a href="{{ Storage::url($devoirRendu->fichier_rendu) }}" 
                   target="_blank" 
                   class="mt-1 inline-flex items-center text-indigo-600 hover:text-indigo-900 text-sm">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Télécharger
                </a>
            </div>
            @endif
            
            @if($devoirRendu->note)
            <div class="sm:col-span-2 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">Note</p>
                <p class="text-2xl font-semibold text-indigo-600">
                    {{ $devoirRendu->note }} / {{ $devoir->points ?? '20' }}
                </p>
                
                @if($devoirRendu->commentaire_professeur)
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Commentaire du professeur</p>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                        {{ $devoirRendu->commentaire_professeur }}
                    </p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@elseif(!$devoir->date_limite->isPast())
<div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg font-medium text-gray-900">Rendre ce devoir</h3>
    </div>
    
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <form action="{{ route('eleve.devoirs.rendre', $devoir->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label for="commentaire" class="block text-sm font-medium text-gray-700">
                    Commentaire (optionnel)
                </label>
                <textarea id="commentaire" name="commentaire" rows="3" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Fichier à rendre
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="fichier" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                <span>Téléverser un fichier</span>
                                <input id="fichier" name="fichier" type="file" class="sr-only" required>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">
                            PDF, DOC, DOCX, ODT, JPG, PNG (max. 10Mo)
                        </p>
                    </div>
                </div>
                @error('fichier')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Soumettre
                </button>
            </div>
        </form>
    </div>
</div>
@else
<div class="mt-8 bg-red-50 border-l-4 border-red-400 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-700">
                La date limite est dépassée. Vous ne pouvez plus soumettre ce devoir.
            </p>
        </div>
    </div>
</div>
@endif

@if(!$devoirRendu && $devoir->date_limite->diffInDays(now()) < 3 && !$devoir->date_limite->isPast())
<div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">
                Attention ! La date limite approche ({{ $devoir->date_limite->diffForHumans() }}).
                Pensez à rendre votre devoir à temps.
            </p>
        </div>
    </div>
</div>
@endif

@endsection
