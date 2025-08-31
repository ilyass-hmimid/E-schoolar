@extends('layouts.admin')

@section('title', 'Gestion des Salaires')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Salaires</h1>
        <a href="{{ route('admin.salaires.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
            Nouveau Salaire
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form action="{{ route('admin.salaires.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
            <div class="flex-1">
                <label for="mois_periode" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                <input type="month" name="mois_periode" id="mois_periode" value="{{ request('mois_periode') }}" class="w-full rounded-md border-gray-300 shadow-sm">
            </div>
            
            <div class="flex-1">
                <label for="professeur_id" class="block text-sm font-medium text-gray-700 mb-1">Professeur</label>
                <select name="professeur_id" id="professeur_id" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Tous les professeurs</option>
                    @foreach($professeurs as $professeur)
                        <option value="{{ $professeur->id }}" {{ request('professeur_id') == $professeur->id ? 'selected' : '' }}>
                            {{ $professeur->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex-1">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $statut)
                        <option value="{{ $statut['value'] }}" {{ request('statut') == $statut['value'] ? 'selected' : '' }}>
                            {{ $statut['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Tableau des salaires -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professeur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($salaires as $salaire)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($salaire->mois_periode)->format('m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $salaire->professeur->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $salaire->matiere->nom ?? 'Toutes matières' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $salaire->heures_travaillees }}h</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($salaire->montant_total, 2, ',', ' ') }} €</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'paye' => 'bg-green-100 text-green-800',
                                        'annule' => 'bg-red-100 text-red-800',
                                    ][$salaire->statut] ?? 'bg-gray-100 text-gray-800';
                                    $statusLabels = [
                                        'en_attente' => 'En attente',
                                        'paye' => 'Payé',
                                        'annule' => 'Annulé',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabels[$salaire->statut] ?? $salaire->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.salaires.show', $salaire) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                <a href="{{ route('admin.salaires.edit', $salaire) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                @if($salaire->statut === 'en_attente')
                                    <form action="{{ route('admin.salaires.marquer-paye', $salaire) }}" method="POST" class="inline" onsubmit="return confirm('Marquer ce salaire comme payé ?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-green-600 hover:text-green-900">Payer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun salaire trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($salaires->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $salaires->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
