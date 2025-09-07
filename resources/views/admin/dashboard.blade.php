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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
        }

        .dark {
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
        }

        .min-h-content {
            min-height: calc(100vh - 4rem);
        }
        
        /* Ensure main content has proper spacing */
        @media (min-width: 768px) {
            .main-content {
                margin-left: 16rem; /* Same as sidebar width */
                width: calc(100% - 16rem);
                position: relative;
                z-index: 10;
            }
        }
        
        /* Enhanced Stat Cards */
        .stat-card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg border border-gray-100 dark:border-gray-700;
            height: 100%;
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
        
        .stat-card h3 {
            @apply text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center;
        }
        
        .stat-card .value {
            @apply text-2xl font-semibold text-gray-900 dark:text-white;
        }
        
        .stat-card .trend {
            @apply mt-2 text-sm flex items-center;
        }
        
        .stat-card .trend.up {
            @apply text-green-600 dark:text-green-400;
        }
        
        .stat-card .trend.down {
            @apply text-red-600 dark:text-red-400;
        }
        
        .stat-card .trend i {
            @apply mr-1 text-xs;
        }
        
        /* Chart Container */
        .chart-container {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700;
        }
        
        /* Table Styles */
        .table {
            @apply min-w-full divide-y divide-gray-200 dark:divide-gray-700;
        }
        
        .table thead th {
            @apply px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
        }
        
        .table tbody {
            @apply bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700;
        }
        
        .table tbody tr {
            @apply hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150;
        }
        
        .table tbody td {
            @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100;
        }
        
        /* Tooltip Styles */
        [data-tooltip] {
            @apply relative;
        }
        
        [data-tooltip]:hover::after {
            @apply absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 dark:bg-gray-700 rounded shadow-lg whitespace-nowrap;
            content: attr(data-tooltip);
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            margin-bottom: 5px;
        }
        
        /* Justification Badges */
        .justification-badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
        }
        
        .justified {
            @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300;
        }
        
        .unjustified {
            @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300;
        }
        
        .pending {
            @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300;
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
                            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY', 'Do MMMM YYYY') }}
                        </p>
                    </div>
                    
                    <div class="space-y-8">
                        <!-- Stats Overview -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <!-- Paiements Card -->
                            <div class="relative overflow-hidden transition-all duration-300 transform bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg group hover:shadow-xl hover:-translate-y-1 dark:from-primary-600 dark:to-primary-700">
                                <div class="absolute top-0 right-0 w-32 h-32 -mt-10 -mr-10 bg-white/10 rounded-full"></div>
                                <div class="relative p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-2">
                                            <h3 class="text-sm font-medium tracking-wide text-white/80">
                                                <i class="fas fa-credit-card mr-1"></i>
                                                Paiements du mois
                                            </h3>
                                            <div class="flex items-baseline space-x-2">
                                                <span class="text-2xl font-bold text-white">{{ number_format($stats['paiements_mois'] ?? 0, 0, ',', ' ') }}</span>
                                                <span class="text-sm font-medium text-white/80">DH</span>
                                            </div>
                                            <div class="flex items-center text-xs font-medium {{ isset($stats['paiements_trend']) && $stats['paiements_trend'] >= 0 ? 'text-green-200' : 'text-red-200' }}">
                                                <i class="fas fa-{{ isset($stats['paiements_trend']) && $stats['paiements_trend'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                                                {{ abs($stats['paiements_trend'] ?? 0) }}% vs mois dernier
                                            </div>
                                        </div>
                                        <div class="p-3 rounded-full bg-white/20 text-white">
                                            <i class="text-xl fas fa-credit-card"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-white/10">
                                        <a href="{{ route('admin.paiements.index') }}" class="flex items-center text-xs font-medium text-white/80 hover:text-white transition-colors">
                                            Voir les détails
                                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Élèves Card -->
                            <div class="relative overflow-hidden transition-all duration-300 transform bg-white rounded-xl shadow hover:shadow-md hover:-translate-y-1 dark:bg-gray-800">
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-2">
                                            <h3 class="text-sm font-medium tracking-wide text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-users mr-1"></i>
                                                Élèves inscrits
                                            </h3>
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['eleves_count'] ?? 0, 0, ',', ' ') }}</div>
                                            <div class="flex items-center text-xs font-medium {{ isset($stats['eleves_trend']) && $stats['eleves_trend'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                                <i class="fas fa-{{ isset($stats['eleves_trend']) && $stats['eleves_trend'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                                                {{ abs($stats['eleves_trend'] ?? 0) }}% vs mois dernier
                                            </div>
                                        </div>
                                        <div class="p-3 rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                            <i class="text-xl fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <a href="{{ route('admin.eleves.index') }}" class="flex items-center text-xs font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                                            Voir la liste
                                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Absences Card -->
                            <div class="relative overflow-hidden transition-all duration-300 transform bg-white rounded-xl shadow hover:shadow-md hover:-translate-y-1 dark:bg-gray-800">
                                <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-yellow-100 opacity-50 dark:from-yellow-900/20 dark:to-yellow-800/20"></div>
                                <div class="relative p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-2">
                                            <h3 class="text-sm font-medium tracking-wide text-yellow-700 dark:text-yellow-400">
                                                <i class="fas fa-calendar-times mr-1"></i>
                                                Absences ce mois
                                            </h3>
                                            <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-300">{{ number_format($stats['absences_mois'] ?? 0, 0, ',', ' ') }}</div>
                                            <div class="flex items-center text-xs font-medium {{ isset($stats['absences_trend']) && $stats['absences_trend'] >= 0 ? 'text-red-500' : 'text-green-500' }}">
                                                <i class="fas fa-{{ isset($stats['absences_trend']) && $stats['absences_trend'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                                                {{ abs($stats['absences_trend'] ?? 0) }}% vs mois dernier
                                            </div>
                                        </div>
                                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            <i class="text-xl fas fa-calendar-times"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-yellow-100 dark:border-yellow-800/50">
                                        <a href="{{ route('admin.absences.index') }}" class="flex items-center text-xs font-medium text-yellow-700 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors">
                                            Voir les absences
                                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Classes Card -->
                            <div class="relative overflow-hidden transition-all duration-300 transform bg-white rounded-xl shadow hover:shadow-md hover:-translate-y-1 dark:bg-gray-800">
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-2">
                                            <h3 class="text-sm font-medium tracking-wide text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                Classes actives
                                            </h3>
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['classes_count'] ?? 0 }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $stats['eleves_par_classe'] ?? 0 }} élèves/classe en moyenne
                                            </div>
                                        </div>
                                        <div class="p-3 rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                            <i class="text-xl fas fa-chalkboard-teacher"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <a href="{{ route('admin.classes.index') }}" class="flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 transition-colors">
                                            Voir les classes
                                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Welcome Card -->
                        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg dark:from-blue-700 dark:to-blue-800">
                            <!-- Decorative elements -->
                            <div class="absolute top-0 right-0 w-32 h-32 -mt-10 -mr-10 bg-white/10 rounded-full"></div>
                            <div class="absolute bottom-0 right-0 w-24 h-24 -mb-8 -mr-6 bg-white/5 rounded-full"></div>
                            
                            <div class="relative px-6 py-8 sm:px-8 sm:py-10">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                    <div class="max-w-2xl">
                                        <h2 class="text-2xl font-bold text-white">Bienvenue, {{ Auth::user()->name }}! </h2>
                                        <p class="mt-2 text-blue-100">Tableau de bord - {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
                                        
                                        <div class="mt-4 flex flex-wrap gap-3">
                                            <div class="flex items-center text-sm text-blue-100 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-full">
                                                <span class="w-2 h-2 bg-green-300 rounded-full mr-2"></span>
                                                {{ $stats['cours_aujourdhui'] ?? 0 }} cours aujourd'hui
                                            </div>
                                            @if(($stats['evenements_aujourdhui'] ?? 0) > 0)
                                            <div class="flex items-center text-sm text-blue-100 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-full">
                                                <i class="far fa-calendar-alt text-xs mr-1.5"></i>
                                                {{ $stats['evenements_aujourdhui'] }} événement{{ $stats['evenements_aujourdhui'] > 1 ? 's' : '' }}
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Quick stats -->
                                        <div class="mt-8 pt-6 border-t border-white/10">
                                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                                                <div class="text-center">
                                                    <p class="text-sm font-medium text-blue-100">Nouvelles absences</p>
                                                    <p class="mt-1 text-xl font-semibold text-white">{{ $stats['nouvelles_absences'] ?? 0 }}</p>
                                                </div>
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-blue-100">Messages non lus</p>
                                            <p class="mt-1 text-xl font-semibold text-white">{{ $stats['messages_non_lus'] ?? 0 }}</p>
                                        </div>
                                        <!-- Tâches en attente supprimé -->
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-blue-100">Alertes</p>
                                            <p class="mt-1 text-xl font-semibold text-white">{{ $stats['alertes'] ?? 0 }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-blue-100">Élèves actifs</p>
                                            <p class="mt-1 text-xl font-semibold text-white">{{ $stats['eleves_actifs'] ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Absences Chart -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                                <div class="p-6 pb-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Statistiques des absences</h2>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Évolution sur les 30 derniers jours</p>
                                        </div>
                                        <div class="mt-3 sm:mt-0">
                                            <div class="relative">
                                                <select class="appearance-none block w-full pl-3 pr-10 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 cursor-pointer">
                                                    <option>30 derniers jours</option>
                                                    <option>Ce mois-ci</option>
                                                    <option>Ce trimestre</option>
                                                    <option>Cette année</option>
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                                    <i class="fas fa-chevron-down text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative p-4 pt-0 h-80">
                                    <!-- Loading state -->
                                    <div id="chartLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-800/80 z-10 transition-opacity duration-300">
                                        <div class="text-center">
                                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary-500 border-t-transparent"></div>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Chargement des données...</p>
                                        </div>
                                    </div>
                                    <!-- Chart container -->
                                    <div id="absencesChart" class="w-full h-full transition-opacity duration-300 opacity-0"></div>
                                    <!-- No data state -->
                                    <div id="chartNoData" class="hidden absolute inset-0 flex items-center justify-center">
                                        <div class="text-center p-6 max-w-xs">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 mb-3">
                                                <i class="fas fa-chart-line text-xl"></i>
                                            </div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Aucune donnée disponible</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Les données d'absence apparaîtront ici dès qu'elles seront disponibles.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Lien d'export supprimé -->
                                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                                </div>
                            </div>

                            <!-- Recent Absences -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                                <div class="p-6 pb-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Dernières absences</h2>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Les absences récemment enregistrées</p>
                                        </div>
                                        <a href="{{ route('admin.absences.index') }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                                            Voir tout
                                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Élève</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                        </thead>
                                        <tbody>
                                            @forelse($recentAbsences as $absence)
                                            <tr>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img class="h-10 w-10 rounded-full" src="{{ $absence->eleve->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($absence->eleve->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $absence->eleve->name }}">
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="font-medium text-gray-900 dark:text-white">{{ $absence->eleve->name }}</div>
                                                            <div class="text-gray-500 dark:text-gray-400">{{ $absence->eleve->classe->code ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($absence->justified)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Justifiée
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                                            Non justifiée
                                                        </span>
                                                    @endif
                                                    @if($absence->retard)
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Retard
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end space-x-2">
                                                        @if(!$absence->justified)
                                                        <button type="button" class="inline-flex items-center p-1.5 border border-transparent rounded-full text-green-600 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" 
                                                                data-tooltip="Justifier l'absence"
                                                                data-absence-id="{{ $absence->id }}">
                                                            <i class="fas fa-check h-3 w-3"></i>
                                                        </button>
                                                        @endif
                                                        <a href="{{ route('admin.absences.edit', $absence) }}" 
                                                           class="inline-flex items-center p-1.5 border border-transparent rounded-full text-primary-600 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:text-primary-400 dark:hover:bg-primary-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                                                           data-tooltip="Voir les détails">
                                                            <i class="fas fa-chevron-right h-3 w-3"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-12 text-center">
                                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full mb-3">
                                                            <i class="fas fa-check-circle text-2xl"></i>
                                                        </div>
                                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Aucune absence récente</h4>
                                                        <p class="text-xs max-w-xs">Tous les élèves sont présents. Aucune absence n'a été enregistrée récemment.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
    <!-- Load ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <!-- Load Tippy.js with Popper.js -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips if Tippy is available
            if (typeof tippy !== 'undefined') {
                tippy('[data-tooltip]', {
                    content: (reference) => reference.getAttribute('data-tooltip'),
                    placement: 'top',
                    animation: 'fade',
                    theme: 'light',
                    arrow: true,
                    delay: [100, 200],
                    duration: [200, 150],
                    interactive: true,
                    appendTo: () => document.body
                });
            }
            
            // Initialize charts with loading state
            initAbsencesChart();
            
            // Dark mode toggle
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                // Set initial state from localStorage
                if (localStorage.getItem('darkMode') === 'true' || 
                    (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                    darkModeToggle.checked = true;
                }
                
                darkModeToggle.addEventListener('change', function() {
                    document.documentElement.classList.toggle('dark', this.checked);
                    localStorage.setItem('darkMode', this.checked);
                    updateChartTheme();
                });
            }

            // Handle justify absence buttons
            document.querySelectorAll('[data-absence-id]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const absenceId = this.getAttribute('data-absence-id');
                    justifyAbsence(absenceId, this);
                });
            });
        });

        function justifyAbsence(absenceId, button) {
            // Show loading state
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin h-3 w-3"></i>';
            button.disabled = true;
            
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Make API call to justify absence
            fetch(`/admin/absences/${absenceId}/justify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    justified: true
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the UI to reflect the justified absence
                    updateAbsenceUI(absenceId, true);
                    // Show success message
                    showNotification('Absence justifiée avec succès', 'success');
                } else {
                    throw new Error(data.message || 'Erreur lors de la justification de l\'absence');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message
                showNotification(error.message || 'Une erreur est survenue', 'error');
                // Reset button state
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
        }
        
        /**
         * Update the UI after an absence is justified
         * @param {string} absenceId - The ID of the absence
         * @param {boolean} isJustified - Whether the absence is justified
         */
        function updateAbsenceUI(absenceId, isJustified) {
            // Find the absence row
            const absenceRow = document.querySelector(`[data-absence-id="${absenceId}"]`).closest('tr');
            
            if (absenceRow) {
                // Update status cell
                const statusCell = absenceRow.querySelector('.status-cell') || absenceRow.querySelector('td:nth-child(3)');
                if (statusCell) {
                    statusCell.innerHTML = isJustified ? 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Justifiée</span>' :
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Non justifiée</span>';
                }
                
                // Update action buttons
                const actionButtons = absenceRow.querySelectorAll('button');
                actionButtons.forEach(button => {
                    if (button.getAttribute('data-tooltip') === 'Justifier l\'absence') {
                        // Remove the justify button
                        button.remove();
                    }
                });
                
                // Add a checkmark icon to confirm justification
                const actionCell = absenceRow.querySelector('td:last-child');
                if (actionCell) {
                    // Remove any existing checkmark
                    const existingCheck = actionCell.querySelector('.justification-checkmark');
                    if (!existingCheck) {
                        actionCell.innerHTML += `
                            <span class="ml-2 text-green-500 justification-checkmark">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        `;
                    }
                }
                
                // Remove the justify button
                const justifyButton = absenceRow.querySelector(`[data-absence-id="${absenceId}"]`);
                if (justifyButton) {
                    justifyButton.remove();
                }
            }
        }
        
        /**
         * Show a notification message
         * @param {string} message - The message to display
         * @param {string} type - The type of notification (success, error, warning, info)
         */
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const types = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${types[type] || 'bg-gray-800'} transform transition-all duration-300 translate-x-0 opacity-100`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(120%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

        function initAbsencesChart() {
            const chartElement = document.getElementById('absencesChart');
            if (!chartElement) return;
            
            // Show loading state
            const loadingElement = document.getElementById('chartLoading');
            const noDataElement = document.getElementById('chartNoData');
            
            // Simulate data loading (replace with actual data fetching)
            setTimeout(() => {
                // Sample data - replace with actual data from your backend
                const sampleData = [28, 29, 33, 36, 32, 32, 33, 31, 39, 36, 29, 26, 30, 30, 33];
                
                // Check if we have data
                const hasData = sampleData && sampleData.length > 0 && sampleData.some(val => val > 0);
                
                if (!hasData) {
                    if (loadingElement) loadingElement.style.display = 'none';
                    if (noDataElement) noDataElement.classList.remove('hidden');
                    return;
                }
                
                const options = {
                    series: [{
                        name: 'Absences',
                        data: sampleData
                    }],
                    chart: {
                        id: 'absencesChart',
                        height: '100%',
                        type: 'area',
                        fontFamily: 'Inter, sans-serif',
                        foreColor: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false
                            }
                        },
                        zoom: { enabled: false },
                        animations: { enabled: true, easing: 'easeinout', speed: 800 },
                        events: {
                            mounted: function(chartContext, config) {
                                // Hide loading and show chart with fade in
                                if (loadingElement) loadingElement.style.opacity = '0';
                                setTimeout(() => {
                                    if (loadingElement) loadingElement.style.display = 'none';
                                    chartElement.style.opacity = '1';
                                }, 300);
                            }
                        }
                    },
                    colors: ['#4F46E5'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    grid: {
                        borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
                        strokeDashArray: 4,
                        padding: { top: 0, right: 0, bottom: 0, left: 10 }
                    },
                    markers: {
                        size: 5,
                        strokeWidth: 2,
                        hover: { size: 6 }
                    },
                    xaxis: {
                        categories: Array.from({length: 15}, (_, i) => (i + 1).toString()),
                        labels: {
                            style: {
                                colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                                fontSize: '12px',
                                fontFamily: 'Inter, sans-serif'
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        tooltip: { enabled: false }
                    },
                    yaxis: {
                        min: 0,
                        max: 50,
                        tickAmount: 5,
                        labels: {
                            style: {
                                colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                                fontSize: '12px',
                                fontFamily: 'Inter, sans-serif'
                            },
                            formatter: function(val) {
                                return Math.round(val);
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Inter, sans-serif'
                        },
                        x: {
                            show: true,
                            formatter: function(val) {
                                return `Jour ${val} - ${new Date(2023, 8, parseInt(val)).toLocaleDateString('fr-FR', { weekday: 'long' })}`;
                            }
                        },
                        y: {
                            formatter: function(val) {
                                return `${val} ${val > 1 ? 'absences' : 'absence'}`;
                            }
                        },
                        marker: {
                            show: true
                        }
                    },
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 4,
                            offsetX: -3,
                            offsetY: 1
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 0
                        }
                    },
                    responsive: [{
                        breakpoint: 640,
                        options: {
                            chart: {
                                toolbar: {
                                    show: false
                                }
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'left'
                            }
                        }
                    }]
                };

                const chart = new ApexCharts(chartElement, options);
                chart.render();
                
                // Store chart instance for theme updates
                window.absencesChart = chart;
                
            }, 1200); // Simulate loading delay
        }

        function updateChartTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const options = {
                chart: {
                    foreColor: isDark ? '#9CA3AF' : '#6B7280'
                },
                grid: {
                    borderColor: isDark ? '#374151' : '#E5E7EB'
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: isDark ? '#9CA3AF' : '#6B7280'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: isDark ? '#9CA3AF' : '#6B7280'
                        }
                    }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light'
                }
            };
            
            if (window.absencesChart) {
                window.absencesChart.updateOptions(options);
            }
        }
    </script>
@endpush
