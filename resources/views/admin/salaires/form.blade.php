@php
    $isEdit = isset($salaire) && $salaire->exists;
    $route = $isEdit ? route('admin.salaires.update', $salaire) : route('admin.salaires.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Modifier le salaire' : 'Créer un nouveau salaire';
    $submitText = $isEdit ? 'Mettre à jour' : 'Créer';
    
    // Définir les valeurs par défaut pour éviter les erreurs
    $salaire = $salaire ?? new \App\Models\Salaire();
    $professeurs = $professeurs ?? [];
@endphp

@extends('layouts.admin')

@section('title', $title)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding-top: 4px;
        border-color: #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--multiple,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }
    .select2-dropdown {
        border-color: #d1d5db;
        border-radius: 0.375rem;
        margin-top: 4px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                <p class="text-sm text-gray-500">
                    @if($isEdit)
                        Mise à jour des informations du salaire
                    @else
                        Ajoutez un nouveau salaire au système
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('admin.salaires.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour à la liste
                </a>
            </div>
        </div>
        
        <nav class="flex mt-4" aria-label="Breadcrumb">
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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            {{ $isEdit ? 'Modifier' : 'Nouveau' }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <form action="{{ $route }}" method="POST" id="salaireForm">
            @csrf
            @method($method)
            
            <div class="px-4 py-5 sm:p-6">
                <!-- Messages d'erreur -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Il y a {{ $errors->count() }} erreur(s) dans le formulaire
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="space-y-6">
                    <!-- Section Informations de base -->
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Informations de base</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Renseignez les informations générales concernant ce salaire.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Professeur -->
                        <div class="col-span-1">
                            <label for="professeur_id" class="block text-sm font-medium text-gray-700">
                                Professeur <span class="text-red-500">*</span>
                            </label>
                            <select id="professeur_id" name="professeur_id" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un professeur</option>
                                @foreach($professeurs as $prof)
                                    <option value="{{ $prof->id }}" 
                                        {{ old('professeur_id', $salaire->professeur_id) == $prof->id ? 'selected' : '' }}>
                                        {{ $prof->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Période -->
                        <div class="col-span-1">
                            <label for="periode" class="block text-sm font-medium text-gray-700">
                                Période <span class="text-red-500">*</span>
                            </label>
                            <input type="month" name="periode" id="periode" required
                                value="{{ old('periode', $salaire->periode ? \Carbon\Carbon::parse($salaire->periode)->format('Y-m') : '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <!-- Nombre d'heures -->
                        <div class="col-span-1">
                            <label for="nb_heures" class="block text-sm font-medium text-gray-700">
                                Nombre d'heures <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="nb_heures" id="nb_heures" required
                                    min="0" step="0.5"
                                    value="{{ old('nb_heures', $salaire->nb_heures) }}"
                                    class="block w-full pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">heures</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Taux horaire -->
                        <div class="col-span-1">
                            <label for="taux_horaire" class="block text-sm font-medium text-gray-700">
                                Taux horaire (DH) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">DH</span>
                                </div>
                                <input type="number" name="taux_horaire" id="taux_horaire" required
                                    min="0" step="0.01"
                                    value="{{ old('taux_horaire', $salaire->taux_horaire) }}"
                                    class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">/h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Rémunération -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Rémunération</h3>
                            <button type="button" id="calculateSalaire" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-calculator mr-1"></i> Calculer automatiquement
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Détails de la rémunération et des retenues.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Salaire de base -->
                        <div class="col-span-1 bg-gray-50 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Salaire de base</h4>
                            <div class="space-y-3">
                                <div>
                                    <label for="salaire_brut" class="block text-sm font-medium text-gray-700">
                                        Salaire brut
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="salaire_brut" id="salaire_brut"
                                            value="{{ old('salaire_brut', $salaire->salaire_brut) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="prime_anciennete" class="block text-sm font-medium text-gray-700">
                                        Prime d'ancienneté
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="prime_anciennete" id="prime_anciennete"
                                            value="{{ old('prime_anciennete', $salaire->prime_anciennete) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="prime_rendement" class="block text-sm font-medium text-gray-700">
                                        Prime de rendement
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="prime_rendement" id="prime_rendement"
                                            value="{{ old('prime_rendement', $salaire->prime_rendement) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="indemnite_transport" class="block text-sm font-medium text-gray-700">
                                        Indemnité de transport
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="indemnite_transport" id="indemnite_transport"
                                            value="{{ old('indemnite_transport', $salaire->indemnite_transport) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="autres_primes" class="block text-sm font-medium text-gray-700">
                                        Autres primes
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="autres_primes" id="autres_primes"
                                            value="{{ old('autres_primes', $salaire->autres_primes) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Retenues -->
                        <div class="col-span-1 bg-red-50 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Retenues</h4>
                            <div class="space-y-3">
                                <div>
                                    <label for="cnss" class="block text-sm font-medium text-gray-700">
                                        CNSS
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="cnss" id="cnss"
                                            value="{{ old('cnss', $salaire->cnss) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="ir" class="block text-sm font-medium text-gray-700">
                                        IR (Impôt sur le revenu)
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="ir" id="ir"
                                            value="{{ old('ir', $salaire->ir) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="retenues_diverses" class="block text-sm font-medium text-gray-700">
                                        Retenues diverses
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">DH</span>
                                        </div>
                                        <input type="number" name="retenues_diverses" id="retenues_diverses"
                                            value="{{ old('retenues_diverses', $salaire->retenues_diverses) }}"
                                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div class="pt-2 border-t border-red-200">
                                    <div class="flex justify-between text-sm font-medium text-gray-900">
                                        <span>Total des retenues</span>
                                        <span id="total_retenues">0,00 DH</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Récapitulatif -->
                        <div class="col-span-1 sm:col-span-2 bg-blue-50 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Récapitulatif</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-xs font-medium text-gray-500">Total brut</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900" id="total_brut">0,00 DH</p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-xs font-medium text-gray-500">Total retenues</p>
                                    <p class="mt-1 text-lg font-semibold text-red-600" id="total_retenues_recap">0,00 DH</p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm border-2 border-blue-500">
                                    <p class="text-xs font-medium text-gray-500">Salaire net</p>
                                    <p class="mt-1 text-xl font-bold text-blue-700" id="salaire_net">0,00 DH</p>
                                    <input type="hidden" name="salaire_net" id="salaire_net_input">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Paiement -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Paiement</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Informations relatives au paiement de ce salaire.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Statut -->
                        <div class="col-span-1">
                            <label for="statut" class="block text-sm font-medium text-gray-700">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select id="statut" name="statut" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="en_attente" {{ old('statut', $salaire->statut) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="paye" {{ old('statut', $salaire->statut) == 'paye' ? 'selected' : '' }}>Payé</option>
                                <option value="retard" {{ old('statut', $salaire->statut) == 'retard' ? 'selected' : '' }}>En retard</option>
                            </select>
                        </div>
                        
                        <!-- Type de paiement -->
                        <div class="col-span-1">
                            <label for="type_paiement" class="block text-sm font-medium text-gray-700">
                                Type de paiement
                            </label>
                            <select id="type_paiement" name="type_paiement"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un type</option>
                                <option value="virement" {{ old('type_paiement', $salaire->type_paiement) == 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                                <option value="cheque" {{ old('type_paiement', $salaire->type_paiement) == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                <option value="especes" {{ old('type_paiement', $salaire->type_paiement) == 'especes' ? 'selected' : '' }}>Espèces</option>
                            </select>
                        </div>
                        
                        <!-- Date de paiement -->
                        <div class="col-span-1">
                            <label for="date_paiement" class="block text-sm font-medium text-gray-700">
                                Date de paiement
                            </label>
                            <input type="date" name="date_paiement" id="date_paiement"
                                value="{{ old('date_paiement', $salaire->date_paiement ? \Carbon\Carbon::parse($salaire->date_paiement)->format('Y-m-d') : '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <!-- Référence -->
                        <div class="col-span-1">
                            <label for="reference" class="block text-sm font-medium text-gray-700">
                                Référence
                            </label>
                            <input type="text" name="reference" id="reference"
                                value="{{ old('reference', $salaire->reference) }}"
                                placeholder="N° de chèque, référence virement, etc."
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <!-- Notes -->
                        <div class="col-span-1 sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Notes
                            </label>
                            <div class="mt-1
                                <textarea id="notes" name="notes" rows="3"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('notes', $salaire->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <a href="{{ route('admin.salaires.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $submitText }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('#professeur_id').select2({
            placeholder: 'Sélectionner un professeur',
            allowClear: true,
            width: '100%'
        });
        
        // Fonction pour calculer le salaire
        function calculerSalaire() {
            // Récupération des valeurs
            const nbHeures = parseFloat($('#nb_heures').val()) || 0;
            const tauxHoraire = parseFloat($('#taux_horaire').val()) || 0;
            const primeAnciennete = parseFloat($('#prime_anciennete').val()) || 0;
            const primeRendement = parseFloat($('#prime_rendement').val()) || 0;
            const indemniteTransport = parseFloat($('#indemnite_transport').val()) || 0;
            const autresPrimes = parseFloat($('#autres_primes').val()) || 0;
            const cnss = parseFloat($('#cnss').val()) || 0;
            const ir = parseFloat($('#ir').val()) || 0;
            const retenuesDiverses = parseFloat($('#retenues_diverses').val()) || 0;
            
            // Calcul du salaire brut
            const salaireBrut = nbHeures * tauxHoraire;
            const totalPrimes = primeAnciennete + primeRendement + indemniteTransport + autresPrimes;
            const totalBrut = salaireBrut + totalPrimes;
            
            // Calcul des retenues
            const totalRetenues = cnss + ir + retenuesDiverses;
            
            // Calcul du salaire net
            const salaireNet = Math.max(0, totalBrut - totalRetenues);
            
            // Mise à jour des champs
            $('#salaire_brut').val(salaireBrut.toFixed(2));
            $('#total_brut').text(totalBrut.toFixed(2) + ' DH');
            $('#total_retenues').text(totalRetenues.toFixed(2) + ' DH');
            $('#total_retenues_recap').text(totalRetenues.toFixed(2) + ' DH');
            $('#salaire_net').text(salaireNet.toFixed(2) + ' DH');
            $('#salaire_net_input').val(salaireNet.toFixed(2));
        }
        
        // Calcul automatique lors du clic sur le bouton
        $('#calculateSalaire').on('click', function(e) {
            e.preventDefault();
            calculerSalaire();
        });
        
        // Calcul automatique lors de la modification des champs
        $('input[type="number"]').on('change keyup', function() {
            calculerSalaire();
        });
        
        // Calcul initial au chargement de la page
        calculerSalaire();
        
        // Gestion de l'affichage conditionnel des champs de paiement
        function togglePaiementFields() {
            const statut = $('#statut').val();
            if (statut === 'paye') {
                $('#type_paiement, #date_paiement, #reference').closest('.col-span-1').show();
            } else {
                $('#type_paiement, #date_paiement, #reference').closest('.col-span-1').hide();
            }
        }
        
        // Au changement du statut
        $('#statut').on('change', function() {
            togglePaiementFields();
            
            // Si le statut est "payé" et que la date de paiement est vide, on la met à aujourd'hui
            if ($(this).val() === 'paye' && !$('#date_paiement').val()) {
                const today = new Date().toISOString().split('T')[0];
                $('#date_paiement').val(today);
            }
        });
        
        // Initialisation
        togglePaiementFields();
    });
    
    // Gestion de la soumission du formulaire
    document.getElementById('salaireForm').addEventListener('submit', function(e) {
        // Validation supplémentaire si nécessaire
        const statut = document.getElementById('statut').value;
        const typePaiement = document.getElementById('type_paiement').value;
        const datePaiement = document.getElementById('date_paiement').value;
        
        if (statut === 'paye' && (!typePaiement || !datePaiement)) {
            e.preventDefault();
            alert('Veuillez renseigner le type de paiement et la date de paiement pour un salaire marqué comme payé.');
            return false;
        }
        
        return true;
    });
</script>
@endpush
