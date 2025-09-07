@extends('admin.layout')

@section('title', 'Nouveau Paiement')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Enregistrer un nouveau paiement
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Remplissez les informations ci-dessous pour enregistrer un nouveau paiement.
            </p>
        </div>

        <form action="{{ route('admin.paiements.store') }}" method="POST" class="px-4 py-5 sm:p-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <!-- Sélection de l'élève -->
                <div class="sm:col-span-3">
                    <label for="etudiant_id" class="block text-sm font-medium text-gray-700">Élève *</label>
                    <div class="mt-1">
                        <select id="etudiant_id" name="etudiant_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Sélectionner un élève</option>
                            @foreach($etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}" {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                    {{ $etudiant->nom_complet }} ({{ $etudiant->matricule ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('etudiant_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Type de paiement -->
                <div class="sm:col-span-3">
                    <label for="type_paiement" class="block text-sm font-medium text-gray-700">Type de paiement *</label>
                    <div class="mt-1">
                        <select id="type_paiement" name="type_paiement" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Sélectionner un type</option>
                            <option value="mensualite" {{ old('type_paiement') == 'mensualite' ? 'selected' : '' }}>Mensualité</option>
                            <option value="inscription" {{ old('type_paiement') == 'inscription' ? 'selected' : '' }}>Frais d'inscription</option>
                            <option value="autre" {{ old('type_paiement') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type_paiement')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Montant -->
                <div class="sm:col-span-2">
                    <label for="montant" class="block text-sm font-medium text-gray-700">Montant (MAD) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">MAD</span>
                        </div>
                        <input type="number" step="0.01" name="montant" id="montant" required
                            value="{{ old('montant') }}"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-16 sm:pl-14 sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('montant')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de paiement -->
                <div class="sm:col-span-2">
                    <label for="date_paiement" class="block text-sm font-medium text-gray-700">Date de paiement *</label>
                    <div class="mt-1">
                        <input type="date" name="date_paiement" id="date_paiement" required
                            value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('date_paiement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mode de paiement -->
                <div class="sm:col-span-2">
                    <label for="mode_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement *</label>
                    <div class="mt-1">
                        <select id="mode_paiement" name="mode_paiement" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="carte" {{ old('mode_paiement') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                        </select>
                    </div>
                    @error('mode_paiement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Référence -->
                <div class="sm:col-span-3">
                    <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
                    <div class="mt-1">
                        <input type="text" name="reference" id="reference"
                            value="{{ old('reference') }}" 
                            placeholder="N° de chèque, référence virement, etc."
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('reference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div class="sm:col-span-3">
                    <label for="statut" class="block text-sm font-medium text-gray-700">Statut *</label>
                    <div class="mt-1">
                        <select id="statut" name="statut" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="complet" {{ old('statut') == 'complet' ? 'selected' : 'selected' }}>Complet</option>
                            <option value="partiel" {{ old('statut') == 'partiel' ? 'selected' : '' }}>Partiel</option>
                            <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mois/Année concernés -->
                <div class="sm:col-span-3">
                    <label for="periode" class="block text-sm font-medium text-gray-700">Période concernée</label>
                    <div class="mt-1">
                        <input type="month" name="periode" id="periode"
                            value="{{ old('periode', now()->format('Y-m')) }}"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Pour les mensualités, indiquez le mois concerné</p>
                    @error('periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="sm:col-span-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <div class="mt-1">
                        <textarea id="notes" name="notes" rows="3"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('notes') }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Informations complémentaires sur ce paiement</p>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.paiements.index') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </a>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Enregistrer le paiement
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Script pour gérer la sélection de l'élève et le chargement de ses informations
    document.addEventListener('DOMContentLoaded', function() {
        const etudiantSelect = document.getElementById('etudiant_id');
        
        // Charger les informations de l'élève sélectionné
        function loadEtudiantInfo(etudiantId) {
            if (!etudiantId) return;
            
            // Ici, vous pourriez faire un appel AJAX pour charger les informations de l'élève
            // et pré-remplir certains champs si nécessaire
            console.log('Chargement des informations pour l\'élève ID:', etudiantId);
            
            // Exemple de pré-remplissage conditionnel
            // fetch(`/api/etudiants/${etudiantId}`)
            //     .then(response => response.json())
            //     .then(data => {
            //         // Mettre à jour les champs nécessaires
            //     });
        }
        
        // Écouter les changements de sélection d'élève
        etudiantSelect.addEventListener('change', function() {
            loadEtudiantInfo(this.value);
        });
        
        // Charger les informations au chargement si un élève est déjà sélectionné
        if (etudiantSelect.value) {
            loadEtudiantInfo(etudiantSelect.value);
        }
        
        // Initialiser les sélecteurs avec Select2 si nécessaire
        // $('select').select2({
        //     theme: 'bootstrap4'
        // });
    });
</script>
@endpush
