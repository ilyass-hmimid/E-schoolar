<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ Auth::id() ?? '' }}">
        <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
        <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}">

        <title>{{ config('app.name', 'Allo Tawjih') }} @yield('title')</title>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Custom Navigation CSS -->
        <link href="{{ asset('css/custom-nav.css') }}" rel="stylesheet">
        
        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Vite JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'public/css/admin-dashboard.css'])
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- Styles personnalisés -->
        @stack('styles')
        <style>
            [x-cloak] { display: none !important; }
            
            /* Styles personnalisés */
            body {
                font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
                padding-top: 56px; /* Hauteur de la navbar */
            }
            
            .content-header {
                padding: 1.5rem 0;
                margin-bottom: 1.5rem;
                border-bottom: 1px solid #e9ecef;
                background-color: #f8f9fa;
            }
            
            .content-title {
                font-size: 1.75rem;
                font-weight: 600;
                margin: 0;
                color: #2c3e50;
            }
            
            .main-content {
                min-height: calc(100vh - 160px); /* Hauteur du viewport - hauteur du header et du footer */
                padding: 1.5rem 0;
            }
            
            footer {
                background-color: #f8f9fa;
                padding: 1.5rem 0;
                margin-top: 2rem;
                border-top: 1px solid #e9ecef;
            }
        </style>
    </head>
    <body>
        <div id="app">
            <!-- Barre de navigation -->
            @auth
                @include('layouts.navigation')
            @endauth

            <!-- Contenu principal -->
            <main class="main-content">
                @if (isset($header))
                    <div class="content-header">
                        <div class="container">
                            <h1 class="content-title">
                                {{ $header }}
                            </h1>
                        </div>
                    </div>
                @endif

                <!-- Contenu de la page -->
                <div class="container py-4">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </main>

            <!-- Notifications toast -->
            <toast-notifications position="top-right"></toast-notifications>

            <!-- Pied de page -->
            <footer>
                <div class="container">
                    <p class="text-center text-muted mb-0">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                    </p>
                </div>
            </footer>
        </div>

        <!-- Scripts supplémentaires -->
        @stack('scripts')
    </body>
</html>
