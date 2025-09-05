<!-- Sidebar -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800 dark:text-white">
                {{ config('app.name') }}
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            @php
                $currentRoute = request()->route()->getName();
                $isActive = function($route) use ($currentRoute) {
                    return str_starts_with($currentRoute, $route) ? 'bg-gray-100 dark:bg-gray-700 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
                };
                
                $isExactActive = function($route) use ($currentRoute) {
                    return $currentRoute === $route ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white';
                };
            @endphp

            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-md group {{ $isActive('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt w-5 mr-3 text-center"></i>
                <span>Tableau de bord</span>
            </a>

            <!-- Utilisateurs -->
            <div x-data="{ open: {{ $isActive('admin.users') ? 'true' : 'false' }} }}" class="space-y-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-md {{ $isActive('admin.users') ? 'bg-gray-100 dark:bg-gray-700 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <i class="fas fa-users w-5 mr-3 text-center"></i>
                        <span>Utilisateurs</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'transform rotate-180': open }"></i>
                </button>
                <div x-show="open" class="pl-12 space-y-1">
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-sm font-medium rounded-md {{ $currentRoute === 'admin.users.index' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        Liste des utilisateurs
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="block px-3 py-2 text-sm font-medium rounded-md {{ $currentRoute === 'admin.users.create' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        Ajouter un utilisateur
                    </a>
                </div>
            </div>

            <!-- Élèves -->
            <a href="{{ route('admin.eleves.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-md group {{ $isActive('admin.eleves') }}">
                <i class="fas fa-user-graduate w-5 mr-3 text-center"></i>
                <span>Élèves</span>
            </a>

            <!-- Professeurs -->
            <a href="{{ route('admin.professeurs.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-md group {{ $isActive('admin.professeurs') }}">
                <i class="fas fa-chalkboard-teacher w-5 mr-3 text-center"></i>
                <span>Professeurs</span>
            </a>

            <!-- Absences -->
            <a href="{{ route('admin.absences.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-md group {{ $isActive('admin.absences') }}">
                <i class="fas fa-calendar-times w-5 mr-3 text-center"></i>
                <span>Absences</span>
            </a>

            <!-- Paiements -->
            <a href="{{ route('admin.paiements.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-md group {{ $isActive('admin.paiements') }}">
                <i class="fas fa-credit-card w-5 mr-3 text-center"></i>
                <span>Paiements</span>
            </a>

            <!-- Statistiques -->
            <a href="{{ route('admin.statistiques') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-md group {{ $isActive('admin.statistiques') }}">
                <i class="fas fa-chart-bar w-5 mr-3 text-center"></i>
                <span>Statistiques</span>
            </a>

            <!-- Paramètres -->
            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <div x-data="{ open: {{ in_array($currentRoute, ['admin.roles.index', 'admin.roles.create', 'admin.roles.edit']) ? 'true' : 'false' }} }}" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-md {{ in_array($currentRoute, ['admin.roles.index', 'admin.roles.create', 'admin.roles.edit']) ? 'bg-gray-100 dark:bg-gray-700 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <i class="fas fa-cog w-5 mr-3 text-center"></i>
                            <span>Paramètres</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'transform rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="pl-12 space-y-1">
                        <a href="{{ route('admin.roles.index') }}" class="block px-3 py-2 text-sm font-medium rounded-md {{ $currentRoute === 'admin.roles.index' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            Rôles et permissions
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</aside>

<!-- Sidebar backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 z-20 bg-black bg-opacity-50 opacity-0 invisible transition-opacity duration-200 ease-in-out md:hidden"></div>

@push('scripts')
<script>
    // Toggle mobile sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        
        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarBackdrop.classList.toggle('opacity-0');
            sidebarBackdrop.classList.toggle('opacity-50');
            sidebarBackdrop.classList.toggle('invisible');
            sidebarBackdrop.classList.toggle('visible');
        }
        
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', toggleSidebar);
        }
        
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', toggleSidebar);
        }
        
        // Close sidebar when clicking on a link on mobile
        const navLinks = document.querySelectorAll('#sidebar a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    toggleSidebar();
                }
            });
        });
    });
</script>
@endpush
