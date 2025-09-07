<div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                window.addEventListener('userDeleted', (event) => {
                    if (event.detail.type === 'success') {
                        // Rafraîchir la liste après un court délai
                        setTimeout(() => {
                            Livewire.dispatch('userListUpdated');
                        }, 500);
                    }
                });
            });
        </script>
    @endpush
</div>
