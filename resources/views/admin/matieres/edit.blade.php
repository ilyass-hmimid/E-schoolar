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
                <div class="grid grid-cols-1 gap-6">
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

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="3" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $matiere->description) }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Une brève description de la matière (optionnel).
                        </p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Prix Mensuel -->
                        <div>
                            <label for="prix_mensuel" class="block text-sm font-medium text-gray-700">
                                Prix mensuel (DH) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="prix_mensuel" id="prix_mensuel" 
                                       value="{{ old('prix_mensuel', $matiere->prix_mensuel) }}" step="0.01" min="0"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">
                                        DH
                                    </span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Le montant mensuel facturé aux élèves.
                            </p>
                            @error('prix_mensuel')
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
                            <p class="mt-2 text-sm text-gray-500">
                                Utilisée pour identifier visuellement la matière.
                            </p>
                            @error('couleur')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="est_active" name="est_active" type="checkbox" 
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                   {{ old('est_active', $matiere->est_active) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="est_active" class="font-medium text-gray-700">Matière active</label>
                            <p class="text-gray-500">Les matières inactives ne seront pas disponibles pour les nouvelles inscriptions.</p>
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
