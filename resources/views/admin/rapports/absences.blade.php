@extends('layouts.admin')

@section('title', 'Rapport des absences')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rapport des absences</h1>
            <p class="text-gray-600">Analyse et statistiques des absences des étudiants</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.rapports.export-absences', request()->query()) }}" 
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
        <form action="{{ route('admin.rapports.absences') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Date de début -->
                <div>
                    <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" 
                           value="{{ request('date_debut') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <!-- Date de fin -->
                <div>
                    <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" 
                           value="{{ request('date_fin') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                
                <!-- Filière -->
                <div>
                    <label for="filiere_id" class="block text-sm font-medium text-gray-700 mb-1">Filière</label>
                    <select name="filiere_id" id="filiere_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toutes les filières</option>
                        @foreach($filieres as $filiere)
                            <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                {{ $filiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Matière -->
                <div class="md:col-span-2">
                    <label for="matiere_id" class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
                    <select name="matiere_id" id="matiere_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toutes les matières</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('admin.rapports.absences') }}" 
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
        <!-- Carte 1: Total des absences -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total des absences</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_absences'] ?? 0 }}</p>
                    <p class="text-xs {{ ($stats['evolution_absences'] ?? 0) >= 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ ($stats['evolution_absences'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['evolution_absences'] ?? 0 }}% vs période précédente
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte 2: Taux d'absentéisme -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Taux d'absentéisme</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['taux_absenteisme'] ?? 0 }}%</p>
                    <p class="text-xs {{ ($stats['evolution_taux_absenteisme'] ?? 0) >= 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ ($stats['evolution_taux_absenteisme'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['evolution_taux_absenteisme'] ?? 0 }}% vs période précédente
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte 3: Absences justifiées -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Justifiées</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['absences_justifiees'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['pourcentage_justifiees'] ?? 0 }}% du total</p>
                </div>
            </div>
        </div>

        <!-- Carte 4: Absences non justifiées -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Non justifiées</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['absences_non_justifiees'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['pourcentage_non_justifiees'] ?? 0 }}% du total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique 1: Évolution des absences -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des absences</h3>
            <div class="h-64">
                <canvas id="evolutionAbsencesChart"></canvas>
            </div>
        </div>

        <!-- Graphique 2: Répartition par matière -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par matière</h3>
            <div class="h-64">
                <canvas id="matieresAbsencesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau des absences -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Détail des absences
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Liste des absences selon les critères sélectionnés
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
                            Matière
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Justification
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stats['dernieres_absences'] ?? [] as $absence)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $absence['etudiant_photo'] ?? asset('images/default-avatar.png') }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $absence['etudiant_nom'] ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $absence['classe_nom'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $absence['matiere_nom'] ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $absence['enseignant_nom'] ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $absence['date_formatee'] ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $absence['heure_debut'] ?? '' }} - {{ $absence['heure_fin'] ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'justifiee' => 'bg-green-100 text-green-800',
                                        'non_justifiee' => 'bg-red-100 text-red-800',
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                    ][$absence['statut'] ?? 'en_attente'] ?? 'bg-gray-100 text-gray-800';
                                    $statusLabels = [
                                        'justifiee' => 'Justifiée',
                                        'non_justifiee' => 'Non justifiée',
                                        'en_attente' => 'En attente',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabels[$absence['statut'] ?? 'en_attente'] ?? $absence['statut'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $absence['justification'] ?? 'Aucune justification' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucune absence trouvée pour les critères sélectionnés.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($stats['absences']) && $stats['absences']->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $stats['absences']->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'évolution des absences
    const evolutionCtx = document.getElementById('evolutionAbsencesChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: @json($charts['evolution']['labels'] ?? []),
            datasets: [
                {
                    label: 'Absences',
                    data: @json($charts['evolution']['data'] ?? []),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
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

    // Graphique de répartition par matière
    const matieresCtx = document.getElementById('matieresAbsencesChart').getContext('2d');
    new Chart(matieresCtx, {
        type: 'doughnut',
        data: {
            labels: @json($charts['matieres']['labels'] ?? []),
            datasets: [{
                data: @json($charts['matieres']['data'] ?? []),
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
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
