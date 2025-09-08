@extends('layouts.app')

@section('title', 'Tableau de bord')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">
    <style>
        /* Cartes de statistiques */
        .stat-card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg border border-gray-100 dark:border-gray-700;
            height: 100%;
        }
        .stat-card.primary { @apply border-l-4 border-primary-500; }
        .stat-card.success { @apply border-l-4 border-green-500; }
        .stat-card.warning { @apply border-l-4 border-yellow-500; }
        .stat-card.danger { @apply border-l-4 border-red-500; }
        
        /* Badges d'état */
        .status-badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
        }
        .justified { @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300; }
        .unjustified { @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300; }
        .pending { @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300; }
        
        /* Mise en page principale */
        .main-content {
            @apply min-h-screen bg-gray-50 dark:bg-gray-900 transition-all duration-300;
            padding: 1.5rem 0;
            width: 100%;
            margin-left: 0;
            overflow-x: hidden;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        
        /* Styles responsifs */
        @media (min-width: 768px) {
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
                position: relative;
                z-index: 1;
                padding: 1.5rem 2rem;
                max-width: calc(100vw - 16rem);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            .main-content > main {
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            
            .main-content > main > div {
                flex: 1;
                display: flex;
                flex-direction: column;
            }
        }
        
        /* Ensure content takes full width on mobile */
        @media (max-width: 767px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.25rem;
            }
        }
        
        /* Fix pour les éléments de grille */
        .grid {
            margin-right: -0.5rem;
            margin-left: -0.5rem;
            width: calc(100% + 1rem);
        }
        
        .grid > * {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            box-sizing: border-box;
            min-width: 0; /* Empêche le débordement des éléments flexibles */
        }
        
        /* Styles pour les tableaux */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }
        
        /* Ajustement pour les cartes */
        .stat-card {
            min-width: 0; /* Empêche le débordement des cartes */
            width: 100%;
            overflow: hidden;
        }
        
        /* Correction pour les icônes dans les cartes */
        .stat-card svg {
            flex-shrink: 0;
        }
        
        /* Ajustement pour les graphiques */
        .chart-container {
            position: relative;
            width: 100%;
            min-height: 300px;
        }
        
        /* Correction pour les sélecteurs */
        select {
            max-width: 100%;
            min-width: 0;
            width: 100%;
        }
        
        /* Ajustement pour les conteneurs de sélecteurs */
        .relative {
            min-width: 0;
        }
        
        /* Tooltips personnalisés */
        [data-tooltip] {
            position: relative;
            cursor: pointer;
        }
        
        [data-tooltip]:hover::after {
            @apply absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 dark:bg-gray-700 rounded shadow-lg whitespace-nowrap;
            content: attr(data-tooltip);
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            margin-bottom: 5px;
        }
        
        /* Styles pour les graphiques */
        .chart-container {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md;
        }
    </style>
@endpush

