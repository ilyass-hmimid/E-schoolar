@extends('layouts.eleve')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('eleve.absences.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste des absences
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Détails de l'absence
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ $absence->matiere->nom ?? 'Matière non spécifiée' }}
                </p>
            </div>
            <div>
                @if($absence->type === 'retard')
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Retard
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Absence
                    </span>
                @endif
            </div>
        </div>

        <div class="px-6 py-5">
            <div class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $absence->date_absence->format('d/m/Y') }}
                    </dd>
                </div>

                @if($absence->heure_debut)
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Heure
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $absence->heure_debut->format('H:i') }}
                            @if($absence->heure_fin)
                                - {{ $absence->heure_fin->format('H:i') }}
                            @endif
                        </dd>
                    </div>
                @endif

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Matière
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $absence->matiere->nom ?? 'Non spécifiée' }}
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Professeur
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $absence->professeur->name ?? 'Non spécifié' }}
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Statut
                    </dt>
                    <dd class="mt-1">
                        @if($absence->justifiee)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Justifiée
                            </span>
                        @elseif($absence->peut_etre_justifiee)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                En attente de justification
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Non justifiée
                            </span>
                        @endif
                    </dd>
                </div>

                @if($absence->justification)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Justification
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                            {{ $absence->justification }}
                        </dd>
                    </div>
                @endif

                @if($absence->commentaire)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Commentaire du professeur
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                            {{ $absence->commentaire }}
                        </dd>
                    </div>
                @endif

                @if($absence->piece_jointe)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Pièce jointe
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ Storage::url($absence->piece_jointe) }}" 
                               target="_blank" 
                               class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Voir le document
                            </a>
                        </dd>
                    </div>
                @endif
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 text-right sm:px-6">
            @if($absence->peut_etre_justifiee)
                <a href="{{ route('eleve.absences.justifier', $absence) }}" 
                   class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Justifier cette absence
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
