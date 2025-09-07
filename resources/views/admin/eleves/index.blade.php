@extends('layouts.app')

@section('title', 'Gestion des élèves')

@push('styles')
<style>
    .main-content {
        width: 100%;
        position: relative;
        z-index: 10;
    }
    
    @media (min-width: 768px) {
        .main-content {
            margin-left: 16rem;
            width: calc(100% - 16rem);
        }
    }

    .stat-card {
        @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-all duration-300 hover:shadow-md border border-gray-100 dark:border-gray-700;
        height: 100%;
    }

    .stat-card.primary {
        @apply border-l-4 border-primary-500;
    }
</style>
@endpush

@section('content')
<div x-data="{ sidebarOpen: false, darkMode: false, toggleDarkMode() { this.darkMode = !this.darkMode; localStorage.setItem('darkMode', this.darkMode); document.documentElement.classList.toggle('dark', this.darkMode) } }" x-init="() => { darkMode = localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches); document.documentElement.classList.toggle('dark', darkMode) }">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content flex flex-col flex-1 min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Top Navigation -->
        @include('admin.partials.top-navigation')
        
        <!-- Page Content -->
        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    <!-- Page Header -->
                    <div class="md:flex md:items-center md:justify-between mb-6">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                                Gestion des élèves
                            </h2>
                            <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-users mr-1.5"></i>
                                    {{ $eleves->total() }} élève(s) enregistré(s)
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex md:mt-0 md:ml-4">
                            <a href="{{ route('admin.eleves.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-plus mr-2"></i>Ajouter un élève
                            </a>
                        </div>
                    </div>

                    <!-- Stats Overview -->
                    <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Total des élèves -->
                        <div class="stat-card primary">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des élèves</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_eleves'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Élèves actifs -->
                        <div class="stat-card">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Élèves actifs</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['eleves_actifs'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nouveaux élèves (ce mois-ci) -->
                        <div class="stat-card">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    <i class="fas fa-user-plus text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nouveaux (30j)</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['nouveaux_eleves'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Taux d'absences -->
                        <div class="stat-card">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                    <i class="fas fa-user-minus text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Taux d'absences</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['taux_absences'] ?? 0 }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barre de recherche et filtres -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden mb-6">
                        <form action="{{ route('admin.eleves.index') }}" method="GET" class="p-4">
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-1">
                                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Rechercher un élève
                                    </label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 pr-12 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm" placeholder="Nom, prénom, email ou CNE">
                                        @if(request('search'))
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <a href="{{ route('admin.eleves.index', array_merge(request()->except('search', 'page'))) }}" class="text-gray-400 hover:text-gray-500">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-end space-x-2">
                                    <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white">
                                        <option value="">Tous les statuts</option>
                                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactif" {{ request('status') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                        <option value="abandonne" {{ request('status') == 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                                        <option value="diplome" {{ request('status') == 'diplome' ? 'selected' : '' }}>Diplômé</option>
                                    </select>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        <i class="fas fa-search mr-2"></i>Rechercher
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tableau des élèves -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <span>Élève</span>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="ml-1">
                                                    <i class="fas fa-sort {{ request('sort') === 'name' ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                                </a>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <span>CNE</span>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'cne', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="ml-1">
                                                    <i class="fas fa-sort {{ request('sort') === 'cne' ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                                </a>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <span>Niveau</span>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'niveau_id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="ml-1">
                                                    <i class="fas fa-sort {{ request('sort') === 'niveau_id' ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                                </a>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <span>Statut</span>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="ml-1">
                                                    <i class="fas fa-sort {{ request('sort') === 'status' ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                                </a>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($eleves as $eleve)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $eleve->photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $eleve->name }}">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $eleve->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $eleve->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $eleve->cne ?? 'Non renseigné' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($eleve->niveau)
                                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                        {{ $eleve->niveau->nom }}
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 text-gray-500 dark:text-gray-400">Non assigné</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusClasses = [
                                                        'actif' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                        'inactif' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                        'abandonne' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                        'diplome' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'
                                                    ][$eleve->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-300';
                                                @endphp
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                                    {{ ucfirst($eleve->status ?? 'inactif') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-3">
                                                    <a href="{{ route('admin.eleves.show', $eleve) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-150" data-tooltip="Voir le profil">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.eleves.edit', $eleve) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors duration-150" data-tooltip="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.eleves.destroy', $eleve) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-150" data-tooltip="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                                    <i class="fas fa-user-graduate text-4xl mb-3"></i>
                                                    <p class="text-lg font-medium">Aucun élève trouvé</p>
                                                    <p class="text-sm mt-1">Aucun élève ne correspond à votre recherche</p>
                                                    @if(request()->hasAny(['search', 'status']))
                                                        <a href="{{ route('admin.eleves.index') }}" class="mt-3 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                            Réinitialiser les filtres
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.eleves.create') }}" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                            <i class="fas fa-plus mr-2"></i>Ajouter un élève
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($eleves->hasPages())
                            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                                {{ $eleves->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Focus sur le champ de recherche au chargement de la page
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.focus();
            // Sélectionner tout le texte si une recherche est déjà présente
            if (searchInput.value) {
                searchInput.select();
            }
        }
        
        // Effacer la recherche en cliquant sur l'icône de suppression
        const clearButton = document.querySelector('.clear-search');
        if (clearButton) {
            clearButton.addEventListener('click', function(e) {
                e.preventDefault();
                searchInput.value = '';
                searchInput.focus();
                this.closest('form').submit();
            });
        }
    });
</script>
@endpush
@endsection
