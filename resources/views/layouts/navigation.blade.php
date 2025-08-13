<nav x-data="{ open: false }" class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <x-application-logo class="block h-8 w-auto text-primary-600" />
                        <span class="text-xl font-bold text-gray-900 hidden md:inline-block">{{ config('app.name', 'Allo Tawjih') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:ml-10 md:flex md:space-x-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="px-4 py-2 rounded-lg">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        {{ __('Tableau de bord') }}
                    </x-nav-link>
                    
                    @can('viewAny', App\Models\User::class)
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="px-4 py-2 rounded-lg">
                        <i class="fas fa-users mr-2"></i>
                        {{ __('Utilisateurs') }}
                    </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Right Side Of Navbar -->
            <div class="hidden md:flex md:items-center md:space-x-4">
                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-0 right-0 h-3 w-3 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50">
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex justify-between items-center">
                                <h3 class="font-medium text-gray-900">Notifications</h3>
                                <span class="text-xs bg-primary-100 text-primary-800 px-2 py-1 rounded-full">3 nouvelles</span>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                            <!-- Notification items would be looped here -->
                            <a href="#" class="block p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Bienvenue sur la plateforme</p>
                                        <p class="text-xs text-gray-500 mt-1">Il y a 2 jours</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="p-3 bg-gray-50 text-center">
                            <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-800">Voir toutes les notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative ml-4" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                        <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-medium">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="hidden md:inline-block text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                        <svg :class="{ 'transform rotate-180': open }" class="w-4 h-4 text-gray-500 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-circle mr-2 text-gray-500"></i>
                                {{ __('Mon profil') }}
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2 text-gray-500"></i>
                                {{ __('Paramètres') }}
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('Déconnexion') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center md:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                    <span class="sr-only">Open main menu</span>
                    <i x-show="!open" class="fas fa-bars h-6 w-6"></i>
                    <i x-show="open" class="fas fa-times h-6 w-6" style="display: none;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" class="md:hidden">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center px-3 py-2 rounded-lg">
                <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                {{ __('Tableau de bord') }}
            </x-responsive-nav-link>
            
            @can('viewAny', App\Models\User::class)
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="flex items-center px-3 py-2 rounded-lg">
                <i class="fas fa-users mr-3 w-5 text-center"></i>
                {{ __('Utilisateurs') }}
            </x-responsive-nav-link>
            @endcan
        </div>
        
        <!-- Mobile user menu -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-5">
                <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-medium">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')" class="flex items-center px-3 py-2 rounded-lg">
                    <i class="fas fa-user-circle mr-3 w-5 text-center"></i>
                    {{ __('Mon profil') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#" class="flex items-center px-3 py-2 rounded-lg">
                    <i class="fas fa-cog mr-3 w-5 text-center"></i>
                    {{ __('Paramètres') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200 mt-2 pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                        {{ __('Déconnexion') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
