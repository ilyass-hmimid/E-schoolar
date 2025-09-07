@extends('layouts.app')

@section('title', 'Tableau de bord')

@push('styles')
<style>
    .stat-card {
        @apply bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-all duration-300 hover:shadow-lg;
    }
    .stat-card.primary {
        @apply border-l-4 border-primary-500;
    }
    .stat-card.success {
        @apply border-l-4 border-green-500;
    }
    .stat-card.warning {
        @apply border-l-4 border-yellow-500;
    }
    .stat-card.danger {
        @apply border-l-4 border-red-500;
    }
</style>
@endpush

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
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Aperçu des activités et statistiques
                        </p>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                        <!-- Total des élèves -->
                        <div class="stat-card primary">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des élèves</h3>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_eleves'], 0, ',', ' ') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Élèves actifs -->
                        <div class="stat-card success">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Élèves actifs</h3>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['eleves_actifs'], 0, ',', ' ') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nouveaux élèves (30 derniers jours) -->
                        <div class="stat-card warning">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nouveaux (30j)</h3>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['nouveaux_eleves'], 0, ',', ' ') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Taux d'absences -->
                        <div class="stat-card danger">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Taux d'absences</h3>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['taux_absences'], 2, ',', ' ') }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Derniers élèves inscrits -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Derniers élèves inscrits</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date d'inscription</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($recentStudents as $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="{{ $student->avatar ? asset('storage/' . $student->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $student->name }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->cne ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $student->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $student->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($student->status === 'actif')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Actif
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Inactif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.eleves.edit', $student) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">Voir</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Aucun élève trouvé
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
