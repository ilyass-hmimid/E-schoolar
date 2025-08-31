@extends('layouts.admin')

@section('title', 'Rapport des salaires')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rapport des salaires</h1>
            <p class="text-gray-600">Analyse et suivi des salaires des professeurs</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.rapports.export-salaires', request()->query()) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter en PDF
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form action="{{ route('admin.rapports.salaires') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Période -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                    <div class="flex space-x-2">
                        <input type="month" name="mois" id="mois" 
                               value="{{ request('mois') ?? now()->format('Y-m') }}" 
                               class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <select name="annee" id="annee" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @for($i = now()->year; $i >= now()->subYears(5)->year; $i--)
                                <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                <!-- Professeur -->
                <div>
                    <label for="professeur_id" class="block text-sm font-medium text-gray-700 mb-1">Professeur</label>
                    <select name="professeur_id" id="professeur_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les professeurs</option>
                        @foreach($professeurs as $professeur)
                            <option value="{{ $professeur->id }}" {{ request('professeur_id') == $professeur->id ? 'selected' : '' }}>
                                {{ $professeur->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="paye" {{ request('statut') == 'paye' ? 'selected' : '' }}>Payé</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="en_retard" {{ request('statut') == 'en_retard' ? 'selected' : '' }}>En retard</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('admin.rapports.salaires') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Réinitialiser
                </a>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Appliquer les filtres
                </button>
            </div>
        </form>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cis-4 gap-6 mb-8">
        <!-- Carte 1: Masse salariale -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Masse salariale</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['masse_salariale'] ?? 0, 0, ',', ' ') }} €</p>
                    <p class="text-xs {{ ($stats['evolution_masse'] ?? 0) >= 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ ($stats['evolution_masse'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['evolution_masse'] ?? 0 }}% vs période précédente
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte 2: Salaire moyen -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Salaire moyen</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['salaire_moyen'] ?? 0, 0, ',', ' ') }} €</p>
                    <p class="text-xs {{ ($stats['evolution_moyen'] ?? 0) >= 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ ($stats['evolution_moyen'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['evolution_moyen'] ?? 0 }}% vs période précédente
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte 3: Nombre de professeurs -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Nombre de professeurs</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['nombre_professeurs'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['nouveaux_professeurs'] ?? 0 }} nouveaux cette période</p>
                </div>
            </div>
        </div>

        <!-- Carte 4: Heures enseignées -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Heures enseignées</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['heures_enseignees'] ?? 0 }}h</p>
                    <p class="text-xs text-gray-500">{{ $stats['cours_dispenses'] ?? 0 }} cours dispensés</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique 1: Évolution des salaires -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des salaires</h3>
            <div class="h-64">
                <canvas id="evolutionSalairesChart"></canvas>
            </div>
        </div>

        <!-- Graphique 2: Répartition par matière -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par matière</h3>
            <div class="h-64">
                <canvas id="matieresSalairesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau des salaires -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Détail des salaires
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Liste des salaires selon les critères sélectionnés
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Professeur
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Période
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Salaire de base
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Heures supplémentaires
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Primes
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stats['salaires'] ?? [] as $salaire)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $salaire['professeur_photo'] ?? asset('images/default-avatar.png') }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $salaire['professeur_nom'] ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $salaire['matiere_principale'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $salaire['periode'] ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $salaire['annee_scolaire'] ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($salaire['salaire_base'] ?? 0, 0, ',', ' ') }} €</div>
                                <div class="text-xs text-gray-500">{{ $salaire['heures_contractuelles'] ?? 0 }}h</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($salaire['montant_heures_sup'] ?? 0, 0, ',', ' ') }} €</div>
                                <div class="text-xs text-gray-500">{{ $salaire['heures_supplementaires'] ?? 0 }}h</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($salaire['total_primes'] ?? 0, 0, ',', ' ') }} €</div>
                                <div class="text-xs text-gray-500">{{ $salaire['nombre_primes'] ?? 0 }} primes</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ number_format($salaire['salaire_net'] ?? 0, 0, ',', ' ') }} €
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'paye' => 'bg-green-100 text-green-800',
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'en_retard' => 'bg-red-100 text-red-800',
                                    ][$salaire['statut'] ?? 'en_attente'];
                                    $statusLabels = [
                                        'paye' => 'Payé',
                                        'en_attente' => 'En attente',
                                        'en_retard' => 'En retard',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabels[$salaire['statut'] ?? 'en_attente'] }}
                                </span>
                                @if(($salaire['date_paiement'] ?? false))
                                    <div class="text-xs text-gray-500 mt-1">Payé le {{ $salaire['date_paiement'] }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun salaire trouvé pour les critères sélectionnés.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($stats['salaires']) && $stats['salaires']->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $stats['salaires']->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'évolution des salaires
    const evolutionCtx = document.getElementById('evolutionSalairesChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: @json($charts['evolution']['labels'] ?? []),
            datasets: [
                {
                    label: 'Salaire moyen',
                    data: @json($charts['evolution']['moyen'] ?? []),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Masse salariale',
                    data: @json($charts['evolution']['masse'] ?? []),
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Salaire moyen'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Masse salariale'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0, notation: 'compact' }).format(value);
                        }
                    }
                }
            }
        }
    });

    // Graphique de répartition par matière
    const matieresCtx = document.getElementById('matieresSalairesChart').getContext('2d');
    new Chart(matieresCtx, {
        type: 'bar',
        data: {
            labels: @json($charts['matieres']['labels'] ?? []),
            datasets: [{
                label: 'Salaire total',
                data: @json($charts['matieres']['data'] ?? []),
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value);
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
