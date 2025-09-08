@extends('layouts.professeur')

@section('title', 'Détails du salaire')

@push('styles')
<style>
    .detail-card {
        @apply bg-white rounded-lg shadow overflow-hidden mb-6 transition-all duration-200 hover:shadow-md print:shadow-none print:border print:border-gray-200;
    }
    .detail-card-header {
        @apply px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center print:py-2;
    }
    .detail-card-body {
        @apply px-4 sm:px-6 py-4 print:px-2 print:py-2;
    }
    .detail-row {
        @apply py-2 sm:py-3 flex flex-col md:flex-row border-b border-gray-100 last:border-0 print:py-1 print:text-sm;
    }
    .detail-label {
        @apply text-xs sm:text-sm font-medium text-gray-500 w-full md:w-1/3 print:text-xs;
    }
    .detail-value {
        @apply mt-0.5 md:mt-0 text-sm text-gray-900 w-full md:w-2/3 print:text-xs;
    }
    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    .badge-paye {
        @apply bg-green-100 text-green-800;
    }
    .badge-en_attente {
        @apply bg-yellow-100 text-yellow-800;
    }
    .badge-retard {
        @apply bg-red-100 text-red-800;
    }
    .badge-annule {
        @apply bg-gray-100 text-gray-800;
    }
    .badge-en_reclamation {
        @apply bg-purple-100 text-purple-800;
    }
    .timeline {
        @apply relative border-l-2 border-gray-200 pl-6 pb-6 space-y-6;
    }
    .timeline-item {
        @apply relative;
    }
    .timeline-badge {
        @apply absolute w-4 h-4 rounded-full bg-blue-500 border-4 border-white -left-7 top-1;
    }
    .timeline-content {
        @apply text-sm text-gray-600;
    }
    .timeline-time {
        @apply text-xs text-gray-400;
    }
    .montant-positif {
        @apply text-green-600 font-medium;
    }
    .montant-negatif {
        @apply text-red-600 font-medium;
    }
    .salaire-net {
        @apply text-lg sm:text-xl font-bold text-blue-600 print:text-lg;
    }
    .action-button {
        @apply w-full flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 rounded-md text-xs sm:text-sm font-medium transition-colors duration-200 print:hidden;
    }
    .action-button-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
    }
    .action-button-secondary {
        @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
    }
    .action-button-danger {
        @apply bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-offset-2 focus:ring-red-500;
    }
</style>
@endpush

