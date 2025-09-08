@extends('layouts.app')

@section('title', 'Tableau de bord - Administration')

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
    @stack('admin-styles')
@endpush

@section('content')
<div x-data="{ sidebarOpen: window.innerWidth >= 768, open: {{ request()->routeIs('admin.eleves.*') ? 'true' : 'false' }}, openProfs: {{ request()->routeIs('admin.professeurs.*') ? 'true' : 'false' }} }" class="flex h-screen bg-gray-50 overflow-hidden">
    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden" x-cloak></div>
    
    <!-- Sidebar -->
    <div class="hidden md:flex md:flex-shrink-0 md:fixed md:top-0 md:left-0 md:bottom-0">
        <div class="flex flex-col w-64 bg-white border-r border-gray-200 h-full">
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4">
                    <span class="text-xl font-bold text-gray-900">
                        Administration
                    </span>
                </div>
                <div class="mt-5 flex-1 flex flex-col">
                    <nav class="flex-1 px-2 space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-tachometer-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                            Tableau de bord
                        </a>

                        <!-- Gestion des élèves -->
                        <div>
                            <button type="button" class="group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.eleves.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" @click="open = !open">
                                <i class="fas fa-user-graduate mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                Gestion des élèves
                                <svg :class="{'rotate-90': open, 'text-gray-400': !open}" class="ml-auto h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150" viewBox="0 0 20 20">
                                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor" />
                                </svg>
                            </button>
                            <div x-show="open" class="mt-1 space-y-1 pl-11">
                                <a href="{{ route('admin.eleves.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.eleves.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Liste des élèves
                                </a>
                                <a href="{{ route('admin.eleves.create') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.eleves.create') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Ajouter un élève
                                </a>
                            </div>
                        </div>

                        <!-- Gestion des professeurs -->
                        <div>
                            <button type="button" class="group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.professeurs.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" @click="openProfs = !openProfs">
                                <i class="fas fa-chalkboard-teacher mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                Gestion des professeurs
                                <svg :class="{'rotate-90': openProfs, 'text-gray-400': !openProfs}" class="ml-auto h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150" viewBox="0 0 20 20">
                                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor" />
                                </svg>
                            </button>
                            <div x-show="openProfs" class="mt-1 space-y-1 pl-11">
                                <a href="{{ route('admin.professeurs.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.professeurs.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Liste des professeurs
                                </a>
                                <a href="{{ route('admin.professeurs.create') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.professeurs.create') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Ajouter un professeur
                                </a>
                            </div>
                        </div>

                        <!-- Gestion des matières -->
                        <a href="{{ route('admin.matieres.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.matieres.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-book mr-3 text-gray-400 group-hover:text-gray-500"></i>
                            Gestion des matières
                        </a>

                        <!-- Gestion des absences -->
                        <a href="{{ route('admin.absences.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.absences.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-calendar-times mr-3 text-gray-400 group-hover:text-gray-500"></i>
                            Gestion des absences
                        </a>

                        <!-- Gestion des paiements -->
                        <a href="{{ route('admin.paiements.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.paiements.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-credit-card mr-3 text-gray-400 group-hover:text-gray-500"></i>
                            Gestion des paiements
                        </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden md:ml-64">
        <!-- Top navigation -->
        <div class="bg-white shadow-sm z-10">
        <main class="flex-1 overflow-y-auto focus:outline-none pt-16 px-4 sm:px-6 md:px-8 py-4">
            <div class="mx-auto max-w-7xl">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <i class="fas fa-bars h-6 w-6"></i>
                        </button>
                        
                        <!-- Page title -->
                        <div class="ml-4">
                            <h1 class="text-xl font-semibold text-gray-900">
                                @yield('page-title', 'Tableau de bord')
                            </h1>
                        </div>
                    </div>
                    
                    <!-- Right side items -->
                    <div class="flex items-center
                    
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-green-800 dark:text-green-200">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" class="text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-red-800 dark:text-red-200">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center text-red-800 dark:text-red-200">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Erreur !</strong>
                            </div>
                            <button type="button" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <ul class="mt-2 ml-6 list-disc text-red-700 dark:text-red-300">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    @push('scripts')
        <script>
            // Mobile menu toggle
            document.addEventListener('alpine:init', () => {
                Alpine.data('adminLayout', () => ({
                    sidebarOpen: window.innerWidth >= 768,
                    
                    init() {
                        this.$watch('sidebarOpen', value => {
                            if (value) {
                                document.body.classList.add('overflow-hidden', 'md:overflow-auto');
                            } else {
                                document.body.classList.remove('overflow-hidden', 'md:overflow-auto');
                            }
                        });
                    },
                    
                    closeOnEscape(e) {
                        if (e.key === 'Escape') {
                            this.sidebarOpen = false;
                        }
                    },
                    
                    closeOnClickOutside(e) {
                        if (window.innerWidth < 768 && this.sidebarOpen && !e.target.closest('#sidebar')) {
                            this.sidebarOpen = false;
                        }
                    }
                }));
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    document.body.classList.remove('overflow-hidden');
                }
            });
        </script>
        
        @livewireScripts
        @stack('admin-scripts')
    @endpush
@endsection
