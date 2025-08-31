<div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                window.addEventListener('userDeleted', (event) => {
                    const toast = new bootstrap.Toast(document.getElementById('userNotification'));
                    const toastBody = document.getElementById('toastBody');
                    const toastHeader = document.getElementById('toastHeader');
                    
                    if (event.detail.type === 'success') {
                        toastBody.innerHTML = event.detail.message;
                        toastHeader.className = 'toast-header text-white bg-success';
                        toast.show();
                        
                        // Rafraîchir la liste après un court délai
                        setTimeout(() => {
                            Livewire.dispatch('userListUpdated');
                        }, 500);
                    } else {
                        toastBody.innerHTML = event.detail.message;
                        toastHeader.className = 'toast-header text-white bg-danger';
                        toast.show();
                    }
                });
            });
        </script>
    @endpush

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="userNotification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div id="toastHeader" class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div id="toastBody" class="toast-body"></div>
        </div>
    </div>
</div>