@section('content')
@push('print-styles')
<style>
    @media print {
        body {
            font-size: 12px;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .no-print {
            display: none !important;
        }
        .print-break-inside-avoid {
            break-inside: avoid;
        }
        .print-mt-4 {
            margin-top: 1rem;
        }
        .print-p-2 {
            padding: 0.5rem;
        }
        .print-text-xs {
            font-size: 10px;
        }
        .print-border {
            border: 1px solid #e5e7eb;
        }
        .print-bg-gray-50 {
            background-color: #f9fafb;
        }
    }
</style>
@endpush
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec fil d'Ariane et actions -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Détails du salaire</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('professeur.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('professeur.salaires.index') }}" class="text-blue-600 hover:text-blue-800 ml-1 md:ml-2 text-sm font-medium">Mes Salaires</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-500 ml-1 md:ml-2 text-sm font-medium">Salaire {{ $salaire->periode_formatted }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            
            <!-- Actions rapides -->
            <div class="flex flex-wrap gap-2">
                <!-- Télécharger la fiche de paie -->
                <a href="{{ route('professeur.salaires.telecharger-fiche-paie', $salaire) }}" 
                   class="action-button action-button-primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Télécharger PDF
                </a>
                
                <!-- Bouton d'impression -->
                <button onclick="window.print()" 
                        class="action-button action-button-secondary no-print">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Imprimer
                </button>
                
                <!-- Bouton de réclamation si le salaire est payé et qu'aucune réclamation n'est en cours -->
                @if($salaire->statut === 'paye' && $peut_faire_reclamation)
                <a href="{{ route('professeur.salaires.reclamation', $salaire) }}" 
                   class="action-button action-button-secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 100 2v3a1 1 0 102 0v-3a1 1 0 00-1-1h-1z" clip-rule="evenodd" />
                    </svg>
                    Signaler un problème
                </a>
                @endif
                
                <!-- Lien vers la réclamation si elle existe -->
                @if($salaire->reclamations->isNotEmpty())
                    @php $reclamation = $salaire->reclamations->first(); @endphp
                    <a href="{{ route('professeur.salaires.reclamations.show', $reclamation) }}" 
                       class="action-button {{ $reclamation->statut === 'resolue' ? 'action-button-secondary' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd" />
                        </svg>
                        Voir la réclamation
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Bannière d'information sur le délai de réclamation -->
        @if($salaire->statut === 'paye' && $jours_restants_reclamation > 0 && $salaire->reclamations->isEmpty())
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 100 2v3a1 1 0 102 0v-3a1 1 0 00-1-1h-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Vous avez encore <span class="font-medium">{{ $jours_restants_reclamation }} jours</span> pour signaler un problème concernant ce salaire. 
                        La date limite est le <span class="font-medium">{{ $date_limite_reclamation->format('d/m/Y') }}</span>.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 print:block print:space-y-4">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6 print:space-y-4 print:break-inside-avoid">
            <!-- Carte d'information générale -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h2 class="text-lg font-medium text-gray-900">Informations générales</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ 
                        $salaire->statut === 'paye' ? 'bg-green-100 text-green-800' : 
                        ($salaire->statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : 
                        ($salaire->statut === 'en_reclamation' ? 'bg-purple-100 text-purple-800' : 
                        ($salaire->statut === 'retard' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) 
                    }}">
                        {{ ucfirst(str_replace('_', ' ', $salaire->statut)) }}
                    </span>
                </div>
                <div class="detail-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="detail-row">
                                <span class="detail-label">Période</span>
                                <span class="detail-value font-medium">{{ $salaire->periode_formatted }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Référence</span>
                                <span class="detail-value font-mono text-sm">{{ $salaire->reference }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date d'émission</span>
                                <span class="detail-value">{{ now()->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @if($salaire->date_paiement)
                            <div class="detail-row">
                                <span class="detail-label">Date de paiement</span>
                                <span class="detail-value">{{ $salaire->date_paiement_formatted }}</span>
                            </div>
                            @endif
                            @if($salaire->type_paiement)
                            <div class="detail-row">
                                <span class="detail-label">Mode de paiement</span>
                                <span class="detail-value">{{ ucfirst($salaire->type_paiement) }}</span>
                            </div>
                            @endif
                            @if($salaire->compte_bancaire)
                            <div class="detail-row">
                                <span class="detail-label">Compte crédité</span>
                                <span class="detail-value">{{ $salaire->compte_bancaire }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($salaire->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Notes</h4>
                        <div class="bg-gray-50 p-3 rounded-md text-sm text-gray-700">
                            {{ $salaire->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Détails de rémunération -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h2 class="text-lg font-medium text-gray-900">Détails de la rémunération</h2>
                </div>
                <div class="detail-card-body">
                    <div class="space-y-3">
                        <!-- Gains -->
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-700">Gains</h4>
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Salaire de base</span>
                                    <span class="text-sm font-medium">{{ number_format($salaire->salaire_base, 2, ',', ' ') }} DH</span>
                                </div>
                                
                                @if($salaire->heures_supplementaires > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Heures supplémentaires ({{ $salaire->heures_supplementaires }}h)</span>
                                    <span class="text-sm font-medium text-green-600">+ {{ number_format($salaire->montant_heures_supp, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                                
                                @if($salaire->prime_rendement > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Prime de rendement</span>
                                    <span class="text-sm font-medium text-green-600">+ {{ number_format($salaire->prime_rendement, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                                
                                @if($salaire->prime_anciennete > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Prime d'ancienneté</span>
                                    <span class="text-sm font-medium text-green-600">+ {{ number_format($salaire->prime_anciennete, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                                
                                @if($salaire->autres_primes > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Autres primes</span>
                                    <span class="text-sm font-medium text-green-600">+ {{ number_format($salaire->autres_primes, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="border-t border-gray-100 pt-2 mt-2">
                                <div class="flex justify-between font-medium">
                                    <span>Total des gains</span>
                                    <span>{{ number_format($salaire->salaire_brut, 2, ',', ' ') }} DH</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Retenues -->
                        <div class="space-y-2 pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-medium text-gray-700">Retenues</h4>
                            <div class="space-y-1">
                                @if($salaire->cnss > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">CNSS</span>
                                    <span class="text-sm font-medium text-red-600">- {{ number_format($salaire->cnss, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                                
                                @if($salaire->amo > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Assurance Maladie Obligatoire (AMO)</span>
                                    <span class="text-sm font-medium text-red-600">- {{ number_format($salaire->amo, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                                
                                @if($salaire->ir > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Impôt sur le revenu (IR)</span>
                                    <span class="text-sm font-medium text-red-600">- {{ number_format($salaire->ir, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                                
                                @if($salaire->autres_retenues > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Autres retenues</span>
                                    <span class="text-sm font-medium text-red-600">- {{ number_format($salaire->autres_retenues, 2, ',', ' ') }} DH</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="border-t border-gray-100 pt-2 mt-2">
                                <div class="flex justify-between font-medium">
                                    <span>Total des retenues</span>
                                    <span>- {{ number_format($salaire->total_retenues, 2, ',', ' ') }} DH</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Salaire net -->
                        <div class="bg-blue-50 p-4 rounded-lg mt-4">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-800">Salaire net à payer</span>
                                <span class="text-xl font-bold text-blue-600">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</span>
                            </div>
                            <div class="mt-1 text-sm text-blue-700">
                                Arrêté la présente fiche de paie à la somme de <span class="font-medium">{{ ucfirst($salaire->salaire_net_lettres) }}</span>
                            </div>
                        </div>
                        
                        <!-- Cotisations et retenues -->
                        <div class="mt-6 mb-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-3">RETENUES ET COTISATIONS</h3>
                            
                            @if($salaire->cnss > 0)
                            <div class="detail-row">
                                <span class="detail-label">CNSS</span>
                                <span class="detail-value">- {{ number_format($salaire->cnss, 2, ',', ' ') }} DH</span>
                            </div>
                            @endif
                            
                            @if($salaire->amo > 0)
                            <div class="detail-row">
                                <span class="detail-label">AMO</span>
                                <span class="detail-value">- {{ number_format($salaire->amo, 2, ',', ' ') }} DH</span>
                            </div>
                            @endif
                            
                            @if($salaire->ir > 0)
                            <div class="detail-row">
                                <span class="detail-label">Impôt sur le revenu (IR)</span>
                                <span class="detail-value">- {{ number_format($salaire->ir, 2, ',', ' ') }} DH</span>
                            </div>
                            @endif
                            
                            @if($salaire->autres_retenues > 0)
                            <div class="detail-row">
                                <span class="detail-label">Autres retenues</span>
                                <span class="detail-value">- {{ number_format($salaire->autres_retenues, 2, ',', ' ') }} DH</span>
                            </div>
                            @endif
                            
                            @if($salaire->avance_salaire > 0)
                            <div class="detail-row">
                                <span class="detail-label">Avance sur salaire</span>
                                <span class="detail-value">- {{ number_format($salaire->avance_salaire, 2, ',', ' ') }} DH</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="detail-row pt-4 border-t border-gray-200">
                            <span class="detail-label font-medium text-lg">Salaire net à payer</span>
                            <span class="detail-value font-bold text-lg text-blue-600">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Historique et actions -->
        <div class="space-y-6 print:space-y-4 print:break-before-page">
            <!-- Carte de résumé -->
            <div class="bg-white rounded-lg shadow overflow-hidden print:shadow-none print:border print:border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Résumé du salaire</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Période</span>
                            <span class="text-sm font-medium">{{ $salaire->periode_formatted }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Salaire brut</span>
                            <span class="text-sm font-medium">{{ number_format($salaire->salaire_brut, 2, ',', ' ') }} DH</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total des retenues</span>
                            <span class="text-sm font-medium text-red-600">- {{ number_format($salaire->salaire_brut - $salaire->salaire_net, 2, ',', ' ') }} DH</span>
                        </div>
                        <div class="pt-3 mt-3 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-base font-medium">Net à payer</span>
                                <span class="text-lg font-bold text-blue-600">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($salaire->statut === 'paye')
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <a href="{{ route('professeur.salaires.telecharger-fiche-paie', $salaire) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Télécharger la fiche de paie
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Carte d'information du professeur -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du professeur</h3>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                {{ substr($salaire->professeur->prenom, 0, 1) }}{{ substr($salaire->professeur->nom, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $salaire->professeur->prenom }} {{ $salaire->professeur->nom }}</p>
                            <p class="text-sm text-gray-500">{{ $salaire->professeur->matricule }}</p>
                            <p class="text-sm text-gray-500">{{ $salaire->professeur->email }}</p>
                            <p class="text-sm text-gray-500">{{ $salaire->professeur->telephone }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Date d'embauche</p>
                                <p class="text-sm font-medium">{{ $salaire->professeur->date_embauche ? $salaire->professeur->date_embauche->format('d/m/Y') : 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Ancienneté</p>
                                <p class="text-sm font-medium">{{ $salaire->professeur->date_embauche ? $salaire->professeur->date_embauche->diffInYears(now()) . ' ans' : 'Non spécifiée' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte de statut -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statut du paiement</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Statut</span>
                                <span class="font-medium {{ 
                                    $salaire->statut === 'paye' ? 'text-green-600' : 
                                    ($salaire->statut === 'en_attente' ? 'text-yellow-600' : 
                                    ($salaire->statut === 'en_reclamation' ? 'text-purple-600' : 'text-red-600')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $salaire->statut)) }}
                                </span>
                            </div>
                            @if($salaire->statut === 'paye' && $salaire->date_paiement)
                            <div class="text-xs text-gray-500">Payé le {{ $salaire->date_paiement_formatted }}</div>
                            @endif
                        </div>
                        
                        @if($salaire->statut === 'paye' && $salaire->paiements->isNotEmpty())
                            @php $paiement = $salaire->paiements->first() @endphp
                            <div class="border-t border-gray-100 pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Détails du paiement</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Mode de paiement</span>
                                        <span class="font-medium">{{ ucfirst($paiement->mode_paiement) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Référence</span>
                                        <span class="font-mono">{{ $paiement->reference }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Date d'opération</span>
                                        <span>{{ $paiement->date_operation->format('d/m/Y') }}</span>
                                    </div>
                                    @if($paiement->compte_bancaire)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Compte crédité</span>
                                        <span>{{ $paiement->compte_bancaire }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($salaire->reclamations->isNotEmpty())
                            @php $reclamation = $salaire->reclamations->first() @endphp
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">Réclamation</h4>
                                    <span class="px-2 py-1 text-xs rounded-full {{ 
                                        $reclamation->statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($reclamation->statut === 'en_cours' ? 'bg-blue-100 text-blue-800' : 
                                        ($reclamation->statut === 'resolue' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))
                                    }}">
                                        {{ ucfirst($reclamation->statut) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ Str::limit($reclamation->sujet, 50) }}</p>
                                <div class="mt-2 flex justify-end">
                                    <a href="{{ route('professeur.salaires.reclamations.show', $reclamation) }}" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                        Voir les détails
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Carte d'historique -->
            @if($historique->isNotEmpty())
            <div class="bg-white rounded-lg shadow overflow-hidden print:shadow-none print:border print:border-gray-200 print:break-before-page">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique</h3>
                    <div class="space-y-4">
                        @foreach($historique as $item)
                        <div class="relative pb-6">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-6 w-6 rounded-full bg-blue-500 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-0.5">
                                    <p class="text-sm text-gray-800">{{ $item['description'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['date'] }} par {{ $item['causer'] }}</p>
                                    @if(!empty($item['properties']))
                                    <div class="mt-1 text-xs text-gray-500 bg-gray-50 p-2 rounded">
                                        @foreach($item['properties'] as $key => $value)
                                            <div><span class="font-medium">{{ $key }}:</span> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Carte d'aide -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Besoin d'aide ?</h4>
                <p class="text-xs text-blue-700 mb-3">Si vous avez des questions concernant votre fiche de paie, n'hésitez pas à contacter le service des ressources humaines.</p>
                <button class="text-xs font-medium text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    Contacter les RH
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestion du chargement du PDF
    document.addEventListener('DOMContentLoaded', function() {
        const downloadButtons = document.querySelectorAll('a[href*="telecharger-fiche-paie"]');
        
        downloadButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const originalHtml = this.innerHTML;
                this.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Préparation du document...
                `;
                this.classList.add('opacity-75', 'cursor-not-allowed');
                
                // Rétablir le bouton après 5 secondes au cas où le téléchargement échouerait
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('opacity-75', 'cursor-not-allowed');
                }, 5000);
            });
        });
        
        // Amélioration de l'impression
        const printButton = document.querySelector('button[onclick="window.print()"]');
        if (printButton) {
            printButton.addEventListener('click', function() {
                // Ajouter un délai pour s'assurer que les styles d'impression sont appliqués
                setTimeout(() => {
                    window.print();
                }, 200);
            });
        }
    });
    // Scripts spécifiques à la page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Ajouter un message de chargement avant l'impression
        window.onbeforeprint = function() {
            document.body.classList.add('print-mode');
        };
        
        // Nettoyer après l'impression
        window.onafterprint = function() {
            document.body.classList.remove('print-mode');
        };
    });
</script>
@endpush
