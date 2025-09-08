@extends('layouts.admin')

@section('title', 'Tableau de bord')

@push('styles')
<style>
    /* Cartes de statistiques */
    .stat-card {
        @apply bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 transition-all duration-200 hover:shadow-md border border-gray-100 dark:border-gray-700;
        height: 100%;
    }
    .stat-card.primary { @apply border-l-4 border-blue-500; }
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
    
    /* Amélioration de la mise en page générale */
    .main-content {
        @apply bg-gray-50 dark:bg-gray-900 transition-colors duration-200;
        min-height: calc(100vh - 4rem);
        padding: 1rem;
        margin-top: 4rem;
        width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    
    /* Ajustements pour les grands écrans */
    @media (min-width: 768px) {
        .main-content {
            padding: 1.5rem;
            margin-left: 16rem;
            width: calc(100% - 16rem);
        }
    }
    
    /* Optimisation mobile */
    @media (max-width: 767px) {
        .main-content {
            padding: 1rem;
            margin-top: 3.5rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
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
        min-height: 300px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="w-full mx-auto max-w-7xl">
        <!-- En-tête de page -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Vue d'ensemble des activités et statistiques
            </p>
        </div>
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
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 w-full mb-6">
                            <!-- Carte des paiements -->
                            <div class="stat-card primary">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Paiements du mois</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white truncate">{{ number_format($paiementsStats->total_mois ?? 0, 0, ',', ' ') }} DH</p>
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
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Élèves inscrits</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats->total_eleves ?? 0 }}</p>
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
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ is_countable($recentAbsences) ? $recentAbsences->count() : 0 }}</p>
                                        <p class="text-xs text-yellow-600 dark:text-yellow-400">Taux: {{ number_format($stats->taux_absences ?? 0, 2) }}%</p>
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
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats->total_professeurs ?? 0 }}</p>
                                        <p class="text-xs text-red-600 dark:text-red-400">-2% cette année</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Welcome Card -->
                        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg dark:from-blue-700 dark:to-blue-800">
                            <div class="absolute top-0 right-0 w-32 h-32 -mt-6 -mr-6 bg-blue-500 rounded-full opacity-20"></div>
                            <div class="absolute bottom-0 right-0 w-64 h-64 -mb-12 -mr-12 bg-blue-500 rounded-full opacity-20"></div>
                            <div class="relative px-8 py-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <img class="w-16 h-16 rounded-full border-4 border-white border-opacity-25" src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=EBF4FF&color=7F9CF5' }}" alt="{{ auth()->user()->name }}">
                                    </div>
                                    <div class="ml-6
                                        <h2 class="text-2xl font-bold text-white">Bienvenue, {{ auth()->user()->name }}!</h2>
                                        <p class="mt-1 text-blue-100">Voici un aperçu de votre activité aujourd'hui.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Graphiques et tableaux -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                            <!-- Graphique des absences -->
                            <div class="chart-container">
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Statistiques des absences</h2>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Évolution sur les 30 derniers jours</p>
                                        </div>
                                        <div class="relative">
                                            <select class="appearance-none block w-full pl-3 pr-10 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 cursor-pointer">
                                                <option>30 derniers jours</option>
                                                <option>Ce mois-ci</option>
                                                <option>Ce trimestre</option>
                                                <option>Cette année</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <div id="absencesChart" class="w-full h-64"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dernières absences -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md h-full flex flex-col">
                                <div class="p-6 pb-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">Dernières absences</h2>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Les absences récemment enregistrées</p>
                                        </div>
                                        <a href="{{ route('admin.absences.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
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
                                                @php
                                                    // Vérifier si l'absence a des données d'élève valides
                                                    $eleveName = $absence->eleve->name ?? 'Élève inconnu';
                                                    $classeCode = $absence->eleve->classe->code ?? 'N/A';
                                                    $dateAbsence = isset($absence->date) ? $absence->date->format('d/m/Y') : 'Date inconnue';
                                                    $seanceName = $absence->seance->name ?? 'Toute la journée';
                                                @endphp
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                <img class="h-10 w-10 rounded-full" src="{{ $absence->eleve->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($eleveName).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $eleveName }}">
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $eleveName }}</div>
                                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $classeCode }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900 dark:text-white">{{ $dateAbsence }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $seanceName }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($absence->justified)
                                                            <span class="status-badge justified">
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                                Justifiée
                                                            </span>
                                                        @else
                                                            <span class="status-badge unjustified">
                                                                <i class="fas fa-times-circle mr-1"></i>
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
                                                               class="inline-flex items-center p-1.5 border border-transparent rounded-full text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
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

