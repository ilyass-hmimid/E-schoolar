@extends('layouts.admin')

@push('modals')
    @foreach($salaires as $salaire)
        @include('admin.salaires._paiement_modal', ['salaire' => $salaire])
    @endforeach
@endpush

@section('title', 'Gestion des salaires')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .status-badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
    .status-paid {
        @apply bg-green-100 text-green-800;
    }
    .status-overdue {
        @apply bg-red-100 text-red-800;
    }
    .action-btn {
        @apply inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2;
    }
    .btn-view {
        @apply bg-blue-600 hover:bg-blue-700 focus:ring-blue-500;
    }
    .btn-pay {
        @apply bg-green-600 hover:bg-green-700 focus:ring-green-500;
    }
    .btn-edit {
        @apply bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500;
    }
    .btn-delete {
        @apply bg-red-600 hover:bg-red-700 focus:ring-red-500;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des salaires</h1>
            <p class="text-sm text-gray-500 mt-1">Consultez et gérez les salaires des professeurs</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <a href="{{ route('admin.salaires.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nouveau paiement
            </a>
            <button type="button" id="generateSalaires" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Générer les salaires
            </button>
            <a href="{{ route('admin.salaires.export', request()->query()) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter Excel
            </a>
            <a href="{{ route('admin.salaires.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter PDF
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form action="{{ route('admin.salaires.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Professeur -->
                <div>
                    <label for="professeur_id" class="block text-sm font-medium text-gray-700 mb-1">Professeur</label>
                    <select name="professeur_id" id="professeur_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les professeurs</option>
                        @foreach($professeurs as $prof)
                            <option value="{{ $prof->id }}" {{ request('professeur_id') == $prof->id ? 'selected' : '' }}>
                                {{ $prof->nom_complet }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Mois/Année -->
                <div>
                    <label for="periode" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                    <input type="month" name="periode" id="periode" 
                           value="{{ request('periode', now()->format('Y-m')) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="paye" {{ request('statut') == 'paye' ? 'selected' : '' }}>Payé</option>
                        <option value="retard" {{ request('statut') == 'retard' ? 'selected' : '' }}>En retard</option>
                    </select>
                </div>
                
                <!-- Type de paiement -->
                <div>
                    <label for="type_paiement" class="block text-sm font-medium text-gray-700 mb-1">Type de paiement</label>
                    <select name="type_paiement" id="type_paiement" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                        <option value="virement" {{ request('type_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                        <option value="cheque" {{ request('type_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                        <option value="especes" {{ request('type_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('admin.salaires.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Réinitialiser
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 11-2 0V4H5v12h3a1 1 0 110 2H4a1 1 0 01-1-1V3zm5 2a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H9a1 1 0 01-1-1V5z" clip-rule="evenodd" />
                    </svg>
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total des professeurs</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $stats['total_professeurs'] }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Salaires payés</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $stats['salaires_payes'] }}
                                </div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    {{ $stats['pourcentage_paye'] }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">En attente</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $stats['en_attente'] }}
                                </div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-yellow-600">
                                    {{ $stats['pourcentage_attente'] }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">En retard</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $stats['en_retard'] }}
                                </div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-red-600">
                                    {{ $stats['pourcentage_retard'] }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des salaires -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Liste des salaires</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Affichage de {{ $salaires->firstItem() }} à {{ $salaires->lastItem() }} sur {{ $salaires->total() }} résultats
                </p>
            </div>
            <div class="flex items-center">
                <select id="per_page" name="per_page" onchange="this.form.submit()" 
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 par page</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 par page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 par page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 par page</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <span>Professeur</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Période
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Salaire net
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date de paiement
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($salaires as $salaire)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" 
                                             src="{{ $salaire->professeur->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($salaire->professeur->nom_complet).'&color=7F9CF5&background=EBF4FF' }}" 
                                             alt="{{ $salaire->professeur->nom_complet }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $salaire->professeur->nom_complet }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $salaire->professeur->matricule }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $salaire->nb_heures }} heures
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH
                                </div>
                                <div class="text-xs text-gray-500">
                                    Brut: {{ number_format($salaire->salaire_brut, 2, ',', ' ') }} DH
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'paye' => 'bg-green-100 text-green-800',
                                        'retard' => 'bg-red-100 text-red-800',
                                    ][$salaire->statut];
                                    
                                    $statusLabels = [
                                        'en_attente' => 'En attente',
                                        'paye' => 'Payé',
                                        'retard' => 'En retard',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabels[$salaire->statut] }}
                                </span>
                                @if($salaire->est_avance)
                                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Avance
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($salaire->date_paiement)
                                    {{ \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') }}
                                    <div class="text-xs text-gray-400">
                                        {{ $salaire->type_paiement }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Non payé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-1">
                                    <a href="{{ route('admin.salaires.show', $salaire) }}" class="action-btn btn-view" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($salaire->statut != 'paye')
                                        <button type="button" 
                                                onclick="openPaiementModal({{ $salaire->id }})"
                                                class="action-btn btn-pay"
                                                title="Enregistrer le paiement">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('admin.salaires.edit', $salaire) }}" class="action-btn btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.salaires.destroy', $salaire) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce salaire ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('admin.salaires.fiche', $salaire) }}" class="action-btn bg-purple-600 hover:bg-purple-700 focus:ring-purple-500" title="Fiche de paie" target="_blank">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun salaire trouvé pour les critères sélectionnés.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">{{ $salaires->firstItem() }}</span> à <span class="font-medium">{{ $salaires->lastItem() }}</span> sur <span class="font-medium">{{ $salaires->total() }}</span> résultats
                    </p>
                </div>
                <div>
                    {{ $salaires->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de paiement -->
<div id="paiementModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Enregistrer un paiement
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Veuillez confirmer les détails du paiement du salaire.
                        </p>
                    </div>
                </div>
            </div>
            
            <form id="paiementForm" method="POST" class="mt-5">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label for="date_paiement" class="block text-sm font-medium text-gray-700">Date de paiement</label>
                        <input type="date" name="date_paiement" id="date_paiement" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    
                    <div>
                        <label for="type_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                        <select id="type_paiement" name="type_paiement" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="virement">Virement bancaire</option>
                            <option value="cheque">Chèque</option>
                            <option value="especes">Espèces</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
                        <input type="text" name="reference" id="reference"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="N° de chèque, référence virement, etc.">
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
                
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button" 
                            onclick="closePaiementModal()"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-1 sm:text-sm">
                        Annuler
                    </button>
                    <button type="submit"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-2 sm:text-sm">
                        Confirmer le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Gestion de la génération des salaires
    document.getElementById('generateSalaires').addEventListener('click', function() {
        // Demander confirmation avant de générer les salaires
        Swal.fire({
            title: 'Générer les salaires',
            text: 'Voulez-vous générer les salaires pour le mois en cours ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, générer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                // Afficher un indicateur de chargement
                Swal.fire({
                    title: 'Génération en cours',
                    text: 'Veuillez patienter...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Appeler l'API pour générer les salaires
                fetch('{{ route('admin.salaires.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: data.message || 'Les salaires ont été générés avec succès',
                            showConfirmButton: true,
                        }).then(() => {
                            // Recharger la page pour afficher les nouveaux salaires
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: error.message || 'Une erreur est survenue lors de la génération des salaires',
                    });
                });
            }
        });
    });
    
    // Code existant
    // Initialisation de flatpickr pour les champs de date
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("input[type=date]", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });
    
    // Gestion de la modale de paiement
    function openPaiementModal(salaireId) {
        const modal = document.getElementById('paiementModal');
        const form = document.getElementById('paiementForm');
        
        // Mise à jour de l'action du formulaire
        form.action = `/admin/salaires/${salaireId}/paiement`;
        
        // Affichage de la modale
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closePaiementModal() {
        const modal = document.getElementById('paiementModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Fermeture de la modale en cliquant en dehors
    document.getElementById('paiementModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaiementModal();
        }
    });
    
    // Génération des salaires
    document.getElementById('generateSalaires').addEventListener('click', function() {
        if (confirm('Voulez-vous générer les salaires pour le mois en cours ?')) {
            // Récupérer la période actuelle (année-mois)
            const periode = document.getElementById('periode').value || '{{ now()->format('Y-m') }}';
            
            // Rediriger vers la route de génération avec la période
            window.location.href = `{{ route('admin.salaires.generate') }}?periode=${periode}`;
        }
    });
    
    // Gestion du changement de page
    document.getElementById('per_page').addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        window.location.href = url.toString();
    });
</script>
@endpush

@endsection
