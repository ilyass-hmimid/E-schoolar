<div class="flex h-full">
    <div class="flex flex-col w-64 h-full bg-white border-r border-gray-200">
        <div class="flex flex-col flex-1 overflow-y-auto">
            <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
                <span class="text-xl font-bold text-gray-800">
                    Administration
                </span>
            </div>
            <div class="flex-1 py-4">
                <nav class="px-2 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Tableau de bord
                    </a>

                    <!-- Gestion des élèves -->
                    <a href="{{ route('admin.eleves.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.eleves.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user-graduate mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Gestion des élèves
                    </a>

                    <!-- Gestion des paiements -->
                    <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed" title="Fonctionnalité à venir">
                        <i class="fas fa-credit-card mr-3"></i>
                        Gestion des paiements
                    </a>

                    <!-- Gestion des absences -->
                    <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed" title="Fonctionnalité à venir">
                        <i class="fas fa-calendar-times mr-3"></i>
                        Gestion des absences
                    </a>

                    <!-- Gestion des classes -->
                    <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed" title="Fonctionnalité à venir">
                        <i class="fas fa-chalkboard mr-3"></i>
                        Gestion des classes
                    </a>

                    <!-- Matières -->
                    <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed" title="Fonctionnalité à venir">
                        <i class="fas fa-book mr-3"></i>
                        Matières
                    </a>

                    <!-- Paramètres -->
                    <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed" title="Fonctionnalité à venir">
                        <i class="fas fa-cog mr-3"></i>
                        Paramètres
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
