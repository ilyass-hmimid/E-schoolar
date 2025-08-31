@extends('layouts.admin')

@section('title', 'Tableau de bord')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
    <p class="text-gray-600">Bienvenue dans votre espace d'administration</p>
</div>

<!-- Cartes de statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 stat-card">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Élèves</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $elevesCount ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 stat-card">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-chalkboard-teacher text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Professeurs</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $professeursCount ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 stat-card">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <i class="fas fa-book text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Cours</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $coursCount ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 stat-card">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Revenus</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($revenus ?? 0, 2, ',', ' ') }} DH</p>
            </div>
        </div>
    </div>
</div>

<!-- Graphique des revenus -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Revenus mensuels ({{ now()->year }})</h3>
    </div>
    <div class="h-64">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels ?? []),
                datasets: [{
                    label: 'Revenus (DH)',
                    data: @json($revenusParMois ?? []),
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderColor: 'rgba(79, 70, 229, 0.8)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
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
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' DH';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

<!-- Section Activités récentes -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Activités récentes</h3>
    </div>
    <div class="divide-y divide-gray-200">
        @foreach($activites ?? [] as $activite)
        <div class="p-6 hover:bg-gray-50">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas {{ $activite['icon'] ?? 'fa-bell' }} text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">{{ $activite['titre'] ?? 'Nouvelle activité' }}</p>
                    <p class="text-sm text-gray-500">{{ $activite['description'] ?? 'Description de l\'activité' }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ $activite['date'] ?? now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="px-6 py-4 bg-gray-50 text-right">
        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">Voir toutes les activités</a>
    </div>
</div>

<!-- Aide rapide -->
<div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 mb-8 overflow-hidden relative">
    <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-200 to-transparent"></div>
    <div class="relative z-10">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Besoin d'aide ?</h3>
        <p class="text-gray-600 mb-4 max-w-lg">Consultez notre centre d'aide pour trouver des réponses à vos questions ou contactez notre équipe de support.</p>
        <div class="flex flex-wrap gap-4">
            <a href="#" class="px-4 py-2 bg-white text-blue-600 rounded-md text-sm font-medium hover:bg-blue-50">
                Centre d'aide
            </a>
            <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                Contacter le support
            </a>
        </div>
    </div>
    <div class="absolute right-6 bottom-0 h-32 w-32 text-blue-200">
        <i class="fas fa-question-circle text-8xl opacity-50"></i>
    </div>
</div>
@endsection
