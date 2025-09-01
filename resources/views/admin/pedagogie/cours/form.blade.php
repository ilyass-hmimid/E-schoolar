@props([
    'method' => 'POST',
    'action' => '#',
    'cour' => null,
    'niveaux' => [],
    'matieres' => [],
    'enseignants' => []
])

@php
    $isEdit = $cour !== null;
    $title = $isEdit ? 'Modifier le cours' : 'Créer un nouveau cours';
    $buttonText = $isEdit ? 'Mettre à jour' : 'Créer le cours';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    
    <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Intitulé du cours -->
            <div class="md:col-span-2">
                <label for="intitule" class="block text-sm font-medium text-gray-300">
                    Intitulé du cours <span class="text-red-500">*</span>
                </label>
                <input type="text" id="intitule" name="intitule" 
                       class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                       value="{{ old('intitule', $cour->intitule ?? '') }}" 
                       required>
                @error('intitule')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Code du cours -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-300">
                    Code du cours <span class="text-red-500">*</span>
                </label>
                <input type="text" id="code" name="code" 
                       class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                       value="{{ old('code', $cour->code ?? '') }}" 
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
                        <option value="{{ $niveau->id }}" {{ old('niveau_id', $cour->niveau_id ?? '') == $niveau->id ? 'selected' : '' }}>
                            {{ $niveau->nom }}
                        </option>
                    @endforeach
                </select>
                @error('niveau_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Matière -->
            <div>
                <label for="matiere_id" class="block text-sm font-medium text-gray-300">
                    Matière <span class="text-red-500">*</span>
                </label>
                <select id="matiere_id" name="matiere_id" 
                        class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                        required>
                    <option value="">Sélectionnez une matière</option>
                    @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}" {{ old('matiere_id', $cour->matiere_id ?? '') == $matiere->id ? 'selected' : '' }}>
                            {{ $matiere->nom }}
                        </option>
                    @endforeach
                </select>
                @error('matiere_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Enseignant -->
            <div>
                <label for="enseignant_id" class="block text-sm font-medium text-gray-300">
                    Enseignant <span class="text-red-500">*</span>
                </label>
                <select id="enseignant_id" name="enseignant_id" 
                        class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                        required>
                    <option value="">Sélectionnez un enseignant</option>
                    @foreach($enseignants as $enseignant)
                        <option value="{{ $enseignant->id }}" {{ old('enseignant_id', $cour->enseignant_id ?? '') == $enseignant->id ? 'selected' : '' }}>
                            {{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}
                            @if($enseignant->specialite)
                                ({{ $enseignant->specialite }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('enseignant_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Volume horaire -->
            <div>
                <label for="volume_horaire" class="block text-sm font-medium text-gray-300">
                    Volume horaire (heures) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="volume_horaire" name="volume_horaire" min="1" step="1"
                       class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                       value="{{ old('volume_horaire', $cour->volume_horaire ?? '') }}" 
                       required>
                @error('volume_horaire')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Crédits -->
            <div>
                <label for="credits" class="block text-sm font-medium text-gray-300">
                    Crédits
                </label>
                <input type="number" id="credits" name="credits" min="0" step="0.5"
                       class="mt-1 block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white"
                       value="{{ old('credits', $cour->credits ?? '') }}">
                @error('credits')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Statut -->
            <div>
                <label for="est_actif" class="block text-sm font-medium text-gray-300">
                    Statut
                </label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="hidden" name="est_actif" value="0">
                        <input type="checkbox" name="est_actif" value="1" 
                               class="rounded border-dark-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                               {{ old('est_actif', $cour->est_actif ?? true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-300">Actif</span>
                    </label>
                </div>
                @error('est_actif')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Visibilité -->
            <div>
                <label for="est_public" class="block text-sm font-medium text-gray-300">
                    Visibilité
                </label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="hidden" name="est_public" value="0">
                        <input type="checkbox" name="est_public" value="1" 
                               class="rounded border-dark-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                               {{ old('est_public', $cour->est_public ?? false) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-300">Rendre ce cours public</span>
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-400">Les cours publics sont visibles par tous les utilisateurs</p>
                @error('est_public')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-300">
                    Description
                </label>
                <div class="mt-1">
                    <textarea id="description" name="description" rows="4"
                              class="block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white">{{ old('description', $cour->description ?? '') }}</textarea>
                </div>
                <p class="mt-1 text-xs text-gray-400">Décrivez brièvement le contenu et les objectifs du cours</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Objectifs pédagogiques -->
            <div class="md:col-span-2">
                <label for="objectifs" class="block text-sm font-medium text-gray-300">
                    Objectifs pédagogiques
                </label>
                <div class="mt-1">
                    <textarea id="objectifs" name="objectifs" rows="3"
                              class="block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white">{{ old('objectifs', $cour->objectifs ?? '') }}</textarea>
                </div>
                <p class="mt-1 text-xs text-gray-400">Listez les objectifs d'apprentissage principaux (un par ligne)</p>
                @error('objectifs')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Prérequis -->
            <div class="md:col-span-2">
                <label for="prerequis" class="block text-sm font-medium text-gray-300">
                    Prérequis
                </label>
                <div class="mt-1">
                    <textarea id="prerequis" name="prerequis" rows="2"
                              class="block w-full bg-dark-700 border border-dark-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-white">{{ old('prerequis', $cour->prerequis ?? '') }}</textarea>
                </div>
                <p class="mt-1 text-xs text-gray-400">Connaissances ou compétences requises pour suivre ce cours</p>
                @error('prerequis')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-dark-700">
            <a href="{{ $isEdit ? route('admin.cours.show', $cour->id) : route('admin.cours.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-dark-700 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
        // Initialisation de l'éditeur de texte riche pour la description
        if (document.getElementById('description')) {
            // Initialiser un éditeur de texte riche ici (ex: TinyMCE, CKEditor, etc.)
            // Exemple avec TinyMCE:
            // tinymce.init({
            //     selector: '#description',
            //     plugins: 'link lists table code',
            //     toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link table',
            //     skin: 'oxide-dark',
            //     content_css: 'dark',
            //     height: 300
            // });
        }
        
        // Mise à jour dynamique des matières en fonction du niveau sélectionné
        const niveauSelect = document.getElementById('niveau_id');
        const matiereSelect = document.getElementById('matiere_id');
        
        if (niveauSelect && matiereSelect) {
            niveauSelect.addEventListener('change', function() {
                const niveauId = this.value;
                
                // Réinitialiser les options de la matière
                matiereSelect.innerHTML = '<option value="">Sélectionnez une matière</option>';
                
                if (!niveauId) return;
                
                // Charger les matières correspondant au niveau sélectionné
                fetch(`/api/niveaux/${niveauId}/matieres`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(matiere => {
                            const option = document.createElement('option');
                            option.value = matiere.id;
                            option.textContent = matiere.nom;
                            matiereSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erreur lors du chargement des matières:', error));
            });
        }
    });
</script>
@endpush
