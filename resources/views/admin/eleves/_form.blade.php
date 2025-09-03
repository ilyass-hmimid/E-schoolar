@php
    $isEdit = isset($eleve) && $eleve->exists;
    $route = $isEdit ? route('admin.eleves.update', $eleve) : route('admin.eleves.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $buttonText = $isEdit ? 'Mettre à jour' : 'Enregistrer';
    $buttonIcon = $isEdit ? 'save' : 'plus';
@endphp

<x-form.form :action="$route" :method="$method" has-files class="space-y-6">
    @if($isEdit) @method('PUT') @endif
    
    <div class="grid grid-cols-1 gap-6">
        <!-- Personal Information Section -->
        <div class="bg-dark-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-user-circle mr-2"></i> Informations personnelles
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CNE -->
                <x-form.input 
                    name="cne" 
                    label="CNE" 
                    :value="$eleve->cne ?? old('cne')" 
                    required 
                    maxlength="20"
                    pattern="[A-Za-z0-9]+"
                    title="Le CNE ne doit contenir que des lettres et des chiffres"
                />
                
                <!-- CNI -->
                <x-form.input 
                    name="cni" 
                    label="CNI" 
                    :value="$eleve->cni ?? old('cni')" 
                    maxlength="20"
                />
                
                <!-- Nom -->
                <x-form.input 
                    name="nom" 
                    label="Nom" 
                    :value="$eleve->nom ?? old('nom')" 
                    required 
                    maxlength="50"
                />
                
                <!-- Prénom -->
                <x-form.input 
                    name="prenom" 
                    label="Prénom" 
                    :value="$eleve->prenom ?? old('prenom')" 
                    required 
                    maxlength="50"
                />
                
                <!-- Date de naissance -->
                <x-form.date 
                    name="date_naissance" 
                    label="Date de naissance" 
                    :value="isset($eleve->date_naissance) ? $eleve->date_naissance->format('Y-m-d') : old('date_naissance')" 
                    required 
                    max="today"
                    placeholder="JJ/MM/YYYY"
                />
                
                <!-- Lieu de naissance -->
                <x-form.input 
                    name="lieu_naissance" 
                    label="Lieu de naissance" 
                    :value="$eleve->lieu_naissance ?? old('lieu_naissance')" 
                    required 
                    maxlength="100"
                />
                
                <!-- Sexe -->
                <x-form.radio-group 
                    name="sexe" 
                    label="Sexe" 
                    :options="['M' => 'Masculin', 'F' => 'Féminin']" 
                    :selected="$eleve->sexe ?? old('sexe', 'M')" 
                    required
                    class="md:col-span-2"
                />
                
                <!-- Photo -->
                <x-form.file-upload 
                    name="photo" 
                    label="Photo" 
                    :value="$eleve->photo ?? old('photo')" 
                    accept="image/*"
                    help="Formats acceptés: JPG, PNG, GIF (max 2MB)"
                    class="md:col-span-2"
                />
            </div>
        </div>
        
        <!-- Contact Information Section -->
        <div class="bg-dark-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-address-card mr-2"></i> Coordonnées
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Adresse -->
                <x-form.textarea 
                    name="adresse" 
                    label="Adresse" 
                    rows="2"
                    required
                    class="md:col-span-2"
                >{{ $eleve->adresse ?? old('adresse') }}</x-form.textarea>
                
                <!-- Ville -->
                <x-form.input 
                    name="ville" 
                    label="Ville" 
                    :value="$eleve->ville ?? old('ville')" 
                    required 
                    maxlength="50"
                />
                
                <!-- Code postal -->
                <x-form.input 
                    name="code_postal" 
                    label="Code postal" 
                    :value="$eleve->code_postal ?? old('code_postal')" 
                    maxlength="10"
                    pattern="[0-9]+"
                    title="Le code postal ne doit contenir que des chiffres"
                />
                
                <!-- Téléphone -->
                <x-form.input 
                    name="telephone" 
                    label="Téléphone" 
                    type="tel" 
                    :value="$eleve->telephone ?? old('telephone')" 
                    required 
                    pattern="[0-9]{10}" 
                    data-pattern-message="Veuillez entrer un numéro de téléphone valide (10 chiffres)" 
                />
                
                <!-- Email -->
                <x-form.input 
                    name="email" 
                    label="Email" 
                    type="email" 
                    :value="$eleve->email ?? old('email')" 
                    maxlength="100"
                />
            </div>
        </div>
        
        <!-- Parents Information Section -->
        <div class="bg-dark-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-users mr-2"></i> Informations des parents
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Père -->
                <div class="space-y-4 bg-dark-700 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-300 border-b border-gray-600 pb-2">Père</h4>
                    
                    <x-form.input 
                        name="nom_pere" 
                        label="Nom complet" 
                        :value="$eleve->nom_pere ?? old('nom_pere')" 
                        maxlength="100"
                    />
                    
                    <x-form.input 
                        name="profession_pere" 
                        label="Profession" 
                        :value="$eleve->profession_pere ?? old('profession_pere')" 
                        maxlength="50"
                    />
                    
                    <x-form.input 
                        name="telephone_pere" 
                        label="Téléphone" 
                        type="tel" 
                        :value="$eleve->telephone_pere ?? old('telephone_pere')" 
                        pattern="[0-9]{10}" 
                        data-pattern-message="Veuillez entrer un numéro de téléphone valide (10 chiffres)" 
                    />
                </div>
                
                <!-- Mère -->
                <div class="space-y-4 bg-dark-700 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-300 border-b border-gray-600 pb-2">Mère</h4>
                    
                    <x-form.input 
                        name="nom_mere" 
                        label="Nom complet" 
                        :value="$eleve->nom_mere ?? old('nom_mere')" 
                        maxlength="100"
                    />
                    
                    <x-form.input 
                        name="profession_mere" 
                        label="Profession" 
                        :value="$eleve->profession_mere ?? old('profession_mere')" 
                        maxlength="50"
                    />
                    
                    <x-form.input 
                        name="telephone_mere" 
                        label="Téléphone" 
                        type="tel" 
                        :value="$eleve->telephone_mere ?? old('telephone_mere')" 
                        pattern="[0-9]{10}" 
                        data-pattern-message="Veuillez entrer un numéro de téléphone valide (10 chiffres)" 
                    />
                </div>
                
                <!-- Adresse des parents -->
                <div class="md:col-span-2">
                    <x-form.textarea 
                        name="adresse_parents" 
                        label="Adresse des parents" 
                        rows="2"
                    >{{ $eleve->adresse_parents ?? old('adresse_parents') }}</x-form.textarea>
                </div>
            </div>
        </div>
        
        <!-- Academic Information Section -->
        <div class="bg-dark-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-graduation-cap mr-2"></i> Scolarité
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Classe -->
                <x-form.select 
                    name="classe_id" 
                    label="Classe" 
                    :options="$classes" 
                    :selected="$eleve->classe_id ?? old('classe_id')" 
                    option-value="id"
                    option-label="nom_complet"
                    required
                    data-required-message="La classe est obligatoire"
                />
                
                <!-- Date d'inscription -->
                <x-form.date 
                    name="date_inscription" 
                    label="Date d'inscription" 
                    :value="isset($eleve->date_inscription) ? $eleve->date_inscription->format('Y-m-d') : (old('date_inscription') ?? now()->format('Y-m-d'))" 
                    required 
                    min="{{ now()->subYear()->startOfYear()->format('Y-m-d') }}"
                    max="{{ now()->addMonth()->format('Y-m-d') }}"
                />
                
                <!-- Statut -->
                <x-form.select 
                    name="status" 
                    label="Statut" 
                    :options="[
                        'actif' => 'Actif', 
                        'inactif' => 'Inactif', 
                        'abandonne' => 'Abandonné', 
                        'diplome' => 'Diplômé',
                        'transfere' => 'Transféré',
                        'exclu' => 'Exclu'
                    ]" 
                    :selected="$eleve->status ?? old('status', 'actif')" 
                    required 
                />
                
                <!-- Frais d'inscription -->
                <x-form.input 
                    name="frais_inscription" 
                    label="Frais d'inscription (DH)" 
                    type="number" 
                    :value="$eleve->frais_inscription ?? old('frais_inscription', 0)" 
                    min="0" 
                    step="0.01"
                />
            </div>
            
            <!-- Remarques -->
            <div class="mt-6">
                <x-form.textarea 
                    name="remarques" 
                    label="Remarques" 
                    rows="3"
                >{{ $eleve->remarques ?? old('remarques') }}</x-form.textarea>
            </div>
        </div>
    </div>
    
    <!-- Form Actions -->
    <div class="flex justify-between items-center pt-4">
        @if($isEdit)
            <button 
                type="button" 
                onclick="confirmDelete('{{ route('admin.eleves.destroy', $eleve) }}', 'Voulez-vous vraiment supprimer cet élève ? Cette action est irréversible.')" 
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
            >
                <i class="fas fa-trash mr-1"></i> Supprimer
            </button>
        @else
            <a 
                href="{{ route('admin.eleves.index') }}" 
                class="px-4 py-2 border border-gray-600 rounded-md text-gray-300 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200"
            >
                <i class="fas fa-times mr-1"></i> Annuler
            </a>
        @endif
        
        <div class="flex space-x-3">
            <a 
                href="{{ $isEdit ? route('admin.eleves.show', $eleve) : route('admin.eleves.index') }}" 
                class="px-4 py-2 border border-gray-600 rounded-md text-gray-300 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200"
            >
                <i class="fas {{ $isEdit ? 'fa-eye' : 'fa-list' }} mr-1"></i> {{ $isEdit ? 'Voir' : 'Liste' }}
            </a>
            
            <button 
                type="submit" 
                class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200"
            >
                <i class="fas fa-{{ $buttonIcon }} mr-1"></i> {{ $buttonText }}
            </button>
        </div>
    </div>
