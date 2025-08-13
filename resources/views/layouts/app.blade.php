<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ Auth::id() ?? '' }}">
        <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
        <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}">

        <title>{{ config('app.name', 'Allo Tawjih') }} @yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        
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
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
        <div id="app" class="min-h-screen flex flex-col">
            <!-- Barre de navigation -->
            @auth
                @include('layouts.navigation')
            @endauth

            <!-- Contenu principal -->
            <main class="flex-1">
                @if (isset($header))
                    <div class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <h1 class="text-2xl font-semibold text-gray-900">
                                {{ $header }}
                            </h1>
                        </div>
                    </div>
                @endif

                <!-- Contenu de la page -->
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>

            <!-- Notifications toast -->
            <toast-notifications position="top-right"></toast-notifications>

            <!-- Pied de page -->
            <footer class="bg-white border-t border-gray-200 mt-8">
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