@section('content')
<div class="flex flex-col min-h-screen">
    <!-- Top Navigation -->
    @include('admin.partials.top-navigation')
    
    <!-- Main Content -->
    <div class="main-content">
        <main class="flex-1 w-full overflow-x-hidden">
            <div class="py-2">
                <div class="w-full px-4 mx-auto max-w-7xl">
                    <!-- En-tête de page -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </p>
                    </div>
                    
                    <!-- Contenu principal -->
                    <div class="space-y-6">
                        <!-- Section des statistiques -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 w-full overflow-hidden">
                            <!-- Carte des paiements -->
                            <div class="stat-card primary overflow-hidden">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 p-3 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Paiements du mois</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white truncate">{{ number_format($stats['paiements_du_mois'] ?? 0, 0, ',', ' ') }} DH</p>
                                        <p class="text-xs text-green-600 dark:text-green-400 truncate">+12% par rapport au mois dernier</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte des élèves -->
                            <div class="stat-card success">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des élèves</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_eleves'] ?? 0 }}</p>
                                        <p class="text-xs text-green-600 dark:text-green-400">+5% cette année</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte des absences -->
                            <div class="stat-card warning">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Absences ce mois</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $recentAbsences->count() ?? 0 }}</p>
                                        <p class="text-xs text-yellow-600 dark:text-yellow-400">Taux: {{ number_format($stats['taux_absences'] ?? 0, 2) }}%</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte des professeurs -->
                            <div class="stat-card danger">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Professeurs</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_professeurs'] ?? 0 }}</p>
                                        <p class="text-xs text-green-600 dark:text-green-400">{{ $stats['total_matieres'] ?? 0 }} matières</p>
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
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
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
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md h-full flex flex-col">
                                <div class="p-6 pb-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">Dernières absences</h2>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Les absences récemment enregistrées</p>
                                        </div>
                                        <a href="{{ route('admin.absences.index') }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                                            Voir tout
                                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="overflow-hidden flex-1 flex flex-col">
                                    <div class="overflow-x-auto flex-1">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Élève</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                </tr>
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
                                                        <div class="text-sm text-gray-900 dark:text-white">{{ $absence->date->format('d/m/Y') }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $absence->seance->name ?? 'Toute la journée' }}</div>
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
            // Initialiser le graphique des absences
            initAbsencesChart();
            
            // Mettre à jour le thème du graphique lors du basculement du mode sombre
            const darkModeToggle = document.getElementById('theme-toggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    setTimeout(updateChartTheme, 100);
                });
            }
        });

        // Fonction pour justifier une absence
        function justifyAbsence(absenceId) {
            if (confirm('Êtes-vous sûr de vouloir justifier cette absence ?')) {
                fetch(`/admin/absences/${absenceId}/justify`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateAbsenceUI(absenceId, true);
                        // Afficher une notification de succès
                        showNotification('Absence justifiée avec succès', 'success');
                    } else {
                        showNotification('Une erreur est survenue lors de la justification de l\'absence.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Une erreur est survenue lors de la communication avec le serveur.', 'error');
                });
            }
        }

        // Mettre à jour l'interface utilisateur après justification d'une absence
        function updateAbsenceUI(absenceId, isJustified) {
            const absenceRow = document.querySelector(`[data-absence-id="${absenceId}"]`).closest('tr');
            if (absenceRow) {
                const badge = absenceRow.querySelector('.status-badge');
                if (badge) {
                    badge.className = 'status-badge justified';
                    badge.textContent = 'Justifiée';
                }
                const button = absenceRow.querySelector(`[data-absence-id="${absenceId}"]`);
                if (button) button.remove();
            }
        }

        // Afficher une notification
        function showNotification(message, type = 'success') {
            // Utilisez votre système de notification préféré ici
            // Par exemple, avec Toastr, SweetAlert2, ou une notification personnalisée
            alert(message); // Solution temporaire
        }

        // Initialiser le graphique des absences
        function initAbsencesChart() {
            const chartElement = document.getElementById('absencesChart');
            if (!chartElement) return;
            
            // Données simulées - à remplacer par des données réelles de votre backend
            const months = [
                'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin',
                'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'
            ];
            
            // Générer des données aléatoires pour la démo
            const currentMonth = new Date().getMonth();
            const currentYear = new Date().getFullYear();
            
            // Générer les 12 derniers mois
            const last12Months = [];
            const monthData = [];
            
            for (let i = 11; i >= 0; i--) {
                const date = new Date(currentYear, currentMonth - i, 1);
                last12Months.push(months[date.getMonth()] + ' ' + date.getFullYear());
                
                // Générer un nombre aléatoire d'absences entre 10 et 50
                monthData.push(Math.floor(Math.random() * 41) + 10);
            }
            
            // Options du graphique
            const options = {
                series: [{
                    name: 'Absences',
                    data: monthData
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    },
                    foreColor: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                },
                colors: ['#4F46E5'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
                    strokeDashArray: 3,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                xaxis: {
                    categories: last12Months,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                            fontSize: '12px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 400
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                            fontSize: '12px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 400
                        },
                        formatter: function(value) {
                            return Math.floor(value);
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
                        format: 'MMM yyyy'
                    },
                    y: {
                        formatter: function(value) {
                            return value + ' absences';
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#4F46E5'],
                        inverseColors: false,
                        opacityFrom: 0.7,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                markers: {
                    size: 4,
                    strokeWidth: 2,
                    strokeColors: ['#4F46E5'],
                    colors: ['#FFFFFF'],
                    hover: {
                        size: 5
                    }
                }
            };
            
            // Créer le graphique
            const chart = new ApexCharts(chartElement, options);
            chart.render();
            
            // Stocker la référence du graphique pour une utilisation ultérieure
            window.absencesChart = chart;
        }

        // Mettre à jour le thème du graphique
        function updateChartTheme() {
            if (window.absencesChart) {
                const isDark = document.documentElement.classList.contains('dark');
                window.absencesChart.updateOptions({
                    chart: {
                        foreColor: isDark ? '#9CA3AF' : '#6B7280'
                    },
                    grid: {
                        borderColor: isDark ? '#374151' : '#E5E7EB'
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
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
                    }
                });
            }
        }
    </script>
@endpush
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
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