</x-form.form>

<!-- Delete Confirmation Modal -->
@include('components.modals.confirmation', [
    'id' => 'deleteConfirmationModal',
    'title' => 'Confirmer la suppression',
    'message' => 'Êtes-vous sûr de vouloir supprimer cet élève ? Cette action est irréversible.',
    'confirmText' => 'Supprimer',
    'confirmColor' => 'red',
    'formId' => 'deleteForm',
    'formAction' => '',
    'formMethod' => 'DELETE'
])

@push('scripts')
<script>
    // Initialize date pickers
    document.addEventListener('DOMContentLoaded', function() {
        // Handle class change to update related fields
        const classSelect = document.querySelector('select[name="classe_id"]');
        const registrationFeeInput = document.querySelector('input[name="frais_inscription"]');
        
        if (classSelect && registrationFeeInput) {
            classSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const fraisInscription = selectedOption.getAttribute('data-frais-inscription');
                
                if (fraisInscription && !registrationFeeInput.value) {
                    registrationFeeInput.value = parseFloat(fraisInscription).toFixed(2);
                }
            });
        }
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tooltip]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Confirm before delete
    function confirmDelete(url, message) {
        const modal = document.getElementById('deleteConfirmationModal');
        const form = document.getElementById('deleteForm');
        
        if (form) {
            form.action = url;
        }
        
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
</script>
@endpush
