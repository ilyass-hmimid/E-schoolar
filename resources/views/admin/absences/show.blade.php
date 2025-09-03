@extends('layouts.admin')

@section('title', 'Détails de l\'absence #' . $absence->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Détails de l'absence</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                        <i class="fas fa-home mr-2"></i>
                        Tableau de bord
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('admin.absences.index') }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                            Absences
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            Détails #{{ $absence->id }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Informations sur l'absence
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Détails complets de l'absence enregistrée.
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.absences.edit', $absence) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <i class="fas fa-edit mr-2"></i> Modifier
                </a>
                <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-2"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <!-- Étudiant -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Étudiant
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <a href="{{ route('admin.eleves.show', $absence->eleve) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $absence->eleve->nom }} {{ $absence->eleve->prenom }}
                        </a>
                    </dd>
                </div>

                <!-- Cours -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Cours
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $absence->cours->nom }} ({{ $absence->cours->professeur->nom }})
                    </dd>
                </div>

                <!-- Date et heure -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Date et heure
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        Le {{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}
                        de {{ \Carbon\Carbon::parse($absence->heure_debut)->format('H:i') }}
                        à {{ \Carbon\Carbon::parse($absence->heure_fin)->format('H:i') }}
                    </dd>
                </div>

                <!-- Statut -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Statut
                    </dt>
                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                        @php
                            $statusClasses = [
                                'non_justifiee' => 'bg-red-100 text-red-800',
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'justifiee' => 'bg-green-100 text-green-800',
                            ][$absence->statut];
                            
                            $statusLabels = [
                                'non_justifiee' => 'Non justifiée',
                                'en_attente' => 'En attente de validation',
                                'justifiee' => 'Justifiée',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                            {{ $statusLabels[$absence->statut] }}
                        </span>
                    </dd>
                </div>

                <!-- Justification -->
                @if($absence->justification)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Justification
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $absence->justification }}
                    </dd>
                </div>
                @endif

                <!-- Commentaire -->
                @if($absence->commentaire)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Commentaire
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
                        {{ $absence->commentaire }}
                    </dd>
                </div>
                @endif

                <!-- Dates -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Dates importantes
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <p>Créée le : {{ $absence->created_at->format('d/m/Y H:i') }}</p>
                        <p>Dernière mise à jour : {{ $absence->updated_at->format('d/m/Y H:i') }}</p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('admin.absences.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>
</div>
@endsection
