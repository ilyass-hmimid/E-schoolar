<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Meta pour l'authentification et la configuration -->
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="app-name" content="{{ config('app.name') }}">
    <meta name="app-env" content="{{ config('app.env') }}">
    <meta name="user-role" content="{{ Auth::user()->role }}">

    <title>@yield('title', config('app.name', 'Tableau de bord'))</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Scripts -->
    <script>
        // Détection du thème système
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    
    @stack('styles')
    
    <!-- Scripts -->
    @livewireStyles
    
    <script>
        // Gestion du thème au chargement pour éviter le FOUC
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-sans antialiased min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Mobile menu button -->
        <button id="mobileMenuBtn" class="md:hidden fixed top-6 left-6 z-50 bg-primary-600 p-3 rounded-xl text-white shadow-lg hover:bg-primary-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-900">
            <i class="fas fa-bars text-lg"></i>
            <span class="sr-only">Menu</span>
        </button>

        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
            <!-- En-tête de page -->
            <header class="mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@yield('page-title', 'Tableau de bord')</h1>
                    <div class="flex items-center space-x-4">
                        <!-- Bouton de basculement du thème -->
                        <button id="theme-toggle" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-moon dark:hidden"></i>
                            <i class="fas fa-sun hidden dark:block"></i>
                        </button>
                        
                        <!-- Menu utilisateur -->
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
                
                <!-- Fil d'Ariane -->
                @hasSection('breadcrumbs')
                    <nav class="mt-4" aria-label="Fil d'Ariane">
                        <ol class="flex flex-wrap items-center text-sm text-gray-600 dark:text-gray-400">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                                    <i class="fas fa-home mr-2"></i>Tableau de bord
                                </a>
                            </li>
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                @endif
            </header>

            <!-- Messages flash -->
            @if(session('success') || session('error') || $errors->any())
                <div class="mb-6">
                    @if(session('success'))
                        <div class="alert alert-success flex items-center justify-between p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" class="text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300" data-dismiss="alert" aria-label="Fermer">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger flex items-center justify-between p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" data-dismiss="alert" aria-label="Fermer">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Erreur !</strong>
                                </div>
                                <button type="button" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" data-dismiss="alert" aria-label="Fermer">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul class="mt-1.5 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Contenu principal -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    @livewireScripts
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/eleves.js') }}"></script>
    @stack('scripts')
    
    <script>
        // Gestion du thème
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const html = document.documentElement;
            
            // Toggle du thème
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    html.classList.toggle('dark');
                    localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
                });
            }
            
            // Fermeture des alertes
            document.addEventListener('click', function(event) {
                if (event.target.closest('[data-dismiss="alert"]')) {
                    event.target.closest('.alert').remove();
                }
            });
            
            // Fermeture des menus déroulants au clic à l'extérieur
            document.addEventListener('click', function(event) {
                if (!event.target.closest('[x-data]') && !event.target.closest('.dropdown-menu')) {
                    document.querySelectorAll('[x-data]').forEach(el => {
                        if (el.__x && el.__x.$data && typeof el.__x.$data.open !== 'undefined') {
                            el.__x.$data.open = false;
                        }
                    });
                }
            });
            
            // Fermeture des menus avec la touche Échap
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    document.querySelectorAll('[x-data]').forEach(el => {
                        if (el.__x && el.__x.$data && typeof el.__x.$data.open !== 'undefined') {
                            el.__x.$data.open = false;
                        }
                    });
                }
            });
            
            // Menu mobile
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
</body>
</html>
