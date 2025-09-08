<!-- Modal de paiement -->
<div id="paiement-modal-{{ $salaire->id }}" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.salaires.payer', $salaire) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Enregistrer un paiement
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Vous êtes sur le point d'enregistrer le paiement du salaire pour 
                                    <span class="font-medium">{{ $salaire->professeur->nom_complet }}</span>
                                    pour la période de <span class="font-medium">{{ $salaire->periode->format('F Y') }}</span>.
                                </p>
                                
                                <div class="mt-4 bg-blue-50 p-4 rounded-md">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Salaire net</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Statut actuel</p>
                                            @php
                                                $statusClasses = [
                                                    'en_attente' => 'bg-yellow-100 text-yellow-800',
                                                    'paye' => 'bg-green-100 text-green-800',
                                                    'retard' => 'bg-red-100 text-red-800',
                                                ][$salaire->statut];
                                                
                                                $statusLabels = [
                                                    'en_attente' => 'En attente',
                                                    'paye' => 'Payé',
                                                    'retard' => 'En retard',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                                {{ $statusLabels[$salaire->statut] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="date_paiement" class="block text-sm font-medium text-gray-700">Date de paiement</label>
                                        <div class="mt-1">
                                            <input type="date" name="date_paiement" id="date_paiement" required
                                                value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        @error('date_paiement')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="type_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                                        <select id="type_paiement" name="type_paiement" required
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                            <option value="virement" {{ old('type_paiement', $salaire->type_paiement) == 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                                            <option value="cheque" {{ old('type_paiement', $salaire->type_paiement) == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                            <option value="especes" {{ old('type_paiement', $salaire->type_paiement) == 'especes' ? 'selected' : '' }}>Espèces</option>
                                        </select>
                                        @error('type_paiement')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
                                        <div class="mt-1">
                                            <input type="text" name="reference" id="reference"
                                                value="{{ old('reference', $salaire->reference) }}"
                                                placeholder="N° de chèque, référence virement, etc."
                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        @error('reference')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="notifier_professeur" name="notifier_professeur" type="checkbox" value="1"
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="notifier_professeur" class="font-medium text-gray-700">Notifier le professeur par email</label>
                                            <p class="text-gray-500">Un email sera envoyé au professeur pour l'informer du paiement.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Enregistrer le paiement
                    </button>
                    <button type="button" onclick="closePaiementModal({{ $salaire->id }})" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openPaiementModal(salaireId) {
        document.getElementById(`paiement-modal-${salaireId}`).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closePaiementModal(salaireId) {
        document.getElementById(`paiement-modal-${salaireId}`).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Fermer la modale avec la touche Échap
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('[id^="paiement-modal-"]').forEach(modal => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        }
    });
    
    // Fermer en cliquant en dehors de la modale
    window.addEventListener('click', function(event) {
        document.querySelectorAll('[id^="paiement-modal-"]').forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });
</script>
@endpush
