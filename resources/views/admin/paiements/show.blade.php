@extends('admin.layout')

@section('title', 'Détails du Paiement #' . $paiement->id)

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- En-tête avec boutons d'action -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Détails du Paiement #{{ $paiement->reference ?? $paiement->id }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Effectué le {{ $paiement->date_paiement->format('d/m/Y') }} 
                    à {{ $paiement->date_paiement->format('H:i') }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.paiements.edit', $paiement) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Modifier
                </a>
                <button type="button" onclick="window.print()"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>

        <div class="px-4 py-5 sm:px-6">
            <div class="grid grid-cols-1 gap-y-8 gap-x-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Informations de base -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Informations générales</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Référence</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paiement->reference ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Type de paiement</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $paiement->type_paiement === 'mensualite' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($paiement->type_paiement) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                @php
                                    $statusClasses = [
                                        'complet' => 'bg-green-100 text-green-800',
                                        'partiel' => 'bg-yellow-100 text-yellow-800',
                                        'en_attente' => 'bg-gray-100 text-gray-800',
                                        'annule' => 'bg-red-100 text-red-800',
                                    ][$paiement->statut] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ ucfirst(str_replace('_', ' ', $paiement->statut)) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Mode de paiement</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $paiement->mode_paiement }}</dd>
                        </div>
                        @if($paiement->periode)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Période concernée</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $paiement->periode)->format('F Y') }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Informations financières -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Détails financiers</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Montant</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ number_format($paiement->montant, 2, ',', ' ') }} MAD
                            </dd>
                        </div>
                        @if($paiement->frais_supplementaires > 0)
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Frais supplémentaires</dt>
                            <dd class="text-sm text-gray-900">
                                + {{ number_format($paiement->frais_supplementaires, 2, ',', ' ') }} MAD
                            </dd>
                        </div>
                        @endif
                        @if($paiement->remise > 0)
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Remise appliquée</dt>
                            <dd class="text-sm text-green-600">
                                - {{ number_format($paiement->remise, 2, ',', ' ') }} MAD
                            </dd>
                        </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-200 font-medium">
                            <dt class="text-sm">Montant total</dt>
                            <dd class="text-sm">
                                {{ number_format($paiement->montant_total, 2, ',', ' ') }} MAD
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Informations sur l'élève -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Informations de l'élève</h4>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-600 font-medium">{{ substr($paiement->etudiant->nom, 0, 1) }}{{ substr($paiement->etudiant->prenom, 0, 1) }}</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ $paiement->etudiant->nom_complet }}
                            </h4>
                            <div class="mt-1 text-sm text-gray-500">
                                {{ $paiement->etudiant->classe->nom ?? 'Aucune classe' }}
                            </div>
                            <div class="mt-2 flex space-x-2">
                                <a href="{{ route('admin.etudiants.show', $paiement->etudiant) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Voir le profil
                                </a>
                                <a href="{{ route('admin.paiements.index', ['etudiant_id' => $paiement->etudiant_id]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Voir les paiements
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes et historique -->
            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Notes -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Notes</h4>
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            @if($paiement->notes)
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $paiement->notes }}</p>
                            @else
                                <p class="text-sm text-gray-500 italic">Aucune note pour ce paiement.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Historique des modifications -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Historique</h4>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @php
                                $activities = $paiement->activities()->latest()->take(5)->get();
                            @endphp
                            
                            @forelse($activities as $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            @php
                                                $iconClasses = [
                                                    'created' => 'bg-green-500',
                                                    'updated' => 'bg-blue-500',
                                                    'deleted' => 'bg-red-500',
                                                ][$activity->event] ?? 'bg-gray-400';
                                            @endphp
                                            <span class="h-8 w-8 rounded-full {{ $iconClasses }} flex items-center justify-center ring-8 ring-white">
                                                @switch($activity->event)
                                                    @case('created')
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        @break
                                                    @case('updated')
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                        </svg>
                                                        @break
                                                    @default
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                @endswitch
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $activity->description }}
                                                    <span class="font-medium text-gray-900">{{ $activity->causer->name ?? 'Système' }}</span>
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $activity->created_at->toIso8601String() }}">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @empty
                                <li class="text-sm text-gray-500 py-4">Aucune activité récente.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section pour les pièces jointes -->
    @if($paiement->piecesJointes->count() > 0)
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Pièces jointes
            </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($paiement->piecesJointes as $piece)
                <li class="col-span-1 bg-white rounded-lg shadow divide-y divide-gray-200">
                    <div class="w-full flex items-center justify-between p-6 space-x-6">
                        <div class="flex-1 truncate">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-gray-900 text-sm font-medium truncate">{{ $piece->nom }}</h3>
                                <span class="flex-shrink-0 inline-block px-2 py-0.5 text-green-800 text-xs font-medium bg-green-100 rounded-full">
                                    {{ strtoupper(pathinfo($piece->chemin, PATHINFO_EXTENSION)) }}
                                </span>
                            </div>
                            <p class="mt-1 text-gray-500 text-sm truncate">
                                Ajouté le {{ $piece->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('admin.paiements.pieces-jointes.download', $piece) }}" 
                           class="w-8 h-8 bg-white inline-flex items-center justify-center text-gray-400 hover:text-gray-500 rounded-full">
                            <span class="sr-only">Télécharger</span>
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Section pour les actions supplémentaires -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Actions
            </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.paiements.recu', $paiement) }}" target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Reçu de paiement
                </a>
                
                <a href="{{ route('admin.paiements.edit', $paiement) }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Modifier le paiement
                </a>
                
                <button type="button"
                        onclick="confirm('Êtes-vous sûr de vouloir annuler ce paiement ?') ? document.getElementById('annuler-paiement-form').submit() : false"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    Annuler le paiement
                </button>
                
                <button type="button"
                        onclick="if(confirm('Êtes-vous sûr de vouloir supprimer définitivement ce paiement ?')) { document.getElementById('supprimer-paiement-form').submit() }"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Supprimer définitivement
                </button>
                
                <!-- Formulaires cachés pour les actions -->
                <form id="annuler-paiement-form" action="{{ route('admin.paiements.annuler', $paiement) }}" method="POST" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
                
                <form id="supprimer-paiement-form" action="{{ route('admin.paiements.destroy', $paiement) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Script pour gérer les interactions de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips si vous utilisez une bibliothèque comme Tippy.js
        // tippy('[data-tippy-content]');
        
        // Gestion de la confirmation de suppression
        document.querySelectorAll('[data-confirm]').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm(this.dataset.confirm)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
