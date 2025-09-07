@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div x-data="{ sidebarOpen: false, darkMode: false, toggleDarkMode() { this.darkMode = !this.darkMode; localStorage.setItem('darkMode', this.darkMode); document.documentElement.classList.toggle('dark', this.darkMode) } }" x-init="() => { darkMode = localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches); document.documentElement.classList.toggle('dark', darkMode) }">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content flex flex-col flex-1 min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Top Navigation -->
        @include('admin.partials.top-navigation')
        
        <!-- Page Content -->
        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    <!-- Page Header -->
                    <div class="md:flex md:items-center md:justify-between mb-6">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                                Mon Profil
                            </h2>
                        </div>
                        <div class="mt-4 flex md:mt-0 md:ml-4">
                            <a href="{{ route('profile.edit') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Modifier le profil
                            </a>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img class="h-20 w-20 rounded-full object-cover" 
                                         src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('img/default-avatar.png') }}" 
                                         alt="{{ $user->name }}">
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->name }}</h3>
                                    <p class="text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->roles->first() ? ucfirst($user->roles->first()->name) : 'Aucun rôle' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom complet</h4>
                                    <p class="text-gray-900 dark:text-white">{{ $user->name }}</p>
                                </div>
                                
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h4>
                                    <p class="text-gray-900 dark:text-white">{{ $user->email }}</p>
                                </div>
                                
                                @if($user->telephone)
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</h4>
                                    <p class="text-gray-900 dark:text-white">{{ $user->telephone }}</p>
                                </div>
                                @endif
                                
                                @if($user->adresse)
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Adresse</h4>
                                    <p class="text-gray-900 dark:text-white">{{ $user->adresse }}</p>
                                </div>
                                @endif
                        
                        @if($user->date_naissance)
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de naissance</h4>
                            <p class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($user->date_naissance)->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Compte créé le</h4>
                            <p class="text-gray-900 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                            <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            {{ __('Modifier le profil') }}
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
