@props([
    'method' => 'POST',
    'action' => '#',
    'classe' => null,
    'niveaux' => [],
    'professeurs' => [],
    'salles' => []
])

@php
    $isEdit = $classe !== null;
    $title = $isEdit ? 'Modifier la classe' : 'Créer une nouvelle classe';
    $buttonText = $isEdit ? 'Mettre à jour' : 'Créer la classe';
    
    // Valeurs par défaut pour une nouvelle classe
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $defaultAnneeScolaire = "$currentYear-$nextYear";
@endphp

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    
    <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nom de la classe -->
            <div class="md:col-span-2">
                <label for="nom" class="block text-sm font-medium text-gray-300">
                    Nom de la classe <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nom" name="nom" 
                       class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                       value="{{ old('nom', $classe->nom ?? '') }}" 
                       required>
                @error('nom')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Code de la classe -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-300">
                    Code <span class="text-red-500">*</span>
                </label>
                <input type="text" id="code" name="code" 
                       class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                       value="{{ old('code', $classe->code ?? '') }}" 
                       required>
                @error('code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Niveau -->
            <div>
                <label for="niveau_id" class="block text-sm font-medium text-gray-300">
                    Niveau <span class="text-red-500">*</span>
                </label>
                <select id="niveau_id" name="niveau_id" 
                        class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                        required>
                    <option value="">Sélectionnez un niveau</option>
                    @foreach($niveaux as $niveau)
                        <option value="{{ $niveau->id }}" {{ old('niveau_id', $classe->niveau_id ?? '') == $niveau->id ? 'selected' : '' }}>
                            {{ $niveau->nom }}
                        </option>
                    @endforeach
                </select>
                @error('niveau_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Professeur principal -->
            <div>
                <label for="professeur_principal_id" class="block text-sm font-medium text-gray-300">
                    Professeur principal
                </label>
                <select id="professeur_principal_id" name="professeur_principal_id" 
                        class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white">
                    <option value="">Sélectionnez un professeur</option>
                    @foreach($professeurs as $professeur)
                        <option value="{{ $professeur->id }}" {{ old('professeur_principal_id', $classe->professeur_principal_id ?? '') == $professeur->id ? 'selected' : '' }}>
                            {{ $professeur->user->prenom }} {{ $professeur->user->nom }}
                            @if($professeur->matiere)
                                ({{ $professeur->matiere->nom }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('professeur_principal_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Salle -->
            <div>
                <label for="salle" class="block text-sm font-medium text-gray-300">
                    Salle
                </label>
                <select id="salle" name="salle" 
                        class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white">
                    <option value="">Sélectionnez une salle</option>
                    @foreach($salles as $salle)
                        <option value="{{ $salle }}" {{ old('salle', $classe->salle ?? '') == $salle ? 'selected' : '' }}>
                            {{ $salle }}
                        </option>
                    @endforeach
                </select>
                @error('salle')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Année scolaire -->
            <div>
                <label for="annee_scolaire" class="block text-sm font-medium text-gray-300">
                    Année scolaire <span class="text-red-500">*</span>
                </label>
                <select id="annee_scolaire" name="annee_scolaire" 
                        class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                        required>
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        @php
                            $anneeScolaire = "$i-" . ($i + 1);
                            $selected = old('annee_scolaire', $classe->annee_scolaire ?? $defaultAnneeScolaire) == $anneeScolaire ? 'selected' : '';
                        @endphp
                        <option value="{{ $anneeScolaire }}" {{ $selected }}>
                            {{ $anneeScolaire }}
                        </option>
                    @endfor
                </select>
                @error('annee_scolaire')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Statut -->
            <div>
                <label for="est_active" class="block text-sm font-medium text-gray-300">
                    Statut
                </label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="hidden" name="est_active" value="0">
                        <input type="checkbox" name="est_active" value="1" 
                               class="rounded border-dark-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                               {{ old('est_active', $classe->est_active ?? true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-300">Classe active</span>
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-400">Une classe inactive n'apparaîtra pas dans certaines listes</p>
                @error('est_active')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Capacité maximale -->
            <div>
                <label for="capacite_max" class="block text-sm font-medium text-gray-300">
                    Capacité maximale
                </label>
                <input type="number" id="capacite_max" name="capacite_max" min="1" 
                       class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                       value="{{ old('capacite_max', $classe->capacite_max ?? '30') }}">
                @error('capacite_max')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-300">
                    Description
                </label>
                <div class="mt-1">
                    <textarea id="description" name="description" rows="3"
                              class="block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white">{{ old('description', $classe->description ?? '') }}</textarea>
                </div>
                <p class="mt-1 text-xs text-gray-400">Description optionnelle de la classe</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Options supplémentaires -->
            <div class="md:col-span-2 pt-4 mt-4 border-t border-dark-700">
                <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">Options supplémentaires</h3>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="generer_emploi_du_temps" name="generer_emploi_du_temps" type="checkbox" 
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-dark-300 rounded"
                                   {{ $isEdit ? '' : 'checked' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="generer_emploi_du_temps" class="font-medium text-gray-300">Générer un emploi du temps par défaut</label>
                            <p class="text-gray-400">Crée automatiquement un emploi du temps vide pour cette classe</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="notifier_professeurs" name="notifier_professeurs" type="checkbox" 
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-dark-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notifier_professeurs" class="font-medium text-gray-300">Notifier les professeurs</label>
                            <p class="text-gray-400">Envoie une notification aux professeurs affectés à cette classe</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-dark-700">
            <a href="{{ $isEdit ? route('admin.classes.show', $classe->id) : route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-dark-700 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-times mr-2"></i> Annuler
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas {{ $isEdit ? 'fa-save' : 'fa-plus' }} mr-2"></i> {{ $buttonText }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Génération automatique du code de la classe
        const nomInput = document.getElementById('nom');
        const codeInput = document.getElementById('code');
        const niveauSelect = document.getElementById('niveau_id');
        
        // Ne générer le code que pour une nouvelle classe
        @if(!$isEdit)
            function generateCode() {
                if (niveauSelect.value && nomInput.value) {
                    const niveauText = niveauSelect.options[niveauSelect.selectedIndex].text;
                    const niveauCode = niveauText.match(/\b(\w)/g).join('').toUpperCase();
                    const nomCode = nomInput.value.replace(/[^A-Z0-9]/gi, '').toUpperCase().substring(0, 3);
                    codeInput.value = `${niveauCode}${nomCode}`;
                }
            }
            
            nomInput.addEventListener('blur', generateCode);
            niveauSelect.addEventListener('change', generateCode);
        @endif
        
        // Initialisation des sélecteurs améliorés (si nécessaire)
        // Par exemple, utiliser Select2 ou un autre plugin pour une meilleure expérience utilisateur
    });
</script>
@endpush
