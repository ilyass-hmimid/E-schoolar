@extends('admin.layout')

@section('title', 'Gestion des Matières par Niveau')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Gestion des Matières</h1>
            <p class="mt-1 text-sm text-gray-500">
                Gérer les matières par niveau d'enseignement
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.matieres.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvelle Matière
            </a>
            <button type="button" @click="showImportExport = !showImportExport" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Importer/Exporter
            </button>
        </div>
    </div>
    
    <!-- Menu déroulant Import/Export -->
    <div x-show="showImportExport" @click.away="showImportExport = false" class="mt-4 bg-white rounded-md shadow-lg overflow-hidden ring-1 ring-black ring-opacity-5">
        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
            <a href="{{ route('admin.matieres.import-export') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <svg class="w-4 h-4 inline-block mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Importer des matières
            </a>
            <a href="{{ route('admin.matieres.export') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exporter les matières
            </a>
            <a href="{{ route('admin.matieres.template') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <svg class="w-4 h-4 inline-block mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Télécharger le modèle
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('matieres', () => ({
            showImportExport: false,
            init() {
                // Initialisation si nécessaire
            },
            getNiveauGroupe(niveau) {
                if (niveau.startsWith('primaire_')) return 'Primaire';
                if (niveau.startsWith('college_')) return 'Collège';
                if (niveau === 'tronc_common') return 'Lycée - Tronc commun';
                if (niveau.startsWith('1bac_') || niveau.startsWith('2bac_')) return 'Lycée - Bac';
                return 'Autres';
            },
            getNiveauLibelle(niveau) {
                const niveaux = @json(\App\Models\Matiere::getNiveauxDisponibles());
                return niveaux[niveau] || niveau;
            }
        }));
    });
</script>
@endpush

