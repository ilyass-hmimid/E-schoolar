@extends('layouts.app')

@section('title', 'Paramètres')

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
                                Paramètres
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Gérez les paramètres de votre compte et de l'application
                            </p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Préférences</h3>
                            <div class="mt-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Mode sombre</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Activez ou désactivez le mode sombre</p>
                                    </div>
                                    <button @click="toggleDarkMode" type="button" x-text="darkMode ? 'Désactiver' : 'Activer'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" :class="{'bg-primary-600': darkMode, 'bg-gray-200': !darkMode}" role="switch" :aria-checked="darkMode">
                                        <span class="sr-only">Activer le mode sombre</span>
                                        <span aria-hidden="true" :class="{'translate-x-5': darkMode, 'translate-x-0': !darkMode}" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
