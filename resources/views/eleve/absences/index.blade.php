@extends('layouts.eleve')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mes Absences</h1>
        <p class="text-sm text-gray-600 mt-1">Consultez l'historique de vos absences et retards</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm font-medium text-gray-500">Total des absences</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_absences'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm font-medium text-gray-500">Absences justifiées</div>
            <div class="mt-1 text-2xl font-semibold text-green-600">{{ $stats['absences_justifiees'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm font-medium text-gray-500">Absences non justifiées</div>
            <div class="mt-1 text-2xl font-semibold text-red-600">{{ $stats['absences_non_justifiees'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm font-medium text-gray-500">Retards</div>
            <div class="mt-1 text-2xl font-semibold text-yellow-600">{{ $stats['retards'] }}</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filtrer les absences</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('eleve.absences.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="matiere" class="block text-sm font-medium text-gray-700">Matière</label>
                        <select id="matiere" name="matiere_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Toutes les matières</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                    {{ $matiere->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Tous les types</option>
                            <option value="absence" {{ request('type') == 'absence' ? 'selected' : '' }}>Absence</option>
                            <option value="retard" {{ request('type') == 'retard' ? 'selected' : '' }}>Retard</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="justifiee" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select id="justifiee" name="justifiee" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Tous les statuts</option>
                            <option value="1" {{ request('justifiee') === '1' ? 'selected' : '' }}>Justifié</option>
                            <option value="0" {{ request('justifiee') === '0' ? 'selected' : '' }}>Non justifié</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Appliquer les filtres
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des absences -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($absences as $absence)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $absence->date_absence->format('d/m/Y') }}
                                </div>
                                @if($absence->heure_debut)
                                    <div class="text-sm text-gray-500">
                                        {{ $absence->heure_debut->format('H:i') }}
                                        @if($absence->heure_fin)
                                            - {{ $absence->heure_fin->format('H:i') }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $absence->matiere->nom ?? 'Non spécifié' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $absence->professeur->name ?? 'Professeur non défini' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($absence->type === 'retard')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Retard
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Absence
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($absence->justifiee)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Justifiée
                                    </span>
                                @elseif($absence->peut_etre_justifiee)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        À justifier
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Non justifiée
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('eleve.absences.show', $absence) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Détails
                                </a>
                                @if($absence->peut_etre_justifiee)
                                    <span class="mx-2 text-gray-300">|</span>
                                    <a href="{{ route('eleve.absences.justifier', $absence) }}" class="text-blue-600 hover:text-blue-900">
                                        Justifier
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucune absence enregistrée pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($absences->hasPages())
            <div class="px-6 py-4 bg-gray-50">
                {{ $absences->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
