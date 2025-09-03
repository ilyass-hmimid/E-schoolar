@php
    $isEdit = isset($paiement) && $paiement->exists;
    $route = $isEdit ? route('admin.paiements.eleves.update', $paiement) : route('admin.paiements.eleves.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Modifier le Paiement' : 'Nouveau Paiement';
    $submitText = $isEdit ? 'Mettre à jour' : 'Enregistrer';
    
    // Default values
    $defaults = [
        'eleve_id' => old('eleve_id', $paiement->eleve_id ?? ''),
        'mois' => old('mois', $paiement->mois ?? now()->format('Y-m')),
        'montant' => old('montant', $paiement->montant ?? ''),
        'statut' => old('statut', $paiement->statut ?? 'impaye'),
        'mode_paiement' => old('mode_paiement', $paiement->mode_paiement ?? ''),
        'reference_paiement' => old('reference_paiement', $paiement->reference_paiement ?? ''),
        'date_paiement' => old('date_paiement', $paiement->date_paiement ? $paiement->date_paiement->format('Y-m-d') : now()->format('Y-m-d')),
        'notes' => old('notes', $paiement->notes ?? ''),
    ];
@endphp

<x-form.form :action="$route" :method="$method">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
                </div>
                <div class="card-body">
                    <!-- Student Selection -->
                    <div class="mb-3">
                        <x-form.label for="eleve_id" :required="true">Élève</x-form.label>
                        <x-form.select 
                            name="eleve_id" 
                            id="eleve_id" 
                            :options="$eleves" 
                            :selected="$defaults['eleve_id']"
                            :required="true"
                            placeholder="Sélectionner un élève"
                        />
                        @error('eleve_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Month -->
                        <div class="col-md-6">
                            <x-form.label for="mois" :required="true">Mois</x-form.label>
                            <x-form.input 
                                type="month" 
                                name="mois" 
                                id="mois" 
                                :value="$defaults['mois']" 
                                :required="true"
                            />
                            @error('mois')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <x-form.label for="montant" :required="true">Montant (DH)</x-form.label>
                            <x-form.input 
                                type="number" 
                                name="montant" 
                                id="montant" 
                                :value="$defaults['montant']" 
                                :required="true"
                                min="0"
                                step="0.01"
                            />
                            @error('montant')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Status -->
                        <div class="col-md-4">
                            <x-form.label :required="true">Statut</x-form.label>
                            <x-form.radio-group 
                                name="statut" 
                                :options="[
                                    'paye' => 'Payé',
                                    'en_retard' => 'En retard',
                                    'impaye' => 'Impayé'
                                ]" 
                                :checked="$defaults['statut']"
                                :inline="true"
                            />
                            @error('statut')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-4">
                            <x-form.label for="mode_paiement">Mode de Paiement</x-form.label>
                            <x-form.select 
                                name="mode_paiement" 
                                id="mode_paiement"
                                :options="[
                                    '' => 'Sélectionner un mode de paiement',
                                    'especes' => 'Espèces',
                                    'cheque' => 'Chèque',
                                    'virement' => 'Virement',
                                    'carte' => 'Carte bancaire',
                                    'autre' => 'Autre'
                                ]"
                                :selected="$defaults['mode_paiement']"
                            />
                            @error('mode_paiement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Date -->
                        <div class="col-md-4">
                            <x-form.label for="date_paiement">Date de Paiement</x-form.label>
                            <x-form.date 
                                name="date_paiement" 
                                id="date_paiement" 
                                :value="$defaults['date_paiement']"
                            />
                            @error('date_paiement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Reference -->
                    <div class="mt-3">
                        <x-form.label for="reference_paiement">Référence Paiement</x-form.label>
                        <x-form.input 
                            type="text" 
                            name="reference_paiement" 
                            id="reference_paiement" 
                            :value="$defaults['reference_paiement']"
                            placeholder="N° de chèque, référence virement, etc."
                        />
                        @error('reference_paiement')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mt-3">
                        <x-form.label for="notes">Notes</x-form.label>
                        <x-form.textarea 
                            name="notes" 
                            id="notes" 
                            :value="$defaults['notes']"
                            rows="3"
                            placeholder="Informations complémentaires..."
                        />
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Summary Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Résumé</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Dernier Paiement:</strong>
                        <p id="last-payment" class="mb-0">-</p>
                    </div>
                    <div class="mb-3">
                        <strong>Solde Actuel:</strong>
                        <p id="current-balance" class="mb-0">-</p>
                    </div>
                    <div class="mb-3">
                        <strong>Statut:</strong>
                        <p id="payment-status" class="mb-0">
                            @php
                                $statusClass = [
                                    'paye' => 'text-success',
                                    'en_retard' => 'text-warning',
                                    'impaye' => 'text-danger'
                                ][$defaults['statut']] ?? '';
                            @endphp
                            <span class="{{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $defaults['statut'])) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>{{ $submitText }}
                    </button>
                    
                    @if($isEdit)
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" 
                            onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-form.form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update status display when radio button changes
        document.querySelectorAll('input[name="statut"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const statusText = {
                    'paye': 'Payé',
                    'en_retard': 'En retard',
                    'impaye': 'Impayé'
                };
                
                const statusClass = {
                    'paye': 'text-success',
                    'en_retard': 'text-warning',
                    'impaye': 'text-danger'
                };
                
                const status = this.value;
                const statusElement = document.getElementById('payment-status');
                statusElement.innerHTML = `<span class="${statusClass[status]}">${statusText[status]}</span>`;
            });
        });

        // Load student payment history when student changes
        const eleveSelect = document.getElementById('eleve_id');
        if (eleveSelect) {
            eleveSelect.addEventListener('change', function() {
                const eleveId = this.value;
                if (!eleveId) {
                    document.getElementById('last-payment').textContent = '-';
                    document.getElementById('current-balance').textContent = '-';
                    return;
                }

                // In a real app, you would fetch this data via AJAX
                // fetch(`/api/eleves/${eleveId}/payment-history`)
                //     .then(response => response.json())
                //     .then(data => {
                //         document.getElementById('last-payment').textContent = data.last_payment || '-';
                //         document.getElementById('current-balance').textContent = data.balance || '-';
                //     });
            });
        }
    });
</script>
@endpush
