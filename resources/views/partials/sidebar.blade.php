<!-- Backdrop pour le menu mobile -->
<div id="mobileMenuBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2">
            <!-- Tableau de bord -->
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-tachometer-alt w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Tableau de bord</span>
                </a>
            </li>
            
            <!-- Élèves -->
            <li>
                <a href="{{ route('admin.eleves.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.eleves.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-user-graduate w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Élèves</span>
                </a>
            </li>
            
            <!-- Professeurs -->
            <li>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <i class="fas fa-chalkboard-teacher w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Professeurs</span>
                </a>
            </li>
            
            <!-- Paiements -->
            <li>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <i class="fas fa-credit-card w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Paiements</span>
                </a>
            </li>
            
            <!-- Absences -->
            <li>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <i class="fas fa-calendar-times w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Absences</span>
                </a>
            </li>
            
            <!-- Matières -->
            <li>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <i class="fas fa-book w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Matières</span>
                </a>
            </li>
            
            <!-- Classes -->
            <li>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <i class="fas fa-school w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Classes</span>
                </a>
            </li>
            
            <!-- Utilisateurs -->
            <li>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <i class="fas fa-users-cog w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Utilisateurs</span>
                </a>
            </li>
            
            <!-- Paramètres -->
            <li class="mt-auto">
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <i class="fas fa-cog w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Paramètres</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Script pour la gestion du menu mobile -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
        
        if (toggleButton && sidebar) {
            // Gestion du clic sur le bouton de bascule
            toggleButton.addEventListener('click', function() {
                sidebar.classList.toggle('-translate-x-full');
                mobileMenuBackdrop.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden', !sidebar.classList.contains('-translate-x-full'));
            });
            
            // Gestion du clic sur le fond pour fermer le menu
            if (mobileMenuBackdrop) {
                mobileMenuBackdrop.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    mobileMenuBackdrop.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }
            
            // Gestion du redimensionnement de l'écran
            function handleResize() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                    if (mobileMenuBackdrop) mobileMenuBackdrop.classList.add('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            }
            
            // Ajouter l'écouteur d'événement de redimensionnement
            window.addEventListener('resize', handleResize);
            
            // Appeler handleResize au chargement
            handleResize();
        }
    });
</script>
