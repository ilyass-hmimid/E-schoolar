<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Allo Tawjih') }} - @yield('title', 'Accueil')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite CSS/JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <style>
        /* Styles pour le contenu principal */
        .main-content {
            transition: all 0.3s ease;
            min-height: calc(100vh - 4rem);
            padding-top: 1rem;
            width: 100%;
            margin-left: 0;
        }
        
        /* Styles pour la barre latérale */
        .sidebar {
            transition: transform 0.3s ease-in-out;
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            width: 16rem;
            z-index: 40;
            transform: translateX(-100%);
        }
        
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
        }
        
        /* Styles pour l'overlay du menu mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 30;
        }
        
        /* Styles pour les écrans moyens et grands */
        @media (min-width: 768px) {
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
            
            .sidebar {
                transform: translateX(0);
            }
            
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* Classes utilitaires pour les transformations */
        .-translate-x-full {
            transform: translateX(-100%);
        }
        
        .translate-x-0 {
            transform: translateX(0);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    @include('partials.navigation')
    
    <div class="flex flex-1">
        @auth
        <!-- Sidebar Admin -->
        <aside id="sidebar" class="sidebar bg-white border-r border-gray-200 pt-16">
            <div class="h-full overflow-y-auto">
                @include('admin.partials.sidebar')
            </div>
        </aside>
        
        <!-- Overlay pour le menu mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden"></div>
        @endauth
        
        <!-- Main Content -->
        <div class="main-content w-full transition-all duration-300 ease-in-out">
            <main class="container mx-auto px-4 md:px-6 py-4">
                <!-- Bouton pour afficher/masquer la sidebar sur mobile -->
                <button id="sidebarToggle" class="md:hidden fixed top-4 left-4 z-30 p-2 rounded-md bg-white shadow-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-bars"></i>
                </button>
                
                @yield('header')
                
                @if (session('success'))
                    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                
                @if (session('warning'))
                    <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded" role="alert">
                        <p>{{ session('warning') }}</p>
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (!sidebar) {
                console.error('Sidebar element not found');
                return;
            }
            
            // Fonction pour vérifier la largeur de l'écran et ajuster la barre latérale
            function checkScreenSize() {
                if (window.innerWidth < 768) {
                    // Sur mobile, masquer la barre latérale par défaut
                    sidebar.classList.add('-translate-x-full');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.add('hidden');
                    }
                    document.body.style.overflow = 'auto';
                } else {
                    // Sur desktop, toujours afficher la barre latérale
                    sidebar.classList.remove('-translate-x-full');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.add('hidden');
                    }
                    document.body.style.overflow = 'auto';
                }
            }
            
            // Fonction pour basculer la visibilité de la barre latérale
            function toggleSidebar() {
                if (window.innerWidth < 768) {
                    sidebar.classList.toggle('-translate-x-full');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.toggle('hidden');
                    }
                    // Empêcher le défilement du contenu principal lorsque la barre latérale est ouverte
                    document.body.style.overflow = sidebar.classList.contains('-translate-x-full') ? 'auto' : 'hidden';
                }
            }
            
            // Vérifier la taille de l'écran au chargement
            checkScreenSize();
            
            // Gérer le clic sur le bouton de bascule
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            // Fermer la barre latérale lors du clic sur l'overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
            }
            
            // Vérifier la taille de l'écran lors du redimensionnement
            window.addEventListener('resize', checkScreenSize);
        });
    </script>
</body>
</html>
