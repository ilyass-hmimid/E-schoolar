<!-- Sidebar -->
<aside id="sidebar" 
       x-data="{ open: window.innerWidth >= 768 }"
       :class="{ '-translate-x-full': !open }"
       @resize.window="open = window.innerWidth >= 768"
       class="fixed inset-y-0 left-0 z-20 w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0">
    
    <div class="flex flex-col h-full">
        <!-- App Name -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-semibold text-white">
                {{ config('app.name') }}
            </a>
            <button @click="open = false" class="md:hidden p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800">
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

            <!-- Absences -->
            <a href="{{ route('admin.absences.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('admin.absences.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.absences.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Absences
            </a>

            <!-- Paiements -->
            <a href="{{ route('admin.paiements.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('admin.paiements.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.paiements.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Paiements
            </a>
        </nav>

        <!-- Bottom section -->
        <div class="mt-auto p-4 border-t border-gray-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md">
                    <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Backdrop -->
<div x-show="$store.sidebar.mobileOpen" 
     @click="$store.sidebar.close()" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-10 bg-black bg-opacity-50 md:hidden" 
     x-cloak>
</div>
