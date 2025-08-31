@extends('layouts.admin')

@section('content')
    @livewire('admin.roles.roles-list')
@endsection

@push('styles')
<style>
    /* Styles pour les badges de statut */
    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    
    .badge-success {
        @apply bg-green-100 text-green-800;
    }
    
    .badge-warning {
        @apply bg-yellow-100 text-yellow-800;
    }
    
    .badge-danger {
        @apply bg-red-100 text-red-800;
    }
    
    .badge-info {
        @apply bg-blue-100 text-blue-800;
    }
    
    /* Animation pour les transitions */
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.2s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
    
    /* Style pour les boutons d'action */
    .action-btn {
        @apply p-2 rounded-full hover:bg-gray-100 transition-colors duration-200;
    }
    
    .action-btn-edit {
        @apply text-blue-600 hover:text-blue-800;
    }
    
    .action-btn-delete {
        @apply text-red-600 hover:text-red-800;
    }
    
    /* Style pour les cases à cocher des permissions */
    .permission-group {
        @apply mb-6 p-4 border border-gray-200 rounded-lg;
    }
    
    .permission-group-title {
        @apply text-lg font-medium text-gray-700 mb-3 pb-2 border-b border-gray-100;
    }
    
    .permission-item {
        @apply flex items-center mb-2;
    }
    
    .permission-checkbox {
        @apply h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded;
    }
    
    .permission-label {
        @apply ml-2 text-sm text-gray-700;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Gestion de la suppression d'un rôle avec confirmation
        window.addEventListener('confirm-role-delete', event => {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action est irréversible.')) {
                Livewire.dispatch('deleteRole', { id: event.detail.id });
            }
        });
        
        // Gestion des notifications
        Livewire.on('notify', (data) => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: data.type,
                title: data.message
            });
        });
    });
</script>
@endpush
