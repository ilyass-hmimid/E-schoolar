<!-- Sidebar -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-20 w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full">
    <div class="flex flex-col h-full">
        <!-- App Name -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-semibold text-white">
                {{ config('app.name') }}
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Tableau de bord
            </a>

            <!-- Students -->
            <a href="{{ route('admin.eleves.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('admin.eleves.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.eleves.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Élèves
            </a>

            <!-- Absences (Désactivé) -->
            <div class="flex items-center px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed opacity-50">
                <svg class="mr-3 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Absences (Bientôt disponible)
            </div>

            <!-- Paiements des élèves (Désactivé) -->
            <div class="flex items-center px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed opacity-50">
                <svg class="mr-3 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Paiements (Bientôt disponible)
            </div>

            <!-- Classes (Désactivé) -->
            <div class="flex items-center px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed opacity-50">
                <svg class="mr-3 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Classes (Bientôt disponible)
            </div>
        </nav>

        <div class="mt-3 space-y-1">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md">
                    Déconnexion
                </button>
            </form>
            </div>
        </div>
    </div>
</aside>

<!-- Backdrop -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden" x-cloak></div>
