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
                <a href="{{ route('admin.professeurs.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.professeurs.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-chalkboard-teacher w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Professeurs</span>
                </a>
            </li>
            
            <!-- Paiements -->
            <li>
                <a href="{{ route('admin.paiements.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.paiements.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-credit-card w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Paiements</span>
                </a>
            </li>
            
            <!-- Absences -->
            <li>
                <a href="{{ route('admin.absences.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.absences.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-calendar-times w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Absences</span>
                </a>
            </li>
            
            <!-- Matières -->
            <li>
                <a href="{{ route('admin.matieres.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.matieres.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-book w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Matières</span>
                </a>
            </li>
            
            <!-- Classes -->
            <li>
                <a href="{{ route('admin.classes.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.classes.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-school w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Classes</span>
                </a>
            </li>
            
            <!-- Utilisateurs -->
            <li>
                <a href="{{ route('admin.users.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-users-cog w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-white"></i>
                    <span class="ml-3">Utilisateurs</span>
                </a>
            </li>
            
            <!-- Paramètres -->
            <li class="mt-auto">
                <a href="{{ route('admin.settings') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.settings') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
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
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
        
        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', () => {
                const isHidden = sidebar.classList.contains('-translate-x-full');
                
                if (isHidden) {
                    // Ouvrir le menu
                    sidebar.classList.remove('-translate-x-full');
                    document.body.classList.add('overflow-hidden');
                    mobileMenuBackdrop.classList.remove('hidden');
                } else {
                    // Fermer le menu
                    sidebar.classList.add('-translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                    mobileMenuBackdrop.classList.add('hidden');
                }
            });
            
            // Fermer le menu en cliquant sur le backdrop
            if (mobileMenuBackdrop) {
                mobileMenuBackdrop.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                    mobileMenuBackdrop.classList.add('hidden');
                });
            }
            
            // Fermer le menu en redimensionnant l'écran
            function handleResize() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                    if (mobileMenuBackdrop) mobileMenuBackdrop.classList.add('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            }
            
            window.addEventListener('resize', handleResize);
            handleResize();
        }
    });
</script>
