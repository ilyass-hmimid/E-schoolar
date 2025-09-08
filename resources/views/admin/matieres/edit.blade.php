@extends('admin.layout')

@section('title', 'Modifier la matière : ' . $matiere->nom)

@section('header')
    <h1 class="text-2xl font-bold">
        <a href="{{ route('admin.matieres.index') }}" class="text-indigo-600 hover:text-indigo-900">Matières</a>
        <span class="text-gray-400 mx-2">/</span>
        <a href="{{ route('admin.matieres.show', $matiere) }}" class="text-indigo-600 hover:text-indigo-900">{{ $matiere->nom }}</a>
        <span class="text-gray-400 mx-2">/</span>
        Modifier
    </h1>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('admin.matieres.update', $matiere) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de base -->
                    <div class="space-y-6">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700">
                                Nom de la matière <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="nom" id="nom" value="{{ old('nom', $matiere->nom) }}" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    required>
                            </div>
                            @error('nom')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Niveau d'enseignement -->
                        <div>
                            <label for="niveau" class="block text-sm font-medium text-gray-700">
                                Niveau d'enseignement <span class="text-red-500">*</span>
                            </label>
                            <select id="niveau" name="niveau" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Sélectionnez un niveau</option>
                                <optgroup label="Primaire">
                                    <option value="primaire_3" {{ (old('niveau', $matiere->niveau) == 'primaire_3') ? 'selected' : '' }}>3ème année primaire</option>
                                    <option value="primaire_4" {{ (old('niveau', $matiere->niveau) == 'primaire_4') ? 'selected' : '' }}>4ème année primaire</option>
                                    <option value="primaire_5" {{ (old('niveau', $matiere->niveau) == 'primaire_5') ? 'selected' : '' }}>5ème année primaire</option>
                                    <option value="primaire_6" {{ (old('niveau', $matiere->niveau) == 'primaire_6') ? 'selected' : '' }}>6ème année primaire</option>
                                </optgroup>
                                <optgroup label="Collège">
                                    <option value="college_1" {{ (old('niveau', $matiere->niveau) == 'college_1') ? 'selected' : '' }}>1ère année collège</option>
                                    <option value="college_2" {{ (old('niveau', $matiere->niveau) == 'college_2') ? 'selected' : '' }}>2ème année collège</option>
                                    <option value="college_3" {{ (old('niveau', $matiere->niveau) == 'college_3') ? 'selected' : '' }}>3ème année collège</option>
                                </optgroup>
                                <optgroup label="Lycée">
                                    <option value="tronc_commun" {{ (old('niveau', $matiere->niveau) == 'tronc_commun') ? 'selected' : '' }}>Tronc commun</option>
                                    <option value="1bac_sm" {{ (old('niveau', $matiere->niveau) == '1bac_sm') ? 'selected' : '' }}>1ère année bac - Sciences Maths</option>
                                    <option value="1bac_sv" {{ (old('niveau', $matiere->niveau) == '1bac_sv') ? 'selected' : '' }}>1ère année bac - Sciences Physiques</option>
                                    <option value="2bac_sm" {{ (old('niveau', $matiere->niveau) == '2bac_sm') ? 'selected' : '' }}>2ème année bac - Sciences Maths</option>
                                    <option value="2bac_sv" {{ (old('niveau', $matiere->niveau) == '2bac_sv') ? 'selected' : '' }}>2ème année bac - Sciences Physiques</option>
                                </optgroup>
                            </select>
                            @error('niveau')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type de matière -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type de matière</label>
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center">
                                    <input id="est_fixe" name="est_fixe" type="checkbox" value="1" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           {{ old('est_fixe', $matiere->est_fixe) ? 'checked' : '' }}>
                                    <label for="est_fixe" class="ml-2 block text-sm text-gray-700">
                                        Matière fixe (obligatoire pour ce niveau)
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">Les matières fixes sont prédéfinies et ne peuvent pas être supprimées.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Prix et apparence -->
                    <div class="space-y-6">
                        <!-- Prix mensuel -->
                        <div>
                            <label for="prix_mensuel" class="block text-sm font-medium text-gray-700">
                                Prix mensuel (MAD) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">MAD</span>
                                </div>
                                <input type="number" name="prix_mensuel" id="prix_mensuel" 
                                       value="{{ old('prix_mensuel', $matiere->prix_mensuel) }}" step="0.01" min="0"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md"
                                       placeholder="0.00" required>
                            </div>
                            @error('prix_mensuel')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prix trimestriel -->
                        <div>
                            <label for="prix_trimestriel" class="block text-sm font-medium text-gray-700">
                                Prix trimestriel (MAD) <span class="text-xs text-gray-500">(optionnel)</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">MAD</span>
                                </div>
                                <input type="number" name="prix_trimestriel" id="prix_trimestriel" 
                                       value="{{ old('prix_trimestriel', $matiere->prix_trimestriel) }}" step="0.01" min="0"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md"
                                       placeholder="0.00">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Si renseigné, un rabais sera appliqué par rapport au prix mensuel.</p>
                            @error('prix_trimestriel')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icône -->
                        <div>
                            <label for="icone" class="block text-sm font-medium text-gray-700">
                                Icône <span class="text-xs text-gray-500">(optionnel)</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-icons text-gray-400"></i>
                                </div>
                                <input type="text" name="icone" id="icone" 
                                       value="{{ old('icone', $matiere->icone) }}" 
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="fas fa-book">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Utilisez une icône de <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-600 hover:text-indigo-800">Font Awesome</a> (ex: fas fa-book, fas fa-flask).</p>
                            @error('icone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Couleur -->
                        <div>
                            <label for="couleur" class="block text-sm font-medium text-gray-700">
                                Couleur d'identification <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex items-center">
                                <input type="color" name="couleur" id="couleur" value="{{ old('couleur', $matiere->couleur) }}"
                                       class="h-10 w-16 p-1 border border-gray-300 rounded-md">
                                <input type="text" id="couleur_hex" value="{{ old('couleur', $matiere->couleur) }}"
                                       class="ml-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       readonly>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Cette couleur sera utilisée pour identifier visuellement la matière.
                            </p>
                            @error('couleur')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description détaillée
                        </label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="3" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $matiere->description) }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Décrivez en détail les objectifs et le contenu de la matière.
                        </p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="md:col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="est_active" name="est_active" type="checkbox" 
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('est_active', $matiere->est_active) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="est_active" class="font-medium text-gray-700">Matière active</label>
                                <p class="text-gray-500">Les matières inactives ne seront pas disponibles pour les inscriptions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('admin.matieres.show', $matiere) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Mettre à jour la matière
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Synchronisation entre le sélecteur de couleur et le champ texte
    document.addEventListener('DOMContentLoaded', function() {
        const colorPicker = document.getElementById('couleur');
        const colorHex = document.getElementById('couleur_hex');
        
        // Mettre à jour la valeur hexadécimale lorsque la couleur change
        colorPicker.addEventListener('input', function() {
            colorHex.value = this.value.toUpperCase();
        });
        
        // Initialiser la valeur hexadécimale
        colorHex.value = colorPicker.value.toUpperCase();
    });
</script>
@endpush
