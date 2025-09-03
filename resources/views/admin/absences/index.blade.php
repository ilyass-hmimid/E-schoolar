@extends('layouts.admin')

@section('title', 'Gestion des absences')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .status-badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
    .status-justified {
        @apply bg-green-100 text-green-800;
    }
    .status-unjustified {
        @apply bg-red-100 text-red-800;
    }
    .status-validated {
        @apply bg-blue-100 text-blue-800;
    }
    .action-btn {
        @apply inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2;
    }
    .btn-view {
        @apply bg-blue-600 hover:bg-blue-700 focus:ring-blue-500;
    }
    .btn-edit {
        @apply bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500;
    }
    .btn-justify {
        @apply bg-green-600 hover:bg-green-700 focus:ring-green-500;
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
            <h1 class="text-2xl font-bold text-gray-900">Gestion des absences</h1>
            <p class="text-sm text-gray-500 mt-1">Consultez et gérez les absences des étudiants</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <a href="{{ route('admin.absences.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nouvelle absence
            </a>
            <a href="{{ route('admin.absences.export') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter
            </a>
            <button type="button" id="importBtn" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Importer
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form action="{{ route('admin.absences.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Étudiant -->
                <div>
                    <label for="etudiant" class="block text-sm font-medium text-gray-700 mb-1">Étudiant</label>
                    <input type="text" name="etudiant" id="etudiant" 
                           value="{{ request('etudiant') }}" 
                           placeholder="Nom ou prénom"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <!-- Matière -->
                <div>
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
                
                <!-- Type d'absence -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                        <option value="absence" {{ request('type') == 'absence' ? 'selected' : '' }}>Absence</option>
                        <option value="retard" {{ request('type') == 'retard' ? 'selected' : '' }}>Retard</option>
                    </select>
                </div>
                
                <!-- Statut de justification -->
                <div>
                    <label for="statut_justification" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut_justification" id="statut_justification" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut_justification') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="acceptee" {{ request('statut_justification') == 'acceptee' ? 'selected' : '' }}>Acceptée</option>
                        <option value="refusee" {{ request('statut_justification') == 'refusee' ? 'selected' : '' }}>Refusée</option>
                    </select>
                </div>
                
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
                
                <!-- Classe -->
                <div>
                    <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                    <select name="classe_id" id="classe_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Justifiée -->
                <div class="flex items-end">
                    <div class="flex items-center h-5">
                        <input id="justifiee" name="justifiee" type="checkbox" 
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                               {{ request('justifiee') ? 'checked' : '' }}>
                    </div>
                    <label for="justifiee" class="ml-2 block text-sm text-gray-700">
                        Uniquement les absences justifiées
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('admin.absences.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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

    <!-- Tableau des absences -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Liste des absences</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Affichage de {{ $absences->firstItem() }} à {{ $absences->lastItem() }} sur {{ $absences->total() }} résultats
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
                                    Élève
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'etudiant', 'direction' => request('sort') === 'etudiant' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="ml-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    Matière
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'matiere', 'direction' => request('sort') === 'matiere' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="ml-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    Date
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => request('sort') === 'date' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="ml-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($absences as $absence)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="{{ $absence->eleve->photo_url ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $absence->eleve->nom_complet ?? 'Élève' }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $absence->eleve->nom_complet ?? 'Élève inconnu' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $absence->eleve->classe->nom ?? 'Classe non définie' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $absence->cours->matiere->nom ?? 'Matière inconnue' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $absence->cours->professeur->nom_complet ?? 'Professeur non défini' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $absence->date_absence->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $absence->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($absence->justifiee)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Justifiée
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Non justifiée
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.absences.edit', $absence) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Aucune absence enregistrée pour le moment.
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
                            Affichage de 
                            <span class="font-medium">{{ $absences->firstItem() }}</span>
                            à 
                            <span class="font-medium">{{ $absences->lastItem() }}</span>
                            sur 
                            <span class="font-medium">{{ $absences->total() }}</span>
                            résultats
                        </p>
                    </div>
                    <div>
                        {{ $absences->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de justification -->
    <div x-data="{ 
        show: false, 
        absenceId: null,
        justification: '',
        pieceJointe: null,
        loading: false,
        errors: { justification: '', piece_jointe: '' },
        open(id) { 
            this.absenceId = id; 
            this.show = true; 
            this.resetForm();
        },
        close() { 
            this.show = false; 
            this.resetForm();
        },
        resetForm() {
            this.justification = '';
            this.pieceJointe = null;
            this.errors = { justification: '', piece_jointe: '' };
            // Réinitialiser le champ de fichier
            const fileInput = document.getElementById('piece_jointe');
            if (fileInput) fileInput.value = '';
        },
        async submit() {
            this.loading = true;
            this.errors = { justification: '', piece_jointe: '' };
            
            const formData = new FormData();
            formData.append('justification', this.justification);
            if (this.pieceJointe) {
                formData.append('piece_jointe', this.pieceJointe);
            }
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            
            try {
                const response = await fetch(`/admin/absences/${this.absenceId}/justifier`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    if (response.status === 422) {
                        // Erreurs de validation
                        Object.keys(data.errors).forEach(key => {
                            this.errors[key] = data.errors[key][0];
                        });
                    } else {
                        throw new Error(data.message || 'Une erreur est survenue');
                    }
                } else {
                    // Succès
                    this.close();
                    window.location.reload();
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la justification de l\'absence');
            } finally {
                this.loading = false;
            }
        },
        onFileChange(event) {
            this.pieceJointe = event.target.files[0];
            this.errors.piece_jointe = '';
        }
    }"
    x-on:open-justify-modal.window="open($event.detail.id)">
        <!-- Overlay -->
        <div 
            x-show="show"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close"
            aria-hidden="true">
        </div>
        
        <!-- Modal -->
        <div 
            x-show="show"
            class="fixed inset-0 z-10 overflow-y-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Justifier une absence
                            </h3>
                            <div class="mt-2">
                                <div class="mt-4">
                                    <label for="justification" class="block text-sm font-medium text-gray-700 text-left">
                                        Justification <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <textarea 
                                            id="justification" 
                                            x-model="justification"
                                            rows="3" 
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                                            placeholder="Veuillez indiquer la raison de cette absence"></textarea>
                                        <p class="mt-1 text-sm text-red-600" x-text="errors.justification"></p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Pièce jointe (optionnel)
                                    </label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="piece_jointe" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                                    <span>Téléverser un fichier</span>
                                                    <input 
                                                        id="piece_jointe" 
                                                        name="piece_jointe" 
                                                        type="file" 
                                                        class="sr-only"
                                                        @change="onFileChange($event)">
                                                </label>
                                                <p class="pl-1">ou glisser-déposer</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PDF, JPG, PNG jusqu'à 5MB
                                            </p>
                                            <p x-show="pieceJointe" class="text-sm text-gray-900 mt-2">
                                                <svg class="inline-block h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span x-text="pieceJointe ? pieceJointe.name : ''"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-red-600" x-text="errors.piece_jointe"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button 
                            type="button" 
                            @click="submit()"
                            :disabled="loading || !justification"
                            :class="{ 'opacity-50 cursor-not-allowed': loading || !justification, 'hover:bg-blue-700': !loading && justification }"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                            <span x-show="!loading">Enregistrer</span>
                            <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            @click="close()"
                            :disabled="loading"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Gestion des événements pour les modaux
        Alpine.data('modal', () => ({
            openModal(event) {
                const modal = document.getElementById(event.detail.id);
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.setAttribute('aria-hidden', 'false');
                }
            },
            closeModal(event) {
                const modal = event.target.closest('.modal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');
                }
            }
        }));

        // Gestion des confirmations de suppression
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm(this.getAttribute('data-confirm'))) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                return true;
            });
        });

        // Initialisation des tooltips avec Tippy.js si disponible
        if (typeof tippy === 'function') {
            tippy('[data-tippy-content]', {
                allowHTML: true,
                animation: 'scale-extreme',
                theme: 'light-border',
                arrow: true,
                delay: [100, 0],
                duration: [200, 150],
                interactive: true,
                appendTo: document.body,
                placement: 'top',
                offset: [0, 10]
            });
        }

        // Gestion des tris de colonnes
        document.querySelectorAll('[data-sort]').forEach(header => {
            header.addEventListener('click', function() {
                const sortField = this.getAttribute('data-sort');
                const currentUrl = new URL(window.location.href);
                const currentSort = currentUrl.searchParams.get('sort');
                const currentDirection = currentUrl.searchParams.get('direction');
                
                let newDirection = 'asc';
                if (currentSort === sortField) {
                    newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                }
                
                currentUrl.searchParams.set('sort', sortField);
                currentUrl.searchParams.set('direction', newDirection);
                
                window.location.href = currentUrl.toString();
            });
        });

        // Gestion des filtres avancés
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            // Réinitialisation des filtres
            document.getElementById('reset-filters').addEventListener('click', function() {
                const url = new URL(window.location.href);
                window.location.href = url.pathname;
            });

            // Soumission du formulaire lors du changement des sélecteurs
            document.querySelectorAll('#filter-form select, #filter-form input[type="checkbox"]').forEach(element => {
                element.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }
    });

    // Fonction pour afficher une notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Afficher les messages flash s'ils existent
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
</script>
@endpush

@endsection
