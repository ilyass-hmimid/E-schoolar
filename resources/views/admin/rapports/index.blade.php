@extends('layouts.admin')

@section('title', 'Tableau de bord des rapports')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord des rapports</h1>
        <p class="text-gray-600">Vue d'ensemble des statistiques et indicateurs clés</p>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Carte 1: Total des étudiants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Étudiants</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_etudiants'] ?? 0 }}</p>
                    <p class="text-xs text-green-600">+{{ $stats['evolution_etudiants'] ?? 0 }}% vs mois dernier</p>
                </div>
            </div>
        </div>

        <!-- Carte 2: Total des professeurs -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Professeurs</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_professeurs'] ?? 0 }}</p>
                    <p class="text-xs text-green-600">+{{ $stats['evolution_professeurs'] ?? 0 }}% vs mois dernier</p>
                </div>
            </div>
        </div>

        <!-- Carte 3: Taux de présence -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Taux de présence</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['taux_presence'] ?? 0 }}%</p>
                    <p class="text-xs text-red-600">{{ $stats['evolution_presence'] ?? 0 }}% vs mois dernier</p>
                </div>
            </div>
        </div>

        <!-- Carte 4: Revenus mensuels -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Revenus du mois</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['revenus_mois'] ?? 0, 0, ',', ' ') }} €</p>
                    <p class="text-xs {{ ($stats['evolution_revenus'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($stats['evolution_revenus'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['evolution_revenus'] ?? 0 }}% vs mois dernier
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique 1: Évolution des inscriptions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des inscriptions</h3>
            <div class="h-64">
                <canvas id="inscriptionsChart"></canvas>
            </div>
        </div>

        <!-- Graphique 2: Répartition par filière -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par filière</h3>
            <div class="h-64">
                <canvas id="filieresChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Liens rapides -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Rapports détaillés</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.rapports.absences') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Rapport des absences</h4>
                            <p class="text-sm text-gray-500">Voir les statistiques d'absences</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.rapports.paiements') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Rapport des paiements</h4>
                            <p class="text-sm text-gray-500">Voir les statistiques de paiements</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.rapports.salaires') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Rapport des salaires</h4>
                            <p class="text-sm text-gray-500">Voir les statistiques de salaires</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données du graphique des inscriptions
    const inscriptionsCtx = document.getElementById('inscriptionsChart').getContext('2d');
    new Chart(inscriptionsCtx, {
        type: 'line',
        data: {
            labels: @json($charts['inscriptions']['labels'] ?? []),
            datasets: [{
                label: 'Nouvelles inscriptions',
                data: @json($charts['inscriptions']['data'] ?? []),
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Données du graphique des filières
    const filieresCtx = document.getElementById('filieresChart').getContext('2d');
    new Chart(filieresCtx, {
        type: 'doughnut',
        data: {
            labels: @json($charts['filieres']['labels'] ?? []),
            datasets: [{
                data: @json($charts['filieres']['data'] ?? []),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
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
                }
            }
        }
    });
</script>
@endpush
@endsection
