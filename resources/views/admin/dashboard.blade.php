@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Message de bienvenue -->
    <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bonjour, {{ Auth::user()->name }} 👋</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Voici un aperçu de votre tableau de bord pour aujourd'hui, {{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
    </div>

    <!-- En-tête avec statistiques -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Carte Nombre d'élèves -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-200">
                        <i class="text-2xl fas fa-user-graduate"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Élèves</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['eleves_count'] ?? 0 }}</p>
                    </div>
                </div>
                @if(isset($stats['eleves_nouveaux']) && $stats['eleves_nouveaux'] > 0)
                    <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">
                        +{{ $stats['eleves_nouveaux'] }} ce mois
                    </span>
                @endif
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.eleves.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Voir tous les élèves <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        <!-- Carte Nombre de professeurs -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-200">
                        <i class="text-2xl fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Professeurs</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['professeurs_count'] ?? 0 }}</p>
                    </div>
                </div>
                @if(isset($stats['moyenne_notes']) && $stats['moyenne_notes'] > 0)
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Moyenne</p>
                        <span class="px-2 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">
                            {{ $stats['moyenne_notes'] }}/20
                        </span>
                    </div>
                @endif
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.professeurs.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                    Voir tous les professeurs <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        <!-- Carte Paiements du mois -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-200">
                        <i class="text-2xl fas fa-credit-card"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Paiements ce mois</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['paiements_mois'] ?? 0, 0, ',', ' ') }} DH</p>
                    </div>
                </div>
                @if(isset($stats['variation_paiements']) && $stats['paiements_mois_dernier'] > 0)
                    @php
                        $isIncrease = $stats['variation_paiements'] >= 0;
                        $icon = $isIncrease ? 'arrow-up' : 'arrow-down';
                        $colorClass = $isIncrease ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                    @endphp
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">vs mois dernier</p>
                        <span class="inline-flex items-center text-sm font-medium {{ $colorClass }}">
                            <i class="fas fa-{{ $icon }} mr-1"></i>
                            {{ abs(round($stats['variation_paiements'])) }}%
                        </span>
                    </div>
                @endif
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.paiements.index') }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-500 dark:text-yellow-400 dark:hover:text-yellow-300">
                    Voir les paiements <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        <!-- Carte Taux de présence -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-200">
                        <i class="text-2xl fas fa-calendar-check"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Taux de présence</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['taux_presence'] ?? 0 }}%</p>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                sur {{ $stats['total_seances'] ?? 0 }} séances
                            </span>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12">
                    <canvas id="presenceGauge"></canvas>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.absences.index') }}" class="text-sm font-medium text-purple-600 hover:text-purple-500 dark:text-purple-400 dark:hover:text-purple-300">
                    Voir les absences <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Graphique des paiements mensuels -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Évolution des paiements</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        <span class="w-2 h-2 mr-1 bg-blue-500 rounded-full"></span>
                        Montant (DH)
                    </span>
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <span class="w-2 h-2 mr-1 bg-green-500 rounded-full"></span>
                        Élèves payants
                    </span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="paiementsChart"></canvas>
            </div>
        </div>

        <!-- Graphique des absences -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Présence des 30 derniers jours</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        <span class="w-2 h-2 mr-1 bg-red-500 rounded-full"></span>
                        Absences
                    </span>
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        <span class="w-2 h-2 mr-1 bg-yellow-500 rounded-full"></span>
                        Justifiées
                    </span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="presenceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau des dernières absences -->
    <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Dernières absences</h3>
            <div class="flex space-x-2">
                <a href="{{ route('admin.absences.create') }}" class="px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-600">
                    <i class="mr-1 fas fa-plus"></i> Nouvelle absence
                </a>
                <a href="{{ route('admin.absences.index') }}" class="px-3 py-1.5 text-sm font-medium text-primary-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-primary-400 dark:border-gray-600 dark:hover:bg-gray-600">
                    Voir tout
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Élève</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Date</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Cours</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($recentAbsences as $absence)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-200 font-semibold">
                                            {{ strtoupper(substr($absence->eleve->nom, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $absence->eleve->nom }} {{ $absence->eleve->prenom }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $absence->eleve->classe->nom ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $absence->date_absence->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $absence->date_absence->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $absence->cours->matiere->nom ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $absence->cours->professeur->nom ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($absence->justifiee)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Justifiée
                                    </span>
                                    @if($absence->justificatif)
                                        <a href="{{ Storage::url($absence->justificatif) }}" target="_blank" class="ml-1 text-xs text-blue-600 hover:text-blue-500 dark:text-blue-400" title="Voir le justificatif">
                                            <i class="fas fa-paperclip"></i>
                                        </a>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <i class="fas fa-times-circle mr-1"></i> Non justifiée
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-300">
                                Aucune absence récente à afficher
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Prochains événements -->
    <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Prochains événements</h3>
            <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                Voir le calendrier
            </a>
        </div>
        <div class="space-y-4">
            @forelse($prochainsEvenements as $evenement)
                <div class="flex items-start p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0 p-2 mr-4 text-{{ $evenement->couleur }}-600 bg-{{ $evenement->couleur }}-100 rounded-lg dark:bg-{{ $evenement->couleur }}-900 dark:text-{{ $evenement->couleur }}-200">
                        <i class="fas fa-{{ $evenement->icone }} text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $evenement->titre }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $evenement->date->format('d/m/Y H:i') }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $evenement->description }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-center text-gray-500 dark:text-gray-400">Aucun événement à venir</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Détecter le mode sombre
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Configuration des couleurs en fonction du mode
    const chartColors = {
        text: isDarkMode ? '#E5E7EB' : '#374151',
        grid: isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
        blue: {
            light: isDarkMode ? 'rgba(59, 130, 246, 0.5)' : 'rgba(59, 130, 246, 0.2)',
            dark: isDarkMode ? 'rgba(96, 165, 250, 1)' : 'rgba(37, 99, 235, 1)'
        },
        green: {
            light: isDarkMode ? 'rgba(34, 197, 94, 0.5)' : 'rgba(34, 197, 94, 0.2)',
            dark: isDarkMode ? 'rgba(74, 222, 128, 1)' : 'rgba(22, 163, 74, 1)'
        },
        red: {
            light: isDarkMode ? 'rgba(239, 68, 68, 0.5)' : 'rgba(239, 68, 68, 0.2)',
            dark: isDarkMode ? 'rgba(248, 113, 113, 1)' : 'rgba(220, 38, 38, 1)'
        },
        yellow: {
            light: isDarkMode ? 'rgba(234, 179, 8, 0.5)' : 'rgba(234, 179, 8, 0.2)',
            dark: isDarkMode ? 'rgba(250, 204, 21, 1)' : 'rgba(202, 138, 4, 1)'
        }
    };

    // Configuration commune des graphiques
    const chartConfig = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: isDarkMode ? '#1F2937' : '#FFFFFF',
                titleColor: chartColors.text,
                bodyColor: chartColors.text,
                borderColor: isDarkMode ? '#374151' : '#E5E7EB',
                borderWidth: 1,
                padding: 12,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat('fr-FR').format(context.parsed.y);
                            if (context.dataset.unit) {
                                label += ' ' + context.dataset.unit;
                            }
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    color: chartColors.text
                }
            },
            y: {
                grid: {
                    color: chartColors.grid,
                    drawBorder: false
                },
                ticks: {
                    color: chartColors.text,
                    callback: function(value) {
                        return new Intl.NumberFormat('fr-FR').format(value);
                    }
                }
            }
        }
    };

    // Graphique des paiements
    const paiementsCtx = document.getElementById('paiementsChart')?.getContext('2d');
    if (paiementsCtx) {
        new Chart(paiementsCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['paiements']['labels'] ?? []),
                datasets: [
                    {
                        label: 'Montant des paiements',
                        data: @json($chartData['paiements']['data']['montants'] ?? []),
                        borderColor: chartColors.blue.dark,
                        backgroundColor: chartColors.blue.light,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        unit: 'DH',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Nombre d\'élèves payants',
                        data: @json($chartData['paiements']['data']['effectifs'] ?? []),
                        borderColor: chartColors.green.dark,
                        backgroundColor: chartColors.green.light,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                ...chartConfig,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    ...chartConfig.scales,
                    y: {
                        ...chartConfig.scales.y,
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Montant (DH)',
                            color: chartColors.text
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Nombre d\'élèves',
                            color: chartColors.text
                        },
                        ticks: {
                            color: chartColors.text,
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Graphique des présences/absences
    const presenceCtx = document.getElementById('presenceChart')?.getContext('2d');
    if (presenceCtx) {
        new Chart(presenceCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['presence']['labels'] ?? []),
                datasets: [
                    {
                        label: 'Absences justifiées',
                        data: @json($chartData['presence']['data']['justifiees'] ?? []),
                        backgroundColor: chartColors.yellow.dark,
                        borderColor: chartColors.yellow.dark,
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Absences non justifiées',
                        data: @json($chartData['presence']['data']['non_justifiees'] ?? []),
                        backgroundColor: chartColors.red.dark,
                        borderColor: chartColors.red.dark,
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                ...chartConfig,
                scales: {
                    ...chartConfig.scales,
                    y: {
                        ...chartConfig.scales.y,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre d\'absences',
                            color: chartColors.text
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    ...chartConfig.plugins,
                    tooltip: {
                        ...chartConfig.plugins.tooltip,
                        callbacks: {
                            ...chartConfig.plugins.tooltip.callbacks,
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                    if (context.parsed.y <= 1) {
                                        label += ' absence';
                                    } else {
                                        label += ' absences';
                                    }
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Jauge de taux de présence
    const presenceGaugeCtx = document.getElementById('presenceGauge')?.getContext('2d');
    if (presenceGaugeCtx) {
        new Chart(presenceGaugeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Présent', 'Absent'],
                datasets: [{
                    data: [
                        {{ $stats['taux_presence'] ?? 0 }}, 
                        {{ max(0, 100 - ($stats['taux_presence'] ?? 0)) }}
                    ],
                    backgroundColor: [
                        isDarkMode ? 'rgba(74, 222, 128, 0.8)' : 'rgba(34, 197, 94, 0.8)',
                        isDarkMode ? 'rgba(75, 85, 99, 0.5)' : 'rgba(229, 231, 235, 0.8)'
                    ],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Mettre à jour les graphiques lors du changement de thème
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const isDarkNow = document.documentElement.classList.contains('dark');
                if (isDarkNow !== isDarkMode) {
                    window.location.reload();
                }
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
</script>
@endpush
