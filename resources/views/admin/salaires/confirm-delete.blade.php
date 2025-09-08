@extends('layouts.admin')

@section('title', 'Confirmer la suppression')

@push('styles')
<style>
    .warning-icon {
        @apply text-yellow-400 text-5xl mb-4;
    }
    .warning-text {
        @apply text-yellow-700 bg-yellow-100 p-4 rounded-lg border-l-4 border-yellow-500;
    }
    .salaire-details {
        @apply bg-gray-50 p-4 rounded-lg border border-gray-200 mt-4;
    }
    .detail-label {
        @apply text-sm font-medium text-gray-500;
    }
    .detail-value {
        @apply text-sm text-gray-900;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Confirmer la suppression</h1>
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
                            <a href="{{ route('admin.salaires.index') }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                                Salaires
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400"></i>
                            <a href="{{ route('admin.salaires.show', $salaire) }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                                {{ $salaire->professeur->nom_complet }} - {{ \Carbon\Carbon::parse($salaire->periode)->format('m/Y') }}
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400"></i>
                            <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                                Supprimer
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-red-50 border-b border-red-200">
                <h3 class="text-lg leading-6 font-medium text-red-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Suppression d'un salaire
                </h3>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <div class="text-center
                ">
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                        Êtes-vous sûr de vouloir supprimer ce salaire ?
                    </h3>
                    
                    <div class="warning-text text-left max-w-2xl mx-auto">
                        <p class="font-medium">Cette action est irréversible !</p>
                        <p class="mt-1">
                            La suppression de ce salaire entraînera la suppression définitive de toutes les données associées.
                            Cette action ne peut pas être annulée.
                        </p>
                    </div>
                    
                    <div class="salaire-details text-left max-w-2xl mx-auto mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">Détails du salaire à supprimer :</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="detail-label">Professeur</p>
                                <p class="detail-value">{{ $salaire->professeur->nom_complet }}</p>
                            </div>
                            <div>
                                <p class="detail-label">Période</p>
                                <p class="detail-value">{{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}</p>
                            </div>
                            <div>
                                <p class="detail-label">Salaire net</p>
                                <p class="detail-value font-medium">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</p>
                            </div>
                            <div>
                                <p class="detail-label">Statut</p>
                                @php
                                    $statusClasses = [
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'paye' => 'bg-green-100 text-green-800',
                                        'retard' => 'bg-red-100 text-red-800',
                                    ][$salaire->statut];
                                    
                                    $statusLabels = [
                                        'en_attente' => 'En attente',
                                        'paye' => 'Payé',
                                        'retard' => 'En retard',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                    {{ $statusLabels[$salaire->statut] }}
                                </span>
                                @if($salaire->est_avance)
                                    <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Avance
                                    </span>
                                @endif
                            </div>
                            @if($salaire->date_paiement)
                            <div class="md:col-span-2">
                                <p class="detail-label">Paiement effectué le</p>
                                <p class="detail-value">
                                    {{ \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') }}
                                    ({{ ucfirst($salaire->type_paiement) }})
                                    @if($salaire->reference)
                                        - {{ $salaire->reference }}
                                    @endif
                                </p>
                            </div>
                            @endif
                            @if($salaire->notes)
                            <div class="md:col-span-2">
                                <p class="detail-label">Notes</p>
                                <p class="detail-value whitespace-pre-line">{{ $salaire->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-8 text-sm text-gray-500 text-left max-w-2xl mx-auto">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Conseil :</span> 
                                    Si ce salaire a été créé par erreur, vous pouvez envisager de le marquer comme annulé 
                                    plutôt que de le supprimer, afin de conserver une trace de l'opération.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.salaires.destroy', $salaire) }}" method="POST" class="mt-8">
                        @csrf
                        @method('DELETE')
                        
                        <div class="flex items-center justify-center">
                            <div class="flex items-center mr-4">
                                <input id="confirm-delete" name="confirm" type="checkbox" required
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="confirm-delete" class="ml-2 block text-sm text-gray-700">
                                    Je confirme vouloir supprimer définitivement ce salaire
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-8 flex justify-center space-x-3">
                            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            
                            <button type="submit" id="delete-button" disabled
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer définitivement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmCheckbox = document.getElementById('confirm-delete');
        const deleteButton = document.getElementById('delete-button');
        
        confirmCheckbox.addEventListener('change', function() {
            deleteButton.disabled = !this.checked;
        });
    });
</script>
@endpush

@endsection
