@extends('layouts.admin')

@section('title', 'Gestion des assistants')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Gestion des assistants</h1>
            <p class="text-gray-400">Liste des assistants enregistrés dans le système</p>
        </div>
        <a href="{{ route('admin.assistants.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i> Ajouter un assistant
        </a>
    </div>

    <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
        <!-- En-tête du tableau -->
        <div class="px-6 py-4 border-b border-dark-700">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-500"></i>
                        </div>
                        <input type="text" id="search" class="bg-dark-700 border-0 text-white rounded-lg pl-10 pr-4 py-2 w-full focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Rechercher un assistant...">
                    </div>
                </div>
                <div class="ml-4">
                    <select id="filter-centre" class="bg-dark-700 border-0 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">Tous les centres</option>
                        @foreach(\App\Models\Centre::all() as $centre)
                            <option value="{{ $centre->id }}">{{ $centre->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Corps du tableau -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-700">
                <thead class="bg-dark-750">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Nom & Prénom
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Téléphone
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Centre
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-dark-800 divide-y divide-dark-700">
                    @forelse($assistants as $assistant)
                        <tr class="hover:bg-dark-750 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center">
                                        <span class="text-white font-medium">{{ substr($assistant->prenom, 0, 1) }}{{ substr($assistant->nom, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white">{{ $assistant->prenom }} {{ $assistant->nom }}</div>
                                        <div class="text-xs text-gray-400">Inscrit le {{ $assistant->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $assistant->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $assistant->telephone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $assistant->centre->nom ?? 'Non défini' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.assistants.show', $assistant->id) }}" class="text-blue-400 hover:text-blue-300 mr-2" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.assistants.edit', $assistant->id) }}" class="text-yellow-400 hover:text-yellow-300 mr-2" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.assistants.destroy', $assistant->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet assistant ?')">
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users-slash text-4xl mb-2 block"></i>
                                <span class="text-lg">Aucun assistant trouvé</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assistants->hasPages())
            <div class="px-6 py-4 border-t border-dark-700">
                {{ $assistants->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de la recherche en temps réel
        const searchInput = document.getElementById('search');
        const filterCentre = document.getElementById('filter-centre');
        
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const centreId = filterCentre.value;
            
            document.querySelectorAll('tbody tr').forEach(row => {
                const name = row.querySelector('td:first-child').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const rowCentreId = row.getAttribute('data-centre-id') || '';
                
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesCentre = !centreId || rowCentreId === centreId;
                
                if (matchesSearch && matchesCentre) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', applyFilters);
        filterCentre.addEventListener('change', applyFilters);
    });
</script>
@endpush

@endsection
