@props([
    'id' => 'confirmationModal',
    'title' => 'Confirmer la suppression',
    'message' => 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.',
    'confirmText' => 'Supprimer',
    'cancelText' => 'Annuler',
    'confirmColor' => 'red',
    'formId' => null,
    'formAction' => null,
    'formMethod' => 'DELETE',
    'size' => 'md', // sm, md, lg, xl
])

@php
$sizes = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
];

$colors = [
    'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
    'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
    'yellow' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
];
@endphp

<div id="{{ $id }}" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-dark-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $sizes[$size] ?? 'max-w-md' }} sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
                        {{ $title }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-300">
                            {{ $message }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                @if($formId)
                    <form id="{{ $formId }}" action="{{ $formAction }}" method="POST" class="inline-flex w-full sm:ml-3 sm:w-auto">
                        @csrf
                        @if($formMethod !== 'POST')
                            @method($formMethod)
                        @endif
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white {{ $colors[$confirmColor] ?? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800 sm:w-auto sm:text-sm">
                            {{ $confirmText }}
                        </button>
                    </form>
                @else
                    <button type="button" id="{{ $id }}-confirm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white {{ $colors[$confirmColor] ?? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $confirmText }}
                    </button>
                @endif
                
                <button type="button" id="{{ $id }}-cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-700 shadow-sm px-4 py-2 bg-dark-700 text-base font-medium text-gray-300 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm">
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('{{ $id }}');
        const confirmBtn = document.getElementById('{{ $id }}-confirm');
        const cancelBtn = document.getElementById('{{ $id }}-cancel');
        
        // Function to show modal
        window.showModal = function(modalId = '{{ $id }}') {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        };
        
        // Function to hide modal
        window.hideModal = function(modalId = '{{ $id }}') {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        };
        
        // Close modal when clicking cancel button or outside the modal
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                window.hideModal('{{ $id }}');
            });
        }
        
        // Close when clicking outside the modal content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                window.hideModal('{{ $id }}');
            }
        });
        
        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.hideModal('{{ $id }}');
            }
        });
    });
</script>
@endpush
