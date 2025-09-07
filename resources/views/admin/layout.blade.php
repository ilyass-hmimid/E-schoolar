<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Schoolar - Administration')</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    
    @stack('styles')
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-indigo-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-xl">E-Schoolar</a>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white px-3 py-2 rounded-md text-sm font-medium">Tableau de bord</a>
                                <a href="{{ route('admin.eleves.index') }}" class="{{ request()->routeIs('admin.eleves.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white px-3 py-2 rounded-md text-sm font-medium">Élèves</a>
                                <a href="{{ route('admin.professeurs.index') }}" class="{{ request()->routeIs('admin.professeurs.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white px-3 py-2 rounded-md text-sm font-medium">Professeurs</a>
                                <a href="{{ route('admin.matieres.index') }}" class="{{ request()->routeIs('admin.matieres.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white px-3 py-2 rounded-md text-sm font-medium">Matières</a>
                                <a href="{{ route('admin.paiements.index') }}" class="{{ request()->routeIs('admin.paiements.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white px-3 py-2 rounded-md text-sm font-medium">Paiements</a>
                                <a href="{{ route('admin.absences.index') }}" class="{{ request()->routeIs('admin.absences.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white px-3 py-2 rounded-md text-sm font-medium">Absences</a>
                                <a href="{{ route('admin.salaires.index') }}" class="{{ request()->routeIs('admin.salaires.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white px-3 py-2 rounded-md text-sm font-medium">Salaires</a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <!-- Profile dropdown -->
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <div>
                                    <button @click="open = !open" class="max-w-xs bg-indigo-600 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white" id="user-menu" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Ouvrir le menu utilisateur</span>
                                        <div class="h-8 w-8 rounded-full bg-indigo-400 flex items-center justify-center text-white font-medium">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    </button>
                                </div>
                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu" style="display: none;">
                                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Votre profil</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <!-- Mobile menu button -->
                        <button type="button" class="bg-indigo-600 inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false" @click="mobileMenuOpen = !mobileMenuOpen">
                            <span class="sr-only">Ouvrir le menu principal</span>
                            <!-- Heroicon name: outline/menu -->
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- Heroicon name: outline/x -->
                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div class="md:hidden" id="mobile-menu" x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" style="display: none;">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white block px-3 py-2 rounded-md text-base font-medium">Tableau de bord</a>
                    <a href="{{ route('admin.eleves.index') }}" class="{{ request()->routeIs('admin.eleves.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white block px-3 py-2 rounded-md text-base font-medium">Élèves</a>
                    <a href="{{ route('admin.professeurs.index') }}" class="{{ request()->routeIs('admin.professeurs.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white block px-3 py-2 rounded-md text-base font-medium">Professeurs</a>
                    <a href="{{ route('admin.matieres.index') }}" class="{{ request()->routeIs('admin.matieres.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white block px-3 py-2 rounded-md text-base font-medium">Matières</a>
                    <a href="{{ route('admin.paiements.index') }}" class="{{ request()->routeIs('admin.paiements.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white block px-3 py-2 rounded-md text-base font-medium">Paiements</a>
                    <a href="{{ route('admin.absences.index') }}" class="{{ request()->routeIs('admin.absences.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white block px-3 py-2 rounded-md text-base font-medium">Absences</a>
                    <a href="{{ route('admin.salaires.index') }}" class="{{ request()->routeIs('admin.salaires.*') ? 'bg-indigo-700' : 'hover:bg-indigo-500' }} text-white block px-3 py-2 rounded-md text-base font-medium">Salaires</a>
                </div>
                <div class="pt-4 pb-3 border-t border-indigo-700">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-indigo-400 flex items-center justify-center text-white font-medium">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-indigo-300">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <a href="{{ route('admin.profile') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-200 hover:text-white hover:bg-indigo-500">Votre profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-indigo-200 hover:text-white hover:bg-indigo-500">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        @yield('header')
                    </h1>
                    <div class="flex space-x-4">
                        @yield('header-actions')
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="$el.parentElement.remove()">
                            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Fermer</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="$el.parentElement.remove()">
                            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Fermer</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="$el.parentElement.remove()">
                            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Fermer</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
