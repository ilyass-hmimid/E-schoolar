@extends('layouts.admin')

@section('title', 'Modifier un élève')

@section('content')
<div class="bg-dark-800 rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Modifier l'élève : {{ $eleve->nom }} {{ $eleve->prenom }}</h2>
        <a href="{{ route('admin.eleves.show', $eleve) }}" class="text-gray-400 hover:text-white">
            <i class="fas fa-arrow-left mr-1"></i> Retour aux détails
        </a>
    </div>

    <form action="{{ route('admin.eleves.update', $eleve) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Informations personnelles -->
        <div class="bg-dark-700 rounded-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-user-circle mr-2"></i> Informations personnelles
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CNI -->
                <div>
                    <label for="cni" class="block text-sm font-medium text-gray-300 mb-1">CNI</label>
                    <input type="text" name="cni" id="cni" value="{{ old('cni', $eleve->cni) }}" 
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Numéro de la carte d'identité nationale">
                    @error('cni')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- CNE -->
                <div>
                    <label for="cne" class="block text-sm font-medium text-gray-300 mb-1">CNE <span class="text-red-500">*</span></label>
                    <input type="text" name="cne" id="cne" value="{{ old('cne', $eleve->cne) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Code national de l'étudiant">
                    @error('cne')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $eleve->nom) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Nom de famille">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Prénom -->
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $eleve->prenom) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Prénom">
                    @error('prenom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Date de naissance -->
                <div>
                    <label for="date_naissance" class="block text-sm font-medium text-gray-300 mb-1">Date de naissance <span class="text-red-500">*</span></label>
                    <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance', $eleve->date_naissance->format('Y-m-d')) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('date_naissance')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Lieu de naissance -->
                <div>
                    <label for="lieu_naissance" class="block text-sm font-medium text-gray-300 mb-1">Lieu de naissance <span class="text-red-500">*</span></label>
                    <input type="text" name="lieu_naissance" id="lieu_naissance" value="{{ old('lieu_naissance', $eleve->lieu_naissance) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Ville, Pays">
                    @error('lieu_naissance')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Sexe -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Sexe <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="sexe" value="Homme" class="text-primary-600 focus:ring-primary-500" {{ old('sexe', $eleve->sexe) === 'Homme' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-300">Homme</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sexe" value="Femme" class="text-primary-600 focus:ring-primary-500" {{ old('sexe', $eleve->sexe) === 'Femme' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-300">Femme</span>
                        </label>
                    </div>
                    @error('sexe')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Coordonnées -->
        <div class="bg-dark-700 rounded-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-address-card mr-2"></i> Coordonnées
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-300 mb-1">Adresse <span class="text-red-500">*</span></label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $eleve->adresse) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Adresse complète">
                    @error('adresse')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-300 mb-1">Téléphone <span class="text-red-500">*</span></label>
                    <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $eleve->telephone) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Numéro de téléphone">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $eleve->email) }}"
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Adresse email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Scolarité -->
        <div class="bg-dark-700 rounded-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-graduation-cap mr-2"></i> Scolarité
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Classe -->
                <div>
                    <label for="classe_id" class="block text-sm font-medium text-gray-300 mb-1">Classe <span class="text-red-500">*</span></label>
                    <select name="classe_id" id="classe_id" required
                            class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $niveau => $niveauClasses)
                            <optgroup label="{{ $niveau }}">
                                @foreach($niveauClasses as $classe)
                                    <option value="{{ $classe->id }}" {{ old('classe_id', $eleve->classe_id) == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->nom }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Date d'inscription -->
                <div>
                    <label for="date_inscription" class="block text-sm font-medium text-gray-300 mb-1">Date d'inscription <span class="text-red-500">*</span></label>
                    <input type="date" name="date_inscription" id="date_inscription" value="{{ old('date_inscription', $eleve->date_inscription->format('Y-m-d')) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('date_inscription')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Statut <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="actif" {{ old('status', $eleve->status) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('status', $eleve->status) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="abandonne" {{ old('status', $eleve->status) === 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                        <option value="diplome" {{ old('status', $eleve->status) === 'diplome' ? 'selected' : '' }}>Diplômé</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Informations des parents -->
        <div class="bg-dark-700 rounded-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">
                <i class="fas fa-users mr-2"></i> Informations des parents
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Père -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-gray-300 border-b border-gray-600 pb-2">Père</h4>
                    
                    <div>
                        <label for="nom_pere" class="block text-sm font-medium text-gray-300 mb-1">Nom complet <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_pere" id="nom_pere" value="{{ old('nom_pere', $eleve->nom_pere) }}" required
                               class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('nom_pere')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="profession_pere" class="block text-sm font-medium text-gray-300 mb-1">Profession</label>
                            <input type="text" name="profession_pere" id="profession_pere" value="{{ old('profession_pere', $eleve->profession_pere) }}"
                                   class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label for="telephone_pere" class="block text-sm font-medium text-gray-300 mb-1">Téléphone</label>
                            <input type="tel" name="telephone_pere" id="telephone_pere" value="{{ old('telephone_pere', $eleve->telephone_pere) }}"
                                   class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
                
                <!-- Mère -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-gray-300 border-b border-gray-600 pb-2">Mère</h4>
                    
                    <div>
                        <label for="nom_mere" class="block text-sm font-medium text-gray-300 mb-1">Nom complet <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_mere" id="nom_mere" value="{{ old('nom_mere', $eleve->nom_mere) }}" required
                               class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('nom_mere')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="profession_mere" class="block text-sm font-medium text-gray-300 mb-1">Profession</label>
                            <input type="text" name="profession_mere" id="profession_mere" value="{{ old('profession_mere', $eleve->profession_mere) }}"
                                   class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label for="telephone_mere" class="block text-sm font-medium text-gray-300 mb-1">Téléphone</label>
                            <input type="tel" name="telephone_mere" id="telephone_mere" value="{{ old('telephone_mere', $eleve->telephone_mere) }}"
                                   class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
                
                <!-- Adresse des parents -->
                <div class="md:col-span-2">
                    <label for="adresse_parents" class="block text-sm font-medium text-gray-300 mb-1">Adresse des parents <span class="text-red-500">*</span></label>
                    <input type="text" name="adresse_parents" id="adresse_parents" value="{{ old('adresse_parents', $eleve->adresse_parents) }}" required
                           class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('adresse_parents')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Remarques -->
        <div class="bg-dark-700 rounded-lg p-6">
            <label for="remarques" class="block text-sm font-medium text-gray-300 mb-2">Remarques</label>
            <textarea name="remarques" id="remarques" rows="3"
                      class="w-full bg-dark-800 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                      placeholder="Informations complémentaires...">{{ old('remarques', $eleve->remarques) }}</textarea>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-between items-center pt-4">
            <button type="button" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet élève ? Cette action est irréversible.')) { document.getElementById('delete-form').submit(); }" 
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i> Supprimer l'élève
            </button>
            
            <div class="flex space-x-4">
                <a href="{{ route('admin.eleves.show', $eleve) }}" class="px-4 py-2 border border-gray-600 rounded-md text-gray-300 hover:bg-gray-700 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Mettre à jour
                </button>
            </div>
        </div>
    </form>
    
    <!-- Formulaire de suppression -->
    <form id="delete-form" action="{{ route('admin.eleves.destroy', $eleve) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les sélecteurs avec Select2 si disponible
        if ($ && $.fn.select2) {
            $('#classe_id').select2({
                theme: 'dark',
                width: '100%',
                placeholder: 'Sélectionner une classe',
                allowClear: true
            });
        }
        
        // Formater automatiquement le numéro de téléphone
        const phoneInputs = ['telephone', 'telephone_pere', 'telephone_mere'];
        phoneInputs.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) value = value.substring(0, 10);
                    e.target.value = value;
                });
            }
        });
        
        // Mettre en majuscule les noms et prénoms
        const nameInputs = ['nom', 'prenom', 'nom_pere', 'nom_mere', 'lieu_naissance'];
        nameInputs.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase();
                });
            }
        });
    });
</script>
@endpush
@endsection
