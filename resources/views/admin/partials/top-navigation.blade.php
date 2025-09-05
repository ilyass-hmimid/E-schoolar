<!-- Top Navigation -->
<header class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Mobile menu button -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="flex-1 flex justify-between px-4 md:px-6">
            <!-- Search bar -->
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" placeholder="Rechercher...">
                </div>
            </div>

            <!-- Right side items -->
            <div class="flex items-center space-x-4">
                <!-- Dark mode toggle -->
                <button @click="toggleDarkMode" class="p-2 rounded-full text-gray-500 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <!-- Notifications -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="p-2 rounded-full text-gray-500 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                        <span class="sr-only">Notifications</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10" x-cloak>
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 font-medium border-b border-gray-100 dark:border-gray-700">
                                Notifications
                            </div>
                            <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=John+Doe" alt="John Doe">
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Nouvelle absence</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">John Doe est absent aujourd'hui</p>
                                        <p class="text-xs text-gray-400">Il y a 5 minutes</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-2 text-center border-t border-gray-100 dark:border-gray-700">
                            <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                                Voir toutes les notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User menu -->
                <div x-data="{ open: false }" class="relative ml-4">
                    <button @click="open = !open" class="flex items-center max-w-xs rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu" aria-expanded="false" aria-haspopup="true">
                        <span class="sr-only">Ouvrir le menu utilisateur</span>
                        <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ Auth::user()->name }}">
                    </button>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10" role="menu" aria-orientation="vertical" aria-labelledby="user-menu" x-cloak>
                        <div class="py-1" role="none">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" role="menuitem">
                                Mon profil
                            </a>
                            {{-- Settings link removed as the route is not defined
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" role="menuitem">
                                Paramètres
                            </a>
                            --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" role="menuitem">
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
