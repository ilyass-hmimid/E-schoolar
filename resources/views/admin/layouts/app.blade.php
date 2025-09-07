<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-Schoolar') }} - @yield('title', 'Tableau de bord')</title>

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
    
    @stack('styles')
    <style>
        /* Styles pour le contenu principal */
        .main-content {
            transition: margin-left 0.3s ease;
        }
        
        @media (min-width: 768px) {
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
        }
        
        /* Ajustement pour les petits écrans */
        @media (max-width: 767px) {
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }
        
        /* Style pour le fond flou lors de l'ouverture du menu mobile */
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
        
        .sidebar-overlay.open {
            display: block;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    @include('partials.navigation')
    
    <div class="flex flex-1 pt-16">
        @auth
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed top-0 left-0 z-20 w-64 h-full pt-16 bg-white border-r border-gray-200 md:translate-x-0">
            @if(isset($isAdmin) && $isAdmin)
                @include('partials.sidebar.admin')
            @elseif(isset($isStudent) && $isStudent)
                @include('partials.sidebar.student')
            @elseif(isset($isProfessor) && $isProfessor)
                @include('partials.sidebar.professor')
            @elseif(isset($isParent) && $isParent)
                @include('partials.sidebar.parent')
            @else
                <!-- Sidebar par défaut pour les utilisateurs sans rôle spécifique -->
                <div class="p-4">
                    <p class="text-sm text-gray-500">Menu de navigation non disponible pour votre rôle.</p>
                </div>
            @endif
        </aside>
        @endauth
        
        <!-- Overlay pour le menu mobile -->
        <div id="sidebarOverlay" class="sidebar-overlay md:hidden"></div>
        
        <!-- Main Content -->
        <div class="main-content flex-1 w-full">
            <main class="p-4 md:p-6">
                @yield('header')
                
                @if (session('success'))
                    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                        <p>{{ session('success') }}</p>
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
            
            @include('partials.footer')
        </div>
    </div>
    
    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Vérifier si on est sur mobile
            let isMobile = window.innerWidth < 768;
            
            // Fonction pour ouvrir/fermer le menu latéral
            function toggleSidebar() {
                if (isMobile) {
                    sidebar.classList.toggle('open');
                    sidebarOverlay.classList.toggle('open');
                    document.body.classList.toggle('overflow-hidden');
                }
            }
            
            // Événements pour le bouton de basculement du menu
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            // Fermer le menu en cliquant sur l'overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    this.classList.remove('open');
                    document.body.classList.remove('overflow-hidden');
                });
            }
            
            // Fermer le menu en cliquant sur un lien
            const sidebarLinks = document.querySelectorAll('#sidebar a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (isMobile) {
                        sidebar.classList.remove('open');
                        if (sidebarOverlay) {
                            sidebarOverlay.classList.remove('open');
                        }
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });
            
            // Gérer le redimensionnement de la fenêtre
            window.addEventListener('resize', function() {
                const newIsMobile = window.innerWidth < 768;
                
                if (isMobile !== newIsMobile) {
                    if (!newIsMobile) {
                        // Passage en mode desktop
                        sidebar.classList.remove('open');
                        if (sidebarOverlay) {
                            sidebarOverlay.classList.remove('open');
                        }
                        document.body.classList.remove('overflow-hidden');
                    }
                    isMobile = newIsMobile;
                }
            });
        });
    </script>
</body>
</html>
