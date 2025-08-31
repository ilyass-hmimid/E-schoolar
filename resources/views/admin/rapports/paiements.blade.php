@extends('layouts.admin')

@section('title', 'Rapport des paiements')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rapport des paiements</h1>
            <p class="text-gray-600">Analyse et suivi des paiements des étudiants</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.rapports.export-paiements', request()->query()) }}" 
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
        <form action="{{ route('admin.rapports.paiements') }}" method="GET" class="space-y-4">
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
                
                <!-- Niveau -->
                <div>
                    <label for="niveau_id" class="block text-sm font-medium text-gray-700 mb-1">Niveau</label>
                    <select name="niveau_id" id="niveau_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les niveaux</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                {{ $niveau->nom }}
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
                        <option value="en_retard" {{ request('statut') == 'en_retard' ? 'selected' : '' }}>En retard</option>
                        <option value="partiel" {{ request('statut') == 'partiel' ? 'selected' : '' }}>Partiel</option>
                        <option value="non_paye" {{ request('statut') == 'non_paye' ? 'selected' : '' }}>Non payé</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('admin.rapports.paiements') }}" 
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Carte 1: Total des paiements -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total des paiements</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_paiements'] ?? 0, 0, ',', ' ') }} €</p>
                    <p class="text-xs {{ ($stats['evolution_paiements'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($stats['evolution_paiements'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['evolution_paiements'] ?? 0 }}% vs période précédente
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte 2: Taux de recouvrement -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Taux de recouvrement</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['taux_recouvrement'] ?? 0 }}%</p>
                    <p class="text-xs {{ ($stats['evolution_recouvrement'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($stats['evolution_recouvrement'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['evolution_recouvrement'] ?? 0 }}% vs période précédente
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte 3: En retard -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Paiements en retard</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['paiements_retard'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['montant_retard'] ?? 0 }} € à récupérer</p>
                </div>
            </div>
        </div>

        <!-- Carte 4: Paiements partiels -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h3m-3 4h3m-3 4h3m-6-4h.01M9 16h.01M7 8h.01M7 12h.01M7 16h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Paiements partiels</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['paiements_partiels'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['reste_a_payer'] ?? 0 }} € restants</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique 1: Évolution des paiements -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des paiements</h3>
            <div class="h-64">
                <canvas id="evolutionPaiementsChart"></canvas>
            </div>
        </div>

        <!-- Graphique 2: Répartition par statut -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par statut</h3>
            <div class="h-64">
                <canvas id="statutPaiementsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau des paiements -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Détail des paiements
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Liste des paiements selon les critères sélectionnés
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Étudiant
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Référence
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Montant
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date d'échéance
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stats['derniers_paiements'] ?? [] as $paiement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $paiement['etudiant_photo'] ?? asset('images/default-avatar.png') }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $paiement['etudiant_nom'] ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $paiement['classe_nom'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $paiement['reference'] ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $paiement['type_libelle'] ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ number_format($paiement['montant'] ?? 0, 2, ',', ' ') }} €</div>
                                @if(($paiement['montant_paye'] ?? 0) > 0)
                                    <div class="text-xs text-gray-500">{{ number_format($paiement['montant_paye'] ?? 0, 2, ',', ' ') }} € payés</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $paiement['date_echeance_formatee'] ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $paiement['jours_retard'] ?? 0 }} jours de retard</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'paye' => 'bg-green-100 text-green-800',
                                        'en_retard' => 'bg-red-100 text-red-800',
                                        'partiel' => 'bg-yellow-100 text-yellow-800',
                                        'non_paye' => 'bg-gray-100 text-gray-800',
                                    ][$paiement['statut'] ?? 'non_paye'];
                                    $statusLabels = [
                                        'paye' => 'Payé',
                                        'en_retard' => 'En retard',
                                        'partiel' => 'Partiel',
                                        'non_paye' => 'Non payé',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabels[$paiement['statut'] ?? 'non_paye'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Télécharger</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun paiement trouvé pour les critères sélectionnés.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($stats['paiements']) && $stats['paiements']->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $stats['paiements']->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'évolution des paiements
    const evolutionCtx = document.getElementById('evolutionPaiementsChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: @json($charts['evolution']['labels'] ?? []),
            datasets: [
                {
                    label: 'Montant payé',
                    data: @json($charts['evolution']['data'] ?? []),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Objectif',
                    data: @json($charts['evolution']['objectif'] ?? []),
                    borderColor: 'rgb(99, 102, 241)',
                    borderDash: [5, 5],
                    backgroundColor: 'transparent',
                    tension: 0.1,
                    yAxisID: 'y'
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
                legend: {
                    position: 'top',
                },
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
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value);
                        }
                    }
                }
            }
        }
    });

    // Graphique de répartition par statut
    const statutCtx = document.getElementById('statutPaiementsChart').getContext('2d');
    new Chart(statutCtx, {
        type: 'doughnut',
        data: {
            labels: @json($charts['statut']['labels'] ?? []),
            datasets: [{
                data: @json($charts['statut']['data'] ?? []),
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(156, 163, 175, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
