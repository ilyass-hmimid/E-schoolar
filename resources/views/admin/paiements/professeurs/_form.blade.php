@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informations du Paiement</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="professeur_id" class="form-label">Enseignant <span class="text-danger">*</span></label>
                            <select name="professeur_id" id="professeur_id" class="form-select @error('professeur_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un enseignant</option>
                                @foreach($professeurs as $professeur)
                                    <option value="{{ $professeur->id }}" 
                                        {{ old('professeur_id', $paiement->professeur_id ?? '') == $professeur->id ? 'selected' : '' }}
                                        data-taux-horaire="{{ $professeur->taux_horaire ?? 0 }}"
                                        data-heures-mensuelles="{{ $professeur->heures_mensuelles ?? 0 }}">
                                        {{ $professeur->nom }} {{ $professeur->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('professeur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mois" class="form-label">Mois <span class="text-danger">*</span></label>
                            <input type="month" class="form-control @error('mois') is-invalid @enderror" 
                                id="mois" name="mois" 
                                value="{{ old('mois', $paiement->mois ?? '') }}" required>
                            @error('mois')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="heures_travaillees" class="form-label">Heures travaillées</label>
                            <input type="number" step="0.5" min="0" class="form-control @error('heures_travaillees') is-invalid @enderror" 
                                id="heures_travaillees" name="heures_travaillees" 
                                value="{{ old('heures_travaillees', $paiement->heures_travaillees ?? '') }}">
                            @error('heures_travaillees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="taux_horaire" class="form-label">Taux horaire (DH)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('taux_horaire') is-invalid @enderror" 
                                    id="taux_horaire" name="taux_horaire" 
                                    value="{{ old('taux_horaire', $paiement->taux_horaire ?? '') }}">
                                <span class="input-group-text">DH/h</span>
                                @error('taux_horaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant total <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('montant') is-invalid @enderror" 
                                    id="montant" name="montant" 
                                    value="{{ old('montant', $paiement->montant ?? '') }}" required>
                                <span class="input-group-text">DH</span>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                @foreach($statuts as $key => $label)
                                    <option value="{{ $key }}" 
                                        {{ old('statut', $paiement->statut ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="mode_paiement" class="form-label">Mode de paiement</label>
                            <select name="mode_paiement" id="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                                <option value="">Sélectionner un mode de paiement</option>
                                @foreach($methodes as $methode)
                                    <option value="{{ $methode->value }}" 
                                        {{ old('mode_paiement', $paiement->mode_paiement ?? '') == $methode->value ? 'selected' : '' }}>
                                        {{ $methode->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mode_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date_paiement" class="form-label">Date de paiement</label>
                            <input type="date" class="form-control @error('date_paiement') is-invalid @enderror" 
                                id="date_paiement" name="date_paiement" 
                                value="{{ old('date_paiement', isset($paiement->date_paiement) ? $paiement->date_paiement->format('Y-m-d') : '') }}">
                            @error('date_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference_paiement" class="form-label">Référence de paiement</label>
                            <input type="text" class="form-control @error('reference_paiement') is-invalid @enderror" 
                                id="reference_paiement" name="reference_paiement" 
                                value="{{ old('reference_paiement', $paiement->reference_paiement ?? '') }}">
                            @error('reference_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="enregistre_par" class="form-label">Enregistré par</label>
                            <input type="text" class="form-control" 
                                value="{{ $paiement->enregistrePar->name ?? Auth::user()->name }}" disabled>
                            <input type="hidden" name="enregistre_par" value="{{ $paiement->enregistre_par ?? Auth::id() }}">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                        id="notes" name="notes" rows="3">{{ old('notes', $paiement->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Résumé</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="border rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-width: 3px !important;">
                        <h4 class="mb-0" id="montantAffiche">
                            {{ number_format(old('montant', $paiement->montant ?? 0), 2, ',', ' ') }} DH
                        </h4>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-{{ isset($paiement) ? ($paiement->statut === 'paye' ? 'success' : ($paiement->statut === 'en_retard' ? 'warning' : 'danger')) : 'secondary' }}" id="statutAffiche">
                            {{ isset($paiement) ? ($statuts[$paiement->statut] ?? $paiement->statut) : 'Nouveau' }}
                        </span>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-2">
                    <strong>Dernier paiement:</strong>
                    <span id="dernierPaiement">
                        {{ $paiement->professeur->paiements()->latest('mois')->first() ? 
                           \Carbon\Carbon::createFromFormat('Y-m', $paiement->professeur->paiements()->latest('mois')->first()->mois)->format('F Y') : 
                           'Aucun paiement' }}
                    </span>
                </div>
                
                <div class="mb-2">
                    <strong>Heures mensuelles:</strong>
                    <span id="heuresMensuelles">{{ $paiement->heures_mensuelles ?? 0 }}</span>h
                </div>
                
                <div class="mb-2">
                    <strong>Taux horaire:</strong>
                    <span id="tauxHoraireAffiche">{{ number_format($paiement->taux_horaire ?? 0, 2, ',', ' ') }}</span> DH/h
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                    
                    <a href="{{ route('admin.paiements.professeurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profSelect = document.getElementById('professeur_id');
        const heuresTravaillees = document.getElementById('heures_travaillees');
        const tauxHoraire = document.getElementById('taux_horaire');
        const montantInput = document.getElementById('montant');
        const statutSelect = document.getElementById('statut');
        const datePaiement = document.getElementById('date_paiement');
        
        // Mettre à jour le montant lorsque les heures ou le taux changent
        function updateMontant() {
            const heures = parseFloat(heuresTravaillees.value) || 0;
            const taux = parseFloat(tauxHoraire.value) || 0;
            const montant = heures * taux;
            
            if (!isNaN(montant) && montant > 0) {
                montantInput.value = montant.toFixed(2);
                document.getElementById('montantAffiche').textContent = montant.toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' DH';
            }
        }
        
        // Mettre à jour le statut affiché
        function updateStatutAffiche() {
            const statut = statutSelect.value;
            const statutBadge = document.getElementById('statutAffiche');
            
            // Mettre à jour la classe du badge en fonction du statut
            statutBadge.className = 'badge ' + (
                statut === 'paye' ? 'bg-success' : 
                statut === 'en_retard' ? 'bg-warning' : 'bg-danger'
            );
            
            // Mettre à jour le texte du statut
            statutBadge.textContent = statutSelect.options[statutSelect.selectedIndex].text;
            
            // Si le statut est payé et que la date de paiement n'est pas définie, la définir à aujourd'hui
            if (statut === 'paye' && !datePaiement.value) {
                const today = new Date().toISOString().split('T')[0];
                datePaiement.value = today;
            } else if (statut !== 'paye' && datePaiement.value) {
                // Si le statut n'est pas payé, effacer la date de paiement
                datePaiement.value = '';
            }
        }
        
        // Charger les informations du professeur sélectionné
        function loadProfesseurInfo() {
            const professeurId = profSelect.value;
            
            if (!professeurId) {
                return;
            }
            
            // Charger les informations du professeur via AJAX
            fetch(`/admin/paiements/professeurs/eleve/${professeurId}/details`)
                .then(response => response.json())
                .then(data => {
                    // Mettre à jour les informations du professeur
                    document.getElementById('dernierPaiement').textContent = 
                        data.dernier_paiement ? new Date(data.dernier_paiement).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' }) : 'Aucun paiement';
                    
                    // Mettre à jour le taux horaire si vide
                    if (!tauxHoraire.value && data.taux_horaire > 0) {
                        tauxHoraire.value = data.taux_horaire;
                        document.getElementById('tauxHoraireAffiche').textContent = 
                            data.taux_horaire.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                    
                    // Mettre à jour les heures travaillées si vides
                    if (!heuresTravaillees.value && data.heures_mensuelles > 0) {
                        heuresTravaillees.value = data.heures_mensuelles;
                        document.getElementById('heuresMensuelles').textContent = data.heures_mensuelles;
                    }
                    
                    // Mettre à jour le montant
                    updateMontant();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des informations du professeur:', error);
                });
        }
        
        // Écouter les changements
        if (profSelect) {
            profSelect.addEventListener('change', loadProfesseurInfo);
        }
        
        if (heuresTravaillees) {
            heuresTravaillees.addEventListener('input', updateMontant);
        }
        
        if (tauxHoraire) {
            tauxHoraire.addEventListener('input', updateMontant);
        }
        
        if (statutSelect) {
            statutSelect.addEventListener('change', updateStatutAffiche);
        }
        
        // Initialiser
        if (profSelect.value) {
            loadProfesseurInfo();
        }
        
        updateStatutAffiche();
    });
</script>
@endpush
