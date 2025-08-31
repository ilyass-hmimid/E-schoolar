@extends('layouts.admin')

@section('title', 'Créer un salaire')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Créer un nouveau salaire</h1>
        <p class="text-gray-600">Remplissez le formulaire pour ajouter un nouveau salaire</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.salaires.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Professeur -->
                <div>
                    <label for="professeur_id" class="block text-sm font-medium text-gray-700 mb-1">Professeur *</label>
                    <select name="professeur_id" id="professeur_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionnez un professeur</option>
                        @foreach($professeurs as $professeur)
                            <option value="{{ $professeur->id }}" {{ old('professeur_id') == $professeur->id ? 'selected' : '' }}>
                                {{ $professeur->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('professeur_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Matière -->
                <div>
                    <label for="matiere_id" class="block text-sm font-medium text-gray-700 mb-1">Matière (optionnel)</label>
                    <select name="matiere_id" id="matiere_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toutes les matières</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('matiere_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mois de période -->
                <div>
                    <label for="mois_periode" class="block text-sm font-medium text-gray-700 mb-1">Mois de paie *</label>
                    <input type="month" name="mois_periode" id="mois_periode" required
                        value="{{ old('mois_periode', now()->format('Y-m')) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('mois_periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Heures travaillées -->
                <div>
                    <label for="heures_travaillees" class="block text-sm font-medium text-gray-700 mb-1">Heures travaillées *</label>
                    <input type="number" name="heures_travaillees" id="heures_travaillees" required
                        value="{{ old('heures_travaillees') }}" step="0.5" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('heures_travaillees')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Taux horaire -->
                <div>
                    <label for="taux_horaire" class="block text-sm font-medium text-gray-700 mb-1">Taux horaire (€) *</label>
                    <input type="number" name="taux_horaire" id="taux_horaire" required
                        value="{{ old('taux_horaire') }}" step="0.01" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('taux_horaire')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Montant total -->
                <div>
                    <label for="montant_total" class="block text-sm font-medium text-gray-700 mb-1">Montant total (€) *</label>
                    <input type="number" name="montant_total" id="montant_total" required
                        value="{{ old('montant_total') }}" step="0.01" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('montant_total')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="statut" id="statut" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="en_attente" {{ old('statut', 'en_attente') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="paye" {{ old('statut') == 'paye' ? 'selected' : '' }}>Payé</option>
                        <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optionnel)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.salaires.index') }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </a>
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Calcul automatique du montant total
    document.addEventListener('DOMContentLoaded', function() {
        const heuresInput = document.getElementById('heures_travaillees');
        const tauxHoraireInput = document.getElementById('taux_horaire');
        const montantTotalInput = document.getElementById('montant_total');
        
        function calculerMontantTotal() {
            const heures = parseFloat(heuresInput.value) || 0;
            const tauxHoraire = parseFloat(tauxHoraireInput.value) || 0;
            const montantTotal = heures * tauxHoraire;
            
            if (!isNaN(montantTotal)) {
                montantTotalInput.value = montantTotal.toFixed(2);
            }
        }
        
        heuresInput.addEventListener('input', calculerMontantTotal);
        tauxHoraireInput.addEventListener('input', calculerMontantTotal);
    });
</script>
@endpush
@endsection
