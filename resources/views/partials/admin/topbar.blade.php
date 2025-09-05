<!-- Topbar -->
<header class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button id="mobileMenuBtn" class="md:hidden p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
            <i class="fas fa-bars text-lg"></i>
            <span class="sr-only">Menu</span>
        </button>

        <!-- Right side -->
        <div class="flex items-center space-x-4">
            <!-- Theme toggle -->
            <button id="theme-toggle" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
                <span class="sr-only">Toggle theme</span>
            </button>

            <!-- User menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-200 font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden md:inline text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                </button>
                
                <!-- Dropdown menu -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50">
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-user-circle mr-2"></i> Mon profil
                    </a>
                    <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-cog mr-2"></i> Paramètres
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumbs -->
    @hasSection('breadcrumbs')
        <div class="px-4 sm:px-6 lg:px-8 py-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <nav class="flex" aria-label="Fil d'Ariane">
                <ol class="flex items-center space-x-2 text-sm">
                    <li class="flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                            <i class="fas fa-home mr-2"></i>Tableau de bord
                        </a>
                    </li>
                    @yield('breadcrumbs')
                </ol>
            </nav>
        </div>
    @endif
</header>
