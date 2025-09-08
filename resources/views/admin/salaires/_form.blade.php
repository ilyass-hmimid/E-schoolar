@php
    // Définir des valeurs par défaut si non définies
    $salaire = $salaire ?? null;
    $isEdit = isset($salaire);
    $professeurs = $professeurs ?? collect();
    
    // Valeurs par défaut pour les champs
    $defaults = [
        'professeur_id' => old('professeur_id', $salaire->professeur_id ?? ''),
        'periode' => old('periode', $salaire->periode ?? now()->format('Y-m')),
        'nb_heures' => old('nb_heures', $salaire->nb_heures ?? 0),
        'taux_horaire' => old('taux_horaire', $salaire->taux_horaire ?? 70),
        'salaire_brut' => old('salaire_brut', $salaire->salaire_brut ?? 0),
        'primes' => old('primes', $salaire->primes ?? 0),
        'retenues' => old('retenues', $salaire->retenues ?? 0),
        'salaire_net' => old('salaire_net', $salaire->salaire_net ?? 0),
        'statut' => old('statut', $salaire->statut ?? 'en_attente'),
        'date_paiement' => old('date_paiement', $salaire->date_paiement ? $salaire->date_paiement->format('Y-m-d') : now()->format('Y-m-d')),
        'type_paiement' => old('type_paiement', $salaire->type_paiement ?? 'virement'),
        'reference' => old('reference', $salaire->reference ?? ''),
        'est_avance' => old('est_avance', $salaire->est_avance ?? false),
        'notes' => old('notes', $salaire->notes ?? ''),
    ];
    
    // Déterminer si les champs de paiement doivent être affichés
    $showPaiementFields = in_array($defaults['statut'], ['paye']);
@endphp

