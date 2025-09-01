@extends('layouts.admin')

@section('title', 'Gestion des classes')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Gestion des classes</h1>
            <p class="text-gray-400">Liste des classes et groupes de l'établissement</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-file-export mr-2"></i> Exporter
            </button>
            <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Nouvelle classe
            </a>
        </div>
    </div>

    <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
        <!-- Filtres et recherches -->
        <div class="px-6 py-4 border-b border-dark-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-500"></i>
                        </div>
                        <input type="text" id="search" class="bg-dark-700 border-0 text-white rounded-lg pl-10 pr-4 py-2 w-full focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Rechercher une classe...">
                    </div>
                </div>
                <select id="filter-niveau" class="bg-dark-700 border-0 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <option value="">Tous les niveaux</option>
                    @foreach(\App\Models\Niveau::all() as $niveau)
                        <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                    @endforeach
                </select>
                <select id="filter-annee" class="bg-dark-700 border-0 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <option value="">Toutes les années</option>
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}-{{ $i + 1 }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Tableau des classes -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-700">
                <thead class="bg-dark-750">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Classe
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Niveau
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Effectif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Professeur principal
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Année scolaire
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-dark-800 divide-y divide-dark-700">
                    @forelse($classes as $classe)
                        <tr class="hover:bg-dark-750 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-primary-500 flex items-center justify-center text-white">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white">{{ $classe->nom }}</div>
                                        <div class="text-xs text-gray-400">{{ $classe->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $classe->niveau->nom }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                <div class="flex items-center">
                                    <span class="text-white font-medium">{{ $classe->eleves_count ?? 0 }}</span>
                                    <span class="text-gray-400 ml-1">élèves</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($classe->professeurPrincipal)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs">
                                            {{ substr($classe->professeurPrincipal->user->prenom, 0, 1) }}{{ substr($classe->professeurPrincipal->user->nom, 0, 1) }}
                                        </div>
                                        <div class="ml-2">
                                            <div class="text-sm text-white">{{ $classe->professeurPrincipal->user->prenom }} {{ $classe->professeurPrincipal->user->nom }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Non défini</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $classe->annee_scolaire }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.classes.show', $classe->id) }}" class="text-blue-400 hover:text-blue-300 mr-2" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.classes.edit', $classe->id) }}" class="text-yellow-400 hover:text-yellow-300 mr-2" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.classes.destroy', $classe->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-chalkboard text-4xl mb-2 block"></i>
                                <span class="text-lg">Aucune classe trouvée</span>
                                <p class="mt-2 text-sm">Créez votre première classe pour commencer</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($classes->hasPages())
            <div class="px-6 py-4 border-t border-dark-700">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des filtres
        const searchInput = document.getElementById('search');
        const filterNiveau = document.getElementById('filter-niveau');
        const filterAnnee = document.getElementById('filter-annee');
        
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const niveauId = filterNiveau.value;
            const annee = filterAnnee.value;
            
            document.querySelectorAll('tbody tr').forEach(row => {
                const nomClasse = row.querySelector('td:first-child').textContent.toLowerCase();
                const niveau = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const anneeScolaire = row.querySelector('td:nth-child(5)').textContent.trim();
                
                const rowNiveauId = row.getAttribute('data-niveau-id') || '';
                
                const matchesSearch = nomClasse.includes(searchTerm);
                const matchesNiveau = !niveauId || rowNiveauId === niveauId;
                const matchesAnnee = !annee || anneeScolaire.startsWith(annee);
                
                if (matchesSearch && matchesNiveau && matchesAnnee) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', applyFilters);
        filterNiveau.addEventListener('change', applyFilters);
        filterAnnee.addEventListener('change', applyFilters);
    });
</script>
@endpush

@endsection
