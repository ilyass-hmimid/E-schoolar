<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Allo Tawjih') }} - @yield('title')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .active {
            background-color: #e5e7eb;
            color: #1f2937;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Simple Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800">
                                Allo Tawjih
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar and Main Content -->
        <div class="flex">
            <!-- Sidebar -->
            <div class="w-64 bg-white shadow-md h-screen sticky top-0">
                <div class="p-4">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Menu</h2>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 font-medium' : '' }}">
                                <i class="fas fa-tachometer-alt mr-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.parametres.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.parametres.*') ? 'bg-gray-100 font-medium' : '' }}">
                                <i class="fas fa-cog mr-2"></i> Paramètres
                            </a>
                        </li>
                        <!-- Add more menu items as needed -->
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="flex-1 p-8">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Succès</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