@push('scripts')
    <!-- Load ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <!-- Load Tippy.js with Popper.js -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser les tooltips
            if (typeof tippy !== 'undefined') {
                tippy('[data-tooltip]', {
                    content: (reference) => reference.getAttribute('data-tooltip'),
                    animation: 'shift-away',
                    theme: 'material',
                    placement: 'top',
                    arrow: true,
                    delay: [100, 0],
                    duration: [200, 150],
                    interactive: true,
                    appendTo: document.body
                });
            }

            // Initialiser le graphique des absences
            initAbsencesChart();
            
            // Gérer le changement de période
            const periodSelect = document.querySelector('select[aria-label="Sélectionner une période"]');
            if (periodSelect) {
                periodSelect.addEventListener('change', function() {
                    // Ici, vous pouvez ajouter la logique pour charger les données en fonction de la période sélectionnée
                    console.log('Période sélectionnée :', this.value);
                    // Par exemple : chargerDonneesGraphique(this.value);
                });
            }
            
            // Gérer le clic sur le bouton de justification d'absence
            document.querySelectorAll('[data-absence-id]').forEach(button => {
                button.addEventListener('click', function() {
                    const absenceId = this.getAttribute('data-absence-id');
                    justifierAbsence(absenceId, this);
                });
            });
        });
        
        // Fonction pour initialiser le graphique des absences
        function initAbsencesChart() {
            const chartElement = document.getElementById('absencesChart');
            if (!chartElement) return;
            
            // Données simulées - à remplacer par des données réelles de votre backend
            const currentDate = new Date();
            const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
            const monthData = [];
            
            // Générer des données pour les 6 derniers mois
            for (let i = 5; i >= 0; i--) {
                const date = new Date();
                date.setMonth(currentDate.getMonth() - i);
                monthData.push({
                    x: monthNames[date.getMonth()],
                    y: Math.floor(Math.random() * 50) + 10 // Valeurs aléatoires pour la démo
                });
            }
            
            const options = {
                series: [{
                    name: 'Absences',
                    data: monthData
                }],
                chart: {
                    type: 'area',
                    height: '100%',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    },
                    foreColor: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                    background: 'transparent'
                },
                colors: ['#3B82F6'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    lineCap: 'round'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    type: 'category',
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
                            fontWeight: 500
                        }
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                            fontSize: '12px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 500
                        },
                        formatter: function(value) {
                            return Math.round(value);
                        }
                    },
                    min: 0,
                    max: 60,
                    tickAmount: 4
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
                        format: 'MMM',
                        formatter: function(seriesName, opts) {
                            return monthNames[new Date().getMonth() - (5 - opts.dataPointIndex)];
                        }
                    },
                    y: {
                        title: {
                            formatter: function() {
                                return 'Absences';
                            }
                        },
                        formatter: function(value) {
                            return value + ' absences';
                        }
                    },
                    marker: {
                        show: true
                    },
                    fixed: {
                        enabled: true,
                        position: 'topRight',
                        offsetX: 0,
                        offsetY: 0
                    }
                },
                grid: {
                    show: true,
                    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
                    strokeDashArray: 4,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                    padding: {
                        top: 0,
                        right: 10,
                        bottom: 0,
                        left: 10
                    }
                },
                responsive: [
                    {
                        breakpoint: 1024,
                        options: {
                            chart: {
                                height: 250
                            },
                            yaxis: {
                                labels: {
                                    show: true
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 640,
                        options: {
                            chart: {
                                height: 200
                            },
                            yaxis: {
                                labels: {
                                    show: false
                                }
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                ]
            };
            
            // Créer le graphique
            const chart = new ApexCharts(chartElement, options);
            chart.render();
            
            // Stocker la référence du graphique pour une utilisation ultérieure
            window.absencesChart = chart;
            
            // Mettre à jour le thème du graphique lors du changement de thème
            const observer = new MutationObserver(updateChartTheme);
            observer.observe(document.documentElement, { 
                attributes: true, 
                attributeFilter: ['class'] 
            });
        }
        
        // Fonction pour mettre à jour le thème du graphique
        function updateChartTheme() {
            if (window.absencesChart) {
                const isDark = document.documentElement.classList.contains('dark');
                const options = {
                    theme: {
                        mode: isDark ? 'dark' : 'light'
                    },
                    grid: {
                        borderColor: isDark ? '#374151' : '#E5E7EB'
                    },
                    xaxis: {
                        labels: {
                            style: {
                                colors: isDark ? '#9CA3AF' : '#6B7280'
                            }
                        },
                        axisBorder: {
                            color: isDark ? '#4B5563' : '#E5E7EB'
                        },
                        axisTicks: {
                            color: isDark ? '#4B5563' : '#E5E7EB'
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
                window.absencesChart.updateOptions(options);
            }
        }
        
        // Fonction pour justifier une absence
        function justifierAbsence(absenceId, button) {
            // Ici, vous pouvez ajouter la logique pour justifier une absence via une requête AJAX
            console.log('Justification de l\'absence :', absenceId);
            
            // Exemple de mise à jour de l'interface utilisateur
            if (button) {
                const row = button.closest('tr');
                const statusCell = row.querySelector('td:nth-child(3)');
                
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="status-badge justified">
                            <i class="fas fa-check-circle mr-1"></i>
                            Justifiée
                        </span>
                    `;
                    
                    // Désactiver le bouton
                    button.disabled = true;
                    button.classList.remove('text-green-600', 'hover:bg-green-200', 'dark:text-green-400', 'dark:hover:bg-green-800/50');
                    button.classList.add('text-gray-400', 'cursor-not-allowed', 'dark:text-gray-500');
                    
                    // Afficher une notification
                    showNotification('L\'absence a été marquée comme justifiée', 'success');
                }
            }
        }
        
        // Fonction utilitaire pour afficher des notifications
        function showNotification(message, type = 'info') {
            const types = {
                info: { bg: 'bg-blue-100 dark:bg-blue-900/30', text: 'text-blue-800 dark:text-blue-200' },
                success: { bg: 'bg-green-100 dark:bg-green-900/30', text: 'text-green-800 dark:text-green-200' },
                warning: { bg: 'bg-yellow-100 dark:bg-yellow-900/30', text: 'text-yellow-800 dark:text-yellow-200' },
                error: { bg: 'bg-red-100 dark:bg-red-900/30', text: 'text-red-800 dark:text-red-200' }
            };
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg ${types[type].bg} ${types[type].text} max-w-sm transform transition-all duration-300 translate-x-0 opacity-100`;
            notification.style.transition = 'all 0.3s ease-in-out';
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${type === 'info' ? '<i class="fas fa-info-circle"></i>' : ''}
                        ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : ''}
                        ${type === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' : ''}
                        ${type === 'error' ? '<i class="fas fa-times-circle"></i>' : ''}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Fermer</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Supprimer la notification après 5 secondes
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    </script>
@endpush
@endsection