@section('content')
    <div class="space-y-6" x-data="matieres">
        <!-- Filtres -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Filtres</h2>
                <div class="flex space-x-4">
                    <div class="w-64">
                        <label for="search" class="sr-only">Rechercher</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="search" x-model="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Rechercher...">
                        </div>
                    </div>
                    
                    <div class="w-48">
                        <select id="niveau" x-model="selectedNiveau" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Tous les niveaux</option>
                            @foreach(\App\Models\Matiere::getNiveauxDisponibles() as $key => $niveau)
                                <option value="{{ $key }}">{{ $niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex items-center">
                        <label class="inline-flex items-center">
                            <input type="checkbox" x-model="showInactive" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Afficher inactives</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des matières groupées par niveau -->
        <template x-for="(group, groupName) in groupedMatieres" :key="groupName">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden" x-show="group.length > 0">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-900" x-text="groupName"></h2>
                </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                        <!-- Icône -->
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button type="button" class="flex items-center" @click="sortBy('nom')">
                                            <span>Matière</span>
                                            <span class="ml-1" x-show="sortField === 'nom'">
                                                <svg x-show="sortDirection === 'asc'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                                <svg x-show="sortDirection === 'desc'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button type="button" class="flex items-center" @click="sortBy('niveau')">
                                            <span>Niveau</span>
                                            <span class="ml-1" x-show="sortField === 'niveau'">
                                                <svg x-show="sortDirection === 'asc'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                                <svg x-show="sortDirection === 'desc'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button type="button" class="flex items-center justify-end w-full" @click="sortBy('prix_mensuel')">
                                            <span>Prix Mensuel</span>
                                            <span class="ml-1" x-show="sortField === 'prix_mensuel'">
                                                <svg x-show="sortDirection === 'asc'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                                <svg x-show="sortDirection === 'desc'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Élèves Inscrits
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="matiere in group" :key="matiere.id">
                                    <tr class="hover:bg-gray-50" :class="{ 'bg-gray-50': !matiere.est_active }">
                                        <td class="px-2 py-3 whitespace-nowrap text-center">
                                            <div class="flex-shrink-0 h-8 w-8 mx-auto flex items-center justify-center rounded-md" :style="`background-color: ${matiere.couleur}20; color: ${matiere.couleur};`">
                                                <template x-if="matiere.icone">
                                                    <i :class="matiere.icone + ' text-sm'"></i>
                                                </template>
                                                <template x-if="!matiere.icone">
                                                    <span class="text-sm font-medium" x-text="matiere.nom.charAt(0).toUpperCase()"></span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900" x-text="matiere.nom"></div>
                                                    <div class="text-xs text-gray-500" x-text="matiere.slug"></div>
                                                </div>
                                                <span x-show="matiere.est_fixe" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Fixe
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" x-text="getNiveauLibelle(matiere.niveau)"></div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-right">
                                            <div class="text-sm text-gray-900 font-medium" x-text="formatPrix(matiere.prix_mensuel) + ' DH'"></div>
                                            <div class="text-xs text-gray-500" x-text="'Trim: ' + formatPrix(matiere.prix_trimestriel) + ' DH'"></div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <div class="flex items-center justify-center">
                                                <div class="w-20 bg-gray-200 rounded-full h-2 mr-1">
                                                    <div 
                                                        class="h-2 rounded-full" 
                                                        :class="getProgressColor(matiere.eleves_count, 50)"
                                                        :style="`width: ${Math.min(100, (matiere.eleves_count / 50) * 100)}%`">
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-700" x-text="matiere.eleves_count"></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <div class="flex items-center justify-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-1">
                                                    <div 
                                                        class="h-2 rounded-full" 
                                                        :class="getProgressColor(matiere.professeurs_count, 5)"
                                                        :style="`width: ${Math.min(100, (matiere.professeurs_count / 5) * 100)}%`">
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-700" x-text="matiere.professeurs_count"></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-center">
                                            <span 
                                                x-text="matiere.est_active ? 'Active' : 'Inactive'" 
                                                :class="matiere.est_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('admin.matieres.show', $matiere) }}" class="text-indigo-600 hover:text-indigo-900" title="Voir les détails">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.matieres.edit', $matiere) }}" class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <template x-if="matiere.est_active">
                                                    <form :action="`/admin/matieres/${matiere.id}/desactiver`" method="POST" class="inline">
                                                        <input type="hidden" name="_token" :value="csrfToken">
                                                        <input type="hidden" name="_method" value="PATCH">
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Désactiver">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </template>
                                                <template x-if="!matiere.est_active">
                                                    <form :action="`/admin/matieres/${matiere.id}/activer`" method="POST" class="inline">
                                                        <input type="hidden" name="_token" :value="csrfToken">
                                                        <input type="hidden" name="_method" value="PATCH">
                                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Activer">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </template>
                                                <template x-if="!matiere.est_fixe">
                                                    <form :action="`/admin/matieres/${matiere.id}`" method="POST" class="inline" @submit.prevent="if(confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')) $el.submit()">
                                                        <input type="hidden" name="_token" :value="csrfToken">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button @click="currentPage > 1 ? currentPage-- : null" :disabled="currentPage === 1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage === 1}">
                    Précédent
                </button>
                <button @click="currentPage < totalPages ? currentPage++ : null" :disabled="currentPage >= totalPages" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage >= totalPages}">
                    Suivant
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Affichage de <span class="font-medium" x-text="(currentPage - 1) * perPage + 1"></span> à 
                        <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredMatieres.length)"></span> sur 
                        <span class="font-medium" x-text="filteredMatieres.length"></span> résultats
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button @click="currentPage = 1" :disabled="currentPage === 1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage === 1}">
                            <span class="sr-only">Première page</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M8.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L11.586 10 8.293 6.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button @click="currentPage--" :disabled="currentPage === 1" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage === 1}">
                            <span class="sr-only">Précédent</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <template x-for="page in visiblePages" :key="page">
                            <button @click="currentPage = page" :class="{'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': currentPage === page, 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': currentPage !== page}" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        
                        <button @click="currentPage++" :disabled="currentPage >= totalPages" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage >= totalPages}">
                            <span class="sr-only">Suivant</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button @click="currentPage = totalPages" :disabled="currentPage >= totalPages" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage >= totalPages}">
                            <span class="sr-only">Dernière page</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M11.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 10l-3.293-3.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Aucune matière -->
    <div x-show="filteredMatieres.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune matière trouvée</h3>
        <p class="mt-1 text-sm text-gray-500">
            Essayez de modifier vos critères de recherche ou créez une nouvelle matière.
        </p>
        <div class="mt-6">
            <a href="{{ route('admin.matieres.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvelle Matière
            </a>
        </div>
    </div>

    <!-- Données initiales pour Alpine.js -->
    <div x-data="{
        showImportExport: false,
        search: '',
        selectedNiveau: '',
        showInactive: false,
        sortField: 'nom',
        sortDirection: 'asc',
        currentPage: 1,
        perPage: 10,
        
        // Données des matières
        matieres: @json($matieres->map(function($matiere) {
            return [
                'id' => $matiere->id,
                'nom' => $matiere->nom,
                'slug' => $matiere->slug,
                'niveau' => $matiere->niveau,
                'prix_mensuel' => (float)$matiere->prix_mensuel,
                'prix_trimestriel' => $matiere->prix_trimestriel ? (float)$matiere->prix_trimestriel : null,
                'couleur' => $matiere->couleur,
                'icone' => $matiere->icone,
                'est_active' => (bool)$matiere->est_active,
                'est_fixe' => (bool)$matiere->est_fixe,
                'eleves_count' => (int)$matiere->eleves_count,
                'professeurs_count' => (int)$matiere->professeurs_count,
                'created_at' => $matiere->created_at,
                'updated_at' => $matiere->updated_at
            ];
        })),
        
        // Token CSRF pour les formulaires
        csrfToken: '{{ csrf_token() }}',
        
        // Propriétés calculées
        get filteredMatieres() {
            return this.matieres
                .filter(matiere => {
                    const matchesSearch = !this.search || 
                        matiere.nom.toLowerCase().includes(this.search.toLowerCase()) ||
                        matiere.slug.toLowerCase().includes(this.search.toLowerCase());
                    
                    const matchesNiveau = !this.selectedNiveau || 
                        matiere.niveau === this.selectedNiveau;
                    
                    const matchesActive = this.showInactive || matiere.est_active;
                    
                    return matchesSearch && matchesNiveau && matchesActive;
                })
                .sort((a, b) => {
                    let modifier = 1;
                    if (this.sortDirection === 'desc') modifier = -1;
                    
                    if (a[this.sortField] < b[this.sortField]) return -1 * modifier;
                    if (a[this.sortField] > b[this.sortField]) return 1 * modifier;
                    return 0;
                });
        },
        
        get groupedMatieres() {
            const groups = {};
            
            this.paginatedMatieres.forEach(matiere => {
                const groupName = this.getNiveauGroupe(matiere.niveau);
                if (!groups[groupName]) {
                    groups[groupName] = [];
                }
                groups[groupName].push(matiere);
            });
            
            return groups;
        },
        
        get paginatedMatieres() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredMatieres.slice(start, start + this.perPage);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredMatieres.length / this.perPage);
        },
        
        get visiblePages() {
            const range = [];
            const maxVisible = 5;
            let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let end = Math.min(this.totalPages, start + maxVisible - 1);
            
            if (end - start + 1 < maxVisible) {
                start = Math.max(1, end - maxVisible + 1);
            }
            
            for (let i = start; i <= end; i++) {
                range.push(i);
            }
            
            return range;
        },
        
        // Méthodes
        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.currentPage = 1;
        },
        
        formatPrix(prix) {
            if (!prix) return '0,00';
            return prix.toFixed(2).replace('.', ',');
        },
        
        getProgressColor(value, max) {
            const percentage = (value / max) * 100;
            if (percentage > 80) return 'bg-red-500';
            if (percentage > 60) return 'bg-yellow-500';
            return 'bg-green-500';
        },
        
        getNiveauGroupe(niveau) {
            if (niveau.startsWith('primaire_')) return 'Primaire';
            if (niveau.startsWith('college_')) return 'Collège';
            if (niveau === 'tronc_commun') return 'Lycée - Tronc commun';
            if (niveau.startsWith('1bac_') || niveau.startsWith('2bac_')) return 'Lycée - Bac';
            return 'Autres';
        },
        
        getNiveauLibelle(niveau) {
            const niveaux = @json(\App\Models\Matiere::getNiveauxDisponibles());
            return niveaux[niveau] || niveau;
        }
    }" x-init="">
    </div>

    @if($matieres->count() === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune matière</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez par créer votre première matière.</p>
            <div class="mt-6">
                <a href="{{ route('admin.matieres.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouvelle Matière
                </a>
            </div>
        </div>
    @endif
@endsection
            </div>
        @endif
    </div>
@endsection
