@extends('layouts.admin')

@section('title', 'Gestion des élèves')

@section('content')
<x-card title="Liste des élèves">
    <x-slot name="header">
        <div class="flex justify-end">
            <x-button href="{{ route('admin.eleves.create') }}" icon="fas fa-plus">
                Ajouter un élève
            </x-button>
        </div>
    </x-slot>

    <!-- Filtres -->
    <x-card variant="secondary" no-padding class="mb-6">
        <form action="{{ route('admin.eleves.index') }}" method="GET" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Recherche -->
                <x-form.input 
                    name="search" 
                    label="Recherche"
                    :value="request('search')"
                    placeholder="Nom, prénom, CNE..."
                />
                
                <!-- Filtre par classe -->
                <x-form.select 
                    name="classe_id" 
                    label="Classe"
                    :options="$classes->mapWithKeys(fn($classe) => [$classe->id => $classe->nom . ' - ' . $classe->niveau->nom])"
                    :selected="request('classe_id')"
                    placeholder="Toutes les classes"
                />
                
                <!-- Filtre par statut -->
                <x-form.select 
                    name="status" 
                    label="Statut"
                    :options="[
                        'actif' => 'Actif',
                        'inactif' => 'Inactif',
                        'abandonne' => 'Abandonné',
                        'diplome' => 'Diplômé'
                    ]"
                    :selected="request('status')"
                    placeholder="Tous les statuts"
                />
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 mt-2 border-t border-gray-700">
                <x-button 
                    href="{{ route('admin.eleves.index') }}" 
                    variant="secondary"
                    type="button"
                >
                    Réinitialiser
                </x-button>
                <x-button type="submit" icon="fas fa-filter">
                    Filtrer
                </x-button>
            </div>
        </form>
    </x-card>

    @php
        $headers = ['CNE', 'Nom & Prénom', 'Classe', 'Téléphone', 'Inscription', 'Statut'];
        
        $rows = $eleves->map(function($eleve) {
            $statusClasses = [
                'actif' => 'bg-green-100 text-green-800',
                'inactif' => 'bg-yellow-100 text-yellow-800',
                'abandonne' => 'bg-red-100 text-red-800',
                'diplome' => 'bg-blue-100 text-blue-800',
            ][$eleve->statut] ?? 'bg-gray-100 text-gray-800';
            
            $classeBadge = $eleve->classe 
                ? "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800'>" . 
                  htmlspecialchars($eleve->classe->nom . ' - ' . $eleve->classe->niveau->nom) . 
                  "</span>"
                : "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800'>Non assigné</span>";
            
            $statusBadge = "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$statusClasses}'>" . 
                          ucfirst($eleve->statut) . 
                          "</span>";
            
            $actions = "
                <div class='flex items-center justify-end space-x-2'>
                    <a href='" . route('admin.eleves.show', $eleve) . "' class='text-blue-400 hover:text-blue-300' title='Voir'>
                        <i class='fas fa-eye'></i>
                    </a>
                    <a href='" . route('admin.eleves.edit', $eleve) . "' class='text-yellow-400 hover:text-yellow-300' title='Modifier'>
                        <i class='fas fa-edit'></i>
                    </a>
                    <form action='" . route('admin.eleves.destroy', $eleve) . "' method='POST' class='inline' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer cet élève ?\")'>
                        @csrf
                        @method('DELETE')
                        <button type='submit' class='text-red-400 hover:text-red-300' title='Supprimer'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </form>
                </div>
            ";
            
            return [
                'id' => $eleve->id,
                'cne' => $eleve->cne,
                'name' => "
                    <div class='flex items-center'>
                        <div class='flex-shrink-0 h-10 w-10 rounded-full bg-dark-600 flex items-center justify-center'>
                            <span class='text-gray-300'>" . substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1) . "</span>
                        </div>
                        <div class='ml-4'>
                            <div class='text-sm font-medium text-white'>{$eleve->nom} {$eleve->prenom}</div>
                            <div class='text-xs text-gray-400'>{$eleve->email}</div>
                        </div>
                    </div>
                ",
                'classe' => $classeBadge,
                'telephone' => $eleve->telephone ?? 'N/A',
                'inscription' => $eleve->date_inscription ? $eleve->date_inscription->format('d/m/Y') : 'N/A',
                'statut' => $statusBadge,
                'actions' => $actions,
            ];
        });
    @endphp
    
    <x-table 
        :headers="$headers" 
        :rows="$rows->toArray()" 
        :empty-message="'Aucun élève trouvé'"
        :empty-description="'Commencez par ajouter un nouvel élève'"
        :empty-icon="'fas fa-user-graduate'"
        empty-action-href="{{ route('admin.eleves.create') }}"
        empty-action-text="Ajouter un élève"
        empty-action-icon="fas fa-plus"
        action-column
        hover
        striped
    >
        <x-slot name="emptyAction">
            <x-button href="{{ route('admin.eleves.create') }}" icon="fas fa-plus" size="sm">
                Ajouter un élève
            </x-button>
        </x-slot>
    </x-table>

    <!-- Pagination -->
    @if($eleves->hasPages())
        <div class="mt-6">
            {{ $eleves->withQueryString()->links() }}
        </div>
    @endif
</x-card>

@push('scripts')
<script>
    // Script pour le chargement dynamique des élèves par classe
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reset form when clicking reset button
        document.querySelector('button[type="reset"]')?.addEventListener('click', function() {
            window.location.href = '{{ route("admin.eleves.index") }}';
        });
    });
</script>
@endpush
@endsection