<div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
    <!-- Professeur (caché en mode édition) -->
    @if($isEdit)
        <div class="sm:col-span-6">
            <label class="block text-sm font-medium text-gray-700">
                Professeur
            </label>
            <div class="mt-1">
                <div class="rounded-md bg-gray-100 px-3 py-2 border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img class="h-10 w-10 rounded-full" 
                                 src="{{ $salaire->professeur->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($salaire->professeur->nom_complet).'&color=7F9CF5&background=EBF4FF' }}" 
                                 alt="{{ $salaire->professeur->nom_complet }}">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $salaire->professeur->nom_complet }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $salaire->professeur->matricule }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Sélection du professeur en création -->
        <div class="sm:col-span-3">
            <label for="professeur_id" class="block text-sm font-medium text-gray-700 required">
                Professeur
            </label>
            <div class="mt-1">
                <select id="professeur_id" name="professeur_id" required
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Sélectionnez un professeur</option>
                    @foreach($professeurs as $professeur)
                        <option value="{{ $professeur->id }}" {{ $defaults['professeur_id'] == $professeur->id ? 'selected' : '' }}>
                            {{ $professeur->nom_complet }} ({{ $professeur->matricule }})
                        </option>
                    @endforeach
                </select>
            </div>
            @error('professeur_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif
    
    <!-- Période -->
    <div class="sm:col-span-3">
        <label for="periode" class="block text-sm font-medium text-gray-700 required">
            Période
        </label>
        <div class="mt-1">
            <input type="month" name="periode" id="periode" required
                value="{{ $defaults['periode'] }}"
                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
        </div>
        @error('periode')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Nombre d'heures -->
    <div class="sm:col-span-2">
        <label for="nb_heures" class="block text-sm font-medium text-gray-700 required">
            Nombre d'heures
        </label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <input type="number" step="0.5" min="0" name="nb_heures" id="nb_heures" required
                value="{{ $defaults['nb_heures'] }}"
                class="salaire-field focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">heures</span>
            </div>
        </div>
        @error('nb_heures')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Taux horaire -->
    <div class="sm:col-span-2">
        <label for="taux_horaire" class="block text-sm font-medium text-gray-700 required">
            Taux horaire
        </label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">DH</span>
            </div>
            <input type="number" step="0.01" min="0" name="taux_horaire" id="taux_horaire" required
                value="{{ $defaults['taux_horaire'] }}"
                class="salaire-field focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">/h</span>
            </div>
        </div>
        @error('taux_horaire')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Salaire brut -->
    <div class="sm:col-span-2">
        <label for="salaire_brut" class="block text-sm font-medium text-gray-700">
            Salaire brut
        </label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">DH</span>
            </div>
            <input type="number" step="0.01" name="salaire_brut" id="salaire_brut" readonly
                value="{{ $defaults['salaire_brut'] }}"
                class="bg-gray-100 focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md">
        </div>
        @error('salaire_brut')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Primes -->
    <div class="sm:col-span-2">
        <label for="primes" class="block text-sm font-medium text-gray-700">
            Primes
        </label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">+</span>
            </div>
            <input type="number" step="0.01" min="0" name="primes" id="primes"
                value="{{ $defaults['primes'] }}"
                class="salaire-field focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 pr-12 sm:text-sm border-gray-300 rounded-md">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">DH</span>
            </div>
        </div>
        @error('primes')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Retenues -->
    <div class="sm:col-span-2">
        <label for="retenues" class="block text-sm font-medium text-gray-700">
            Retenues
        </label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">-</span>
            </div>
            <input type="number" step="0.01" min="0" name="retenues" id="retenues"
                value="{{ $defaults['retenues'] }}"
                class="salaire-field focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 pr-12 sm:text-sm border-gray-300 rounded-md">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">DH</span>
            </div>
        </div>
        @error('retenues')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Salaire net -->
    <div class="sm:col-span-2">
        <label for="salaire_net" class="block text-sm font-medium text-gray-700">
            Salaire net
        </label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">DH</span>
            </div>
            <input type="number" step="0.01" name="salaire_net" id="salaire_net" readonly
                value="{{ $defaults['salaire_net'] }}"
                class="bg-gray-100 font-medium text-gray-900 focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md">
        </div>
        @error('salaire_net')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Statut -->
    <div class="sm:col-span-3">
        <label for="statut" class="block text-sm font-medium text-gray-700 required">
            Statut
        </label>
        <div class="mt-1">
            <select id="statut" name="statut" required
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="en_attente" {{ $defaults['statut'] == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="paye" {{ $defaults['statut'] == 'paye' ? 'selected' : '' }}>Payé</option>
                <option value="retard" {{ $defaults['statut'] == 'retard' ? 'selected' : '' }}>En retard</option>
            </select>
        </div>
        @error('statut')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Date de paiement (conditionnelle) -->
    <div class="sm:col-span-3" id="date_paiement_container" style="{{ $showPaiementFields ? '' : 'display: none;' }}">
        <label for="date_paiement" class="block text-sm font-medium text-gray-700">
            Date de paiement
        </label>
        <div class="mt-1">
            <input type="date" name="date_paiement" id="date_paiement"
                value="{{ $defaults['date_paiement'] }}"
                {{ $showPaiementFields ? 'required' : '' }}
                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
        </div>
        @error('date_paiement')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Type de paiement (conditionnel) -->
    <div class="sm:col-span-3" id="type_paiement_container" style="{{ $showPaiementFields ? '' : 'display: none;' }}">
        <label for="type_paiement" class="block text-sm font-medium text-gray-700">
            Mode de paiement
        </label>
        <div class="mt-1">
            <select id="type_paiement" name="type_paiement"
                {{ $showPaiementFields ? 'required' : '' }}
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="virement" {{ $defaults['type_paiement'] == 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                <option value="cheque" {{ $defaults['type_paiement'] == 'cheque' ? 'selected' : '' }}>Chèque</option>
                <option value="especes" {{ $defaults['type_paiement'] == 'especes' ? 'selected' : '' }}>Espèces</option>
            </select>
        </div>
        @error('type_paiement')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Référence (conditionnelle) -->
    <div class="sm:col-span-3" id="reference_container" style="{{ $showPaiementFields ? '' : 'display: none;' }}">
        <label for="reference" class="block text-sm font-medium text-gray-700">
            Référence
        </label>
        <div class="mt-1">
            <input type="text" name="reference" id="reference"
                value="{{ $defaults['reference'] }}"
                placeholder="N° de chèque, référence virement, etc."
                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
        </div>
        @error('reference')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Est une avance -->
    <div class="sm:col-span-6">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="est_avance" name="est_avance" type="checkbox" value="1"
                    {{ $defaults['est_avance'] ? 'checked' : '' }}
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="est_avance" class="font-medium text-gray-700">Ce paiement est une avance sur salaire</label>
                <p class="text-gray-500">Cochez cette case s'il s'agit d'un acompte ou d'une avance sur le salaire.</p>
            </div>
        </div>
    </div>
    
    <!-- Notes -->
    <div class="sm:col-span-6">
        <label for="notes" class="block text-sm font-medium text-gray-700">
            Notes
        </label>
        <div class="mt-1">
            <textarea id="notes" name="notes" rows="3"
                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ $defaults['notes'] }}</textarea>
        </div>
        <p class="mt-2 text-sm text-gray-500">Ajoutez des notes ou des détails supplémentaires sur ce salaire.</p>
        @error('notes')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'affichage conditionnel des champs de paiement
        const statutSelect = document.getElementById('statut');
        const datePaiementContainer = document.getElementById('date_paiement_container');
        const typePaiementContainer = document.getElementById('type_paiement_container');
        const referenceContainer = document.getElementById('reference_container');
        const datePaiementInput = document.getElementById('date_paiement');
        const typePaiementSelect = document.getElementById('type_paiement');
        
        function togglePaiementFields() {
            const isPaye = statutSelect.value === 'paye';
            datePaiementContainer.style.display = isPaye ? 'block' : 'none';
            typePaiementContainer.style.display = isPaye ? 'block' : 'none';
            referenceContainer.style.display = isPaye ? 'block' : 'none';
            
            // Rendre les champs obligatoires ou non
            datePaiementInput.required = isPaye;
            typePaiementSelect.required = isPaye;
        }
        
        // Écouter les changements sur le statut
        if (statutSelect) {
            statutSelect.addEventListener('change', togglePaiementFields);
        }
        
        // Calcul automatique du salaire
        const nbHeuresInput = document.getElementById('nb_heures');
        const tauxHoraireInput = document.getElementById('taux_horaire');
        const primesInput = document.getElementById('primes');
        const retenuesInput = document.getElementById('retenues');
        const salaireBrutInput = document.getElementById('salaire_brut');
        const salaireNetInput = document.getElementById('salaire_net');
        
        function calculerSalaire() {
            const nbHeures = parseFloat(nbHeuresInput.value) || 0;
            const tauxHoraire = parseFloat(tauxHoraireInput.value) || 0;
            const primes = parseFloat(primesInput.value) || 0;
            const retenues = parseFloat(retenuesInput.value) || 0;
            
            // Calcul du salaire brut
            const salaireBrut = nbHeures * tauxHoraire;
            if (salaireBrutInput) salaireBrutInput.value = salaireBrut.toFixed(2);
            
            // Calcul du salaire net
            const salaireNet = salaireBrut + primes - retenues;
            if (salaireNetInput) salaireNetInput.value = salaireNet.toFixed(2);
        }
        
        // Écouter les changements sur les champs de calcul
        [nbHeuresInput, tauxHoraireInput, primesInput, retenuesInput].forEach(input => {
            if (input) input.addEventListener('input', calculerSalaire);
        });
        
        // Calculer le salaire au chargement de la page
        calculerSalaire();
    });
</script>
@endpush
