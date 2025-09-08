<!-- Modal de suppression -->
<div id="delete-modal-{{ $salaire->id }}" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.salaires.destroy', $salaire) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Supprimer la fiche de paie
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir supprimer définitivement cette fiche de paie ? 
                                    Cette action est irréversible et supprimera toutes les données associées.
                                </p>
                                
                                <div class="mt-4 bg-red-50 p-4 rounded-md">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Professeur</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $salaire->professeur->nom_complet }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Période</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $salaire->periode->format('F Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Salaire net</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Statut</p>
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
                                
                                <div class="mt-4">
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="confirm_delete" name="confirm_delete" type="checkbox" required
                                                class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="confirm_delete" class="font-medium text-gray-700">Je confirme la suppression</label>
                                            <p class="text-gray-500">Cochez cette case pour confirmer la suppression.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Supprimer définitivement
                    </button>
                    <button type="button" onclick="closeDeleteModal({{ $salaire->id }})" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDeleteModal(salaireId) {
        document.getElementById(`delete-modal-${salaireId}`).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closeDeleteModal(salaireId) {
        document.getElementById(`delete-modal-${salaireId}`).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Fermer la modale avec la touche Échap
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('[id^="delete-modal-"]').forEach(modal => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        }
    });
    
    // Fermer en cliquant en dehors de la modale
    window.addEventListener('click', function(event) {
        document.querySelectorAll('[id^="delete-modal-"]').forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });
</script>
@endpush
