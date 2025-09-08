@extends('admin.layout')

@section('title', 'Modifier le Paiement #' . $paiement->id)

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Modifier le paiement #{{ $paiement->reference ?? $paiement->id }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Effectué le {{ $paiement->date_paiement->format('d/m/Y') }} par {{ $paiement->etudiant->nom_complet }}
            </p>
        </div>

        <form action="{{ route('admin.paiements.update', $paiement) }}" method="POST" class="px-4 py-5 sm:p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <!-- Informations de base -->
                <div class="sm:col-span-3">
                    <label for="etudiant_id" class="block text-sm font-medium text-gray-700">Élève *</label>
                    <div class="mt-1">
                        <select id="etudiant_id" name="etudiant_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach($etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}" {{ $paiement->etudiant_id == $etudiant->id ? 'selected' : '' }}>
                                    {{ $etudiant->nom_complet }} ({{ $etudiant->matricule ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('etudiant_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="type_paiement" class="block text-sm font-medium text-gray-700">Type de paiement *</label>
                    <div class="mt-1">
                        <select id="type_paiement" name="type_paiement" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="mensualite" {{ $paiement->type_paiement == 'mensualite' ? 'selected' : '' }}>Mensualité</option>
                            <option value="inscription" {{ $paiement->type_paiement == 'inscription' ? 'selected' : '' }}>Frais d'inscription</option>
                            <option value="autre" {{ $paiement->type_paiement == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    @error('type_paiement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Montant et dates -->
                <div class="sm:col-span-2">
                    <label for="montant" class="block text-sm font-medium text-gray-700">Montant (MAD) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">MAD</span>
                        </div>
                        <input type="number" step="0.01" name="montant" id="montant" required
                            value="{{ old('montant', $paiement->montant) }}"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-16 sm:pl-14 sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('montant')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="date_paiement" class="block text-sm font-medium text-gray-700">Date de paiement *</label>
                    <div class="mt-1">
                        <input type="date" name="date_paiement" id="date_paiement" required
                            value="{{ old('date_paiement', $paiement->date_paiement->format('Y-m-d')) }}"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('date_paiement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="mode_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement *</label>
                    <div class="mt-1">
                        <select id="mode_paiement" name="mode_paiement" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="especes" {{ $paiement->mode_paiement == 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="cheque" {{ $paiement->mode_paiement == 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="virement" {{ $paiement->mode_paiement == 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="carte" {{ $paiement->mode_paiement == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                        </select>
                    </div>
                    @error('mode_paiement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Référence et statut -->
                <div class="sm:col-span-3">
                    <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
                    <div class="mt-1">
                        <input type="text" name="reference" id="reference"
                            value="{{ old('reference', $paiement->reference) }}" 
                            placeholder="N° de chèque, référence virement, etc."
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('reference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="statut" class="block text-sm font-medium text-gray-700">Statut *</label>
                    <div class="mt-1">
                        <select id="statut" name="statut" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="complet" {{ $paiement->statut == 'complet' ? 'selected' : '' }}>Complet</option>
                            <option value="partiel" {{ $paiement->statut == 'partiel' ? 'selected' : '' }}>Partiel</option>
                            <option value="en_attente" {{ $paiement->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="annule" {{ $paiement->statut == 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Période et frais supplémentaires -->
                <div class="sm:col-span-3">
                    <label for="periode" class="block text-sm font-medium text-gray-700">Période concernée</label>
                    <div class="mt-1">
                        <input type="month" name="periode" id="periode"
                            value="{{ old('periode', $paiement->periode ? \Carbon\Carbon::parse($paiement->periode)->format('Y-m') : '') }}"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Pour les mensualités, indiquez le mois concerné</p>
                    @error('periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="frais_supplementaires" class="block text-sm font-medium text-gray-700">Frais supplémentaires (MAD)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">+</span>
                        </div>
                        <input type="number" step="0.01" name="frais_supplementaires" id="frais_supplementaires"
                            value="{{ old('frais_supplementaires', $paiement->frais_supplementaires) }}" 
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('frais_supplementaires')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="remise" class="block text-sm font-medium text-gray-700">Remise (MAD)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">-</span>
                        </div>
                        <input type="number" step="0.01" name="remise" id="remise"
                            value="{{ old('remise', $paiement->remise) }}" 
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('remise')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="montant_total" class="block text-sm font-medium text-gray-700">Montant total (MAD)</label>
                    <div class="mt-1">
                        <input type="text" id="montant_total" readonly
                            value="{{ number_format($paiement->montant_total, 2, ',', ' ') }}"
                            class="bg-gray-100 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>

                <!-- Notes -->
                <div class="sm:col-span-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <div class="mt-1">
                        <textarea id="notes" name="notes" rows="3"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('notes', $paiement->notes) }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Informations complémentaires sur ce paiement</p>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pièces jointes -->
                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Pièces jointes</label>
                    <div class="mt-1 flex items-center">
                        <input type="file" name="pieces_jointes[]" id="pieces_jointes" multiple
                            class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Ajoutez des pièces justificatives (reçus, factures, etc.)</p>
                    
                    @if($paiement->piecesJointes->count() > 0)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Fichiers attachés</h4>
                            <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                @foreach($paiement->piecesJointes as $piece)
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-2 flex-1 w-0 truncate">
                                                {{ $piece->nom }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <a href="{{ route('admin.paiements.pieces-jointes.download', $piece) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                                Télécharger
                                            </a>
                                            <button type="button" 
                                                    onclick="if(confirm('Supprimer ce fichier ?')) document.getElementById('delete-piece-{{ $piece->id }}').submit()"
                                                    class="ml-2 text-red-600 hover:text-red-500">
                                                Supprimer
                                            </button>
                                            <form id="delete-piece-{{ $piece->id }}" 
                                                  action="{{ route('admin.paiements.pieces-jointes.destroy', $piece) }}" 
                                                  method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.paiements.show', $paiement) }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </a>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <!-- Section pour les actions dangereuses -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-red-700">
                Zone dangereuse
            </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Annuler ce paiement</h4>
                    <p class="mt-1 text-sm text-gray-500">Marquer ce paiement comme annulé sans le supprimer.</p>
                    <div class="mt-4">
                        <button type="button"
                                onclick="if(confirm('Êtes-vous sûr de vouloir annuler ce paiement ?')) document.getElementById('annuler-paiement-form').submit()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Annuler le paiement
                        </button>
                        <form id="annuler-paiement-form" action="{{ route('admin.paiements.annuler', $paiement) }}" method="POST" class="hidden">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="redirect_to" value="{{ route('admin.paiements.edit', $paiement) }}">
                        </form>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Supprimer définitivement</h4>
                    <p class="mt-1 text-sm text-gray-500">Cette action est irréversible. Toutes les données liées seront perdues.</p>
                    <div class="mt-4">
                        <button type="button"
                                onclick="if(confirm('Êtes-vous absolument sûr de vouloir supprimer définitivement ce paiement ? Cette action ne peut pas être annulée.')) document.getElementById('supprimer-paiement-form').submit()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Supprimer définitivement
                        </button>
                        <form id="supprimer-paiement-form" action="{{ route('admin.paiements.destroy', $paiement) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calcul automatique du montant total
        function calculerMontantTotal() {
            const montant = parseFloat(document.getElementById('montant').value) || 0;
            const fraisSupp = parseFloat(document.getElementById('frais_supplementaires').value) || 0;
            const remise = parseFloat(document.getElementById('remise').value) || 0;
            
            const total = montant + fraisSupp - remise;
            document.getElementById('montant_total').value = total.toFixed(2).replace(/\./g, ',');
        }

        // Écouter les changements sur les champs de montant
        ['montant', 'frais_supplementaires', 'remise'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculerMontantTotal);
        });

        // Initialiser le calcul au chargement
        calculerMontantTotal();

        // Gestion de l'affichage conditionnel des champs en fonction du type de paiement
        function toggleChampsParTypePaiement() {
            const typePaiement = document.getElementById('type_paiement').value;
            const champPeriode = document.getElementById('periode').closest('div[class*="sm:col-span"]');
            
            if (typePaiement === 'mensualite') {
                champPeriode.style.display = 'block';
            } else {
                champPeriode.style.display = 'none';
            }
        }

        // Écouter les changements sur le type de paiement
        document.getElementById('type_paiement').addEventListener('change', toggleChampsParTypePaiement);
        
        // Initialiser l'affichage au chargement
        toggleChampsParTypePaiement();

        // Initialiser les tooltips si vous utilisez une bibliothèque comme Tippy.js
        // tippy('[data-tippy-content]');
    });
</script>
@endpush
