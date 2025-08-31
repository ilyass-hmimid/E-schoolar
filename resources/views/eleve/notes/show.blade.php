@extends('layouts.eleve')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('eleve.notes.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste des notes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Détail de la note</h2>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    {{ $note->note >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $note->note }}/20
                </span>
            </div>
        </div>

        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Matière</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $note->enseignement->matiere->nom }}
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Professeur</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $note->enseignement->professeur->name ?? 'Non défini' }}
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Type d'évaluation</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ ucfirst($note->type) }}
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $note->created_at->format('d/m/Y') }}
                    </p>
                </div>
                
                @if($note->commentaire)
                <div class="col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Commentaire</h3>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                        {{ $note->commentaire }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">
                    Publiée le {{ $note->created_at->format('d/m/Y à H:i') }}
                </span>
                @if($note->updated_at->gt($note->created_at))
                    <span class="text-xs text-gray-400">
                        Mise à jour: {{ $note->updated_at->format('d/m/Y H:i') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
