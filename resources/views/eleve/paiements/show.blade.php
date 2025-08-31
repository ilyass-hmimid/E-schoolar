@extends('layouts.eleve')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('eleve.paiements.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste des paiements
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Paiement #{{ $paiement->reference }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $paiement->date_paiement->format('d F Y') }}
                    </p>
                </div>
                <div>
                    @if($paiement->statut === 'payé')
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Payé
                        </span>
                    @elseif($paiement->statut === 'en_attente')
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            En attente
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Échoué
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="px-6 py-5">
            <div class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Référence
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $paiement->reference }}
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Date de paiement
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $paiement->date_paiement->format('d/m/Y') }}
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Montant
                    </dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ number_format($paiement->montant, 2) }} DH
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Méthode de paiement
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ ucfirst($paiement->methode) }}
                    </dd>
                </div>

                @if($paiement->methode === 'virement')
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Référence de virement
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $paiement->reference_virement ?? 'Non spécifiée' }}
                        </dd>
                    </div>
                @endif

                @if($paiement->description)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Description
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                            {{ $paiement->description }}
                        </dd>
                    </div>
                @endif

                @if($paiement->piece_jointe)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Pièce jointe
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ Storage::url($paiement->piece_jointe) }}" 
                               target="_blank" 
                               class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Télécharger le reçu
                            </a>
                        </dd>
                    </div>
                @endif
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 text-right sm:px-6">
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimer
            </button>
            
            @if($paiement->statut === 'en_attente' && $paiement->methode === 'en_ligne')
                <a href="#" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Payer maintenant
                </a>
            @endif
        </div>
    </div>

    <!-- Détails de la facture -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Détails de la facture
            </h3>
        </div>
        
        <div class="px-6 py-5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($paiement->lignes as $ligne)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ligne->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ number_format($ligne->montant, 2) }} DH
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Total
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                {{ number_format($paiement->montant, 2) }} DH
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable, #printable * {
            visibility: visible;
        }
        #printable {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection
