<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ Auth::id() ?? '' }}">
        <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
        <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}">

        <title>{{ config('app.name', 'Allo Tawjih') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Scripts -->
        @routes
        @vite([
            'resources/css/app.css', 
            'resources/js/app.js',
            'resources/js/bootstrap.js'
        ])
        
        <!-- Styles -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div id="app" class="min-h-screen flex flex-col">
            <!-- Barre de navigation -->
            @auth
                @include('layouts.navigation')
            @endauth

            <!-- Contenu principal -->
            <main class="flex-grow">
                <!-- En-tête de page -->
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between items-center">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                    {{ $header }}
                                </h2>
                                
                                <!-- Bouton de notification dans la barre d'en-tête -->
                                @auth
                                    <div class="ml-4">
                                        <notification-bell></notification-bell>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </header>
                @endif

                <!-- Contenu de la page -->
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>

            <!-- Notifications toast -->
            <toast-notifications position="top-right"></toast-notifications>

            <!-- Pied de page -->
            <footer class="bg-white shadow-inner mt-auto">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                    </p>
                </div>
            </footer>
        </div>

        <!-- Scripts supplémentaires -->
        @stack('scripts')
    </body>
</html>
