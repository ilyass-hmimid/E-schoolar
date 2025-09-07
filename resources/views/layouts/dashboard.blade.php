<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ 
    darkMode: localStorage.getItem('dark') === 'true',
    sidebarOpen: true,
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('dark', this.darkMode);
        document.documentElement.classList.toggle('dark', this.darkMode);
    },
    init() {
        // Set initial dark mode
        if (localStorage.getItem('dark') === 'true' || 
            (!('dark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            this.darkMode = true;
            document.documentElement.classList.add('dark');
        }
    }
}" x-init="init">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Tableau de bord')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full fixed md:static inset-y-0 z-30"
               :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
               x-cloak>
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                    <div class="flex items-center">
                        <x-application-logo class="block h-8 w-auto text-white" />
                        <span class="ml-2 text-xl font-bold">E-Schoolar</span>
                    </div>
                    <button @click="sidebarOpen = false" class="md:hidden p-1 text-gray-400 hover:text-white">
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                        Tableau de bord
                    </a>

                    <a href="{{ route('admin.eleves.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.eleves.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-users mr-3 w-5 text-center"></i>
                        Gestion des élèves
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow-sm">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-500 hover:text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                        <i class="fas fa-bars h-6 w-6"></i>
                    </button>

                    <div class="flex-1">
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">@yield('title', 'Tableau de bord')</h1>
                    </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button @click="toggleDarkMode" class="p-2 text-gray-500 hover:text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i x-show="!darkMode" class="fas fa-moon h-5 w-5"></i>
                            <i x-show="darkMode" class="fas fa-sun h-5 w-5"></i>
                        </button>

                        <!-- Profile dropdown -->
                        <div class="relative ml-3" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu">
                                <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200 hidden md:block">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
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
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-200 dark:divide-gray-700 focus:outline-none z-50">
                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                @yield('content')
            </main>
                            <div class="mt-4 flex md:mt-0 md:ml-4">
                                @yield('actions')
                            </div>
                        </div>

                        <!-- Flash Messages -->
                        @if (session('status'))
                            <div class="mb-6 rounded-md bg-green-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">
                                            {{ session('status') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">
                                            {{ __('Whoops! Something went wrong.') }}
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Page content -->
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('modals')
    @stack('scripts')
    <script>
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('[x-data]');
            const mobileMenuButton = document.querySelector('[x-on\:click*="mobileOpen = true"]');
            
            if (!sidebar.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                const mobileSidebar = Alpine.store('sidebar');
                if (mobileSidebar && mobileSidebar.mobileOpen) {
                    mobileSidebar.mobileOpen = false;
                }
            }
        });
    </script>
</body>
</html>
