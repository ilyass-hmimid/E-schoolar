@extends('layouts.professeur')

@section('title', 'Mes Salaires')

@push('styles')
<style>
    .salaire-card {
        @apply bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6 transition-all duration-200 hover:shadow-lg;
    }
    .stat-card {
        @apply bg-white rounded-lg shadow p-4 sm:p-6 flex flex-col items-center justify-center text-center transition-all duration-200 hover:shadow-md;
    }
    .stat-value {
        @apply text-3xl font-bold text-blue-600 mt-2;
    }
    .stat-label {
        @apply text-gray-500 text-sm uppercase tracking-wider;
    }
    .badge {
        @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold;
    }
    .badge-paye {
        @apply bg-green-100 text-green-800;
    }
    .badge-en_attente {
        @apply bg-yellow-100 text-yellow-800;
    }
    .badge-retard {
        @apply bg-red-100 text-red-800;
    }
    .badge-annule {
        @apply bg-gray-100 text-gray-800;
    }
    .filter-section {
        @apply bg-white p-4 sm:p-6 rounded-lg shadow mb-6;
    }
    .filter-title {
        @apply text-base sm:text-lg font-medium text-gray-900 mb-4 flex items-center justify-between w-full;
    }
    .chart-container {
        @apply bg-white p-4 sm:p-6 rounded-lg shadow mb-6;
    }
    
    .loading-chart {
        @apply h-64 flex items-center justify-center;
    }
    
    .loading-spinner {
        @apply animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Mes Salaires</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('professeur.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2 text-sm font-medium">Mes Salaires</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Filtres -->
    <div class="filter-section mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <h3 class="filter-title">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtres
                </span>
            </h3>
            <div class="mt-2 sm:mt-0">
                <span class="text-sm text-gray-500">{{ $salaires->total() }} résultat(s) trouvé(s)</span>
            </div>
        </div>
        <form action="{{ route('professeur.salaires.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="paye" {{ request('statut') == 'paye' ? 'selected' : '' }}>Payé</option>
                    <option value="retard" {{ request('statut') == 'retard' ? 'selected' : '' }}>En retard</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div>
                <label for="annee" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                <select id="annee" name="periode" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Toutes les années</option>
                    @foreach($annees as $annee)
                        <option value="{{ $annee }}" {{ request('periode') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 7.707A1 1 0 013 7V3z" clip-rule="evenodd" />
                    </svg>
                    Filtrer
                </button>
                @if(request()->has('statut') || request()->has('periode'))
                    <a href="{{ route('professeur.salaires.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="p-4">
                <span class="stat-label">Salaire Moyen</span>
                <span class="stat-value">{{ number_format($statistiques['moyenne_mensuelle'] ?? 0, 2, ',', ' ') }} DH</span>
                <p class="text-xs text-gray-500 mt-1">Mensuel moyen</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="p-4">
                <span class="stat-label">Total Brut</span>
                <span class="stat-value">{{ number_format($statistiques['total_brut'] ?? 0, 2, ',', ' ') }} DH</span>
                <p class="text-xs text-gray-500 mt-1">Cumul brut</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="p-4">
                <span class="stat-label">Total Net</span>
                <span class="stat-value">{{ number_format($statistiques['total_net'] ?? 0, 2, ',', ' ') }} DH</span>
                <p class="text-xs text-gray-500 mt-1">Cumul net</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="p-4">
                <span class="stat-label">Total des Paiements</span>
                <span class="stat-value">{{ $statistiques['total'] ?? 0 }}</span>
                <p class="text-xs text-gray-500 mt-1">Périodes payées</p>
            </div>
        </div>
    </div>

    <!-- Graphique d'évolution annuelle -->
    @if(isset($statistiques['par_annee']) && count($statistiques['par_annee']) > 0)
    <div class="chart-container mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Évolution annuelle des salaires</h3>
            <div class="flex space-x-2">
                <button type="button" id="chartZoomIn" class="p-1 rounded-full text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
                <button type="button" id="chartZoomOut" class="p-1 rounded-full text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="relative">
            <div id="chartLoading" class="loading-chart">
                <div class="loading-spinner"></div>
            </div>
            <div class="chart-wrapper" style="min-height: 300px;">
                <canvas id="salaireChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Dernier paiement et prochain paiement -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if($statistiques['dernier_paiement'] ?? false)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">Dernier paiement</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Le {{ \Carbon\Carbon::parse($statistiques['dernier_paiement']->date_paiement)->format('d/m/Y') }}
                        <span class="block font-medium">{{ number_format($statistiques['dernier_paiement']->salaire_net, 2, ',', ' ') }} DH</span>
                    </p>
                </div>
            </div>
        </div>
        @endif

        @php
            $prochainPaiement = now()->addMonth()->startOfMonth();
            $joursRestants = now()->diffInDays($prochainPaiement, false);
        @endphp
        
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-green-800">Prochain paiement prévu</h4>
                    <p class="text-sm text-green-700 mt-1">
                        {{ $prochainPaiement->format('d/m/Y') }}
                        <span class="block font-medium">
                            {{ $joursRestants > 0 ? "Dans $joursRestants jours" : 'Aujourd\'hui' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des salaires -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Historique des salaires</h2>
        </div>
        
        @if($salaires->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($salaires as $salaire)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Salaire de {{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}
                                </h3>
                                <span class="ml-3 px-3 py-1 rounded-full text-xs font-semibold {{ 
                                    $salaire->statut === 'paye' ? 'bg-green-100 text-green-800' : 
                                    ($salaire->statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                                }}">
                                    {{ ucfirst($salaire->statut) }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Référence: {{ $salaire->reference }}
                                @if($salaire->date_paiement)
                                    • Paié le {{ \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center">
                            <div class="text-right mr-6">
                                <p class="text-sm font-medium text-gray-500">Salaire Net</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('professeur.salaires.show', $salaire) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    Voir détails
                                </a>
                                @if($salaire->statut === 'paye')
                                <a href="{{ route('professeur.salaires.telecharger-fiche-paie', $salaire) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Télécharger
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                {{ $salaires->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun salaire trouvé</h3>
                <p class="mt-1 text-sm text-gray-500">Aucun relevé de salaire n'est disponible pour le moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Graphique d'évolution annuelle
        const ctx = document.getElementById('salaireChart');
        let chart;
        
        if (ctx) {
            const annees = {!! json_encode(array_keys($statistiques['par_annee']->toArray())) !!};
            const montants = {!! json_encode(array_column($statistiques['par_annee']->toArray(), 'montant_total')) !!};
            
            // Masquer le loader une fois le graphique chargé
            const chartLoading = document.getElementById('chartLoading');
            
            // Configuration du graphique
            const config = {
                type: 'line',
                data: {
                    labels: annees,
                    datasets: [{
                        label: 'Salaire annuel (DH)',
                        data: montants,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'rgb(29, 78, 216)',
                        pointHoverBorderColor: '#fff',
                        pointHitRadius: 10,
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        onComplete: function() {
                            if (chartLoading) {
                                chartLoading.style.display = 'none';
                            }
                        }
                    },
                    plugins: {
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'x',
                            },
                            pan: {
                                enabled: true,
                                mode: 'x',
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR', { 
                                        style: 'currency', 
                                        currency: 'MAD',
                                        maximumFractionDigits: 0
                                    }).format(value).replace('MAD', '') + ' DH';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total: ' + new Intl.NumberFormat('fr-FR', { 
                                        style: 'currency', 
                                        currency: 'MAD',
                                        minimumFractionDigits: 2
                                    }).format(context.raw).replace('MAD', '') + ' DH';
                                }
                            },
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            cornerRadius: 6,
                            displayColors: false
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Gestion des filtres
        const filterForm = document.querySelector('form[method="GET"]');
        if (filterForm) {
            // Ajouter un indicateur de chargement
            const submitButton = filterForm.querySelector('button[type="submit"]');
            
            // Soumission du formulaire
            filterForm.addEventListener('submit', function() {
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Application des filtres...
                    `;
                }
            });
            
            // Réinitialisation des filtres
            const resetButton = filterForm.querySelector('a[href="' + window.location.pathname + '"]');
            if (resetButton) {
                resetButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = this.href;
                });
            }
        }
    });
</script>
@endpush
