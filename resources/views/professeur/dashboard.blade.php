@extends('layouts.app')

@section('title', 'Tableau de bord - Professeur')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Tableau de bord - Professeur
        </h1>
        <div class="text-sm text-gray-500">
            {{ now()->translatedFormat('l d F Y') }}
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Carte Mes Classes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                    <i class="fas fa-chalkboard text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Mes Classes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_classes'] }}</p>
                </div>
            </div>
        </div>

        <!-- Carte Élèves -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Élèves</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_eleves'] }}</p>
                </div>
            </div>
        </div>

        <!-- Carte Cours Aujourd'hui -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Cours Ce Mois</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['cours_ce_mois'] }}</p>
                </div>
            </div>
        </div>

        <!-- Carte Notes à Remplir -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-50 text-red-600 mr-4">
                    <i class="fas fa-tasks text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Devoirs en attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['devoirs_en_attente'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Prochains Cours -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mes prochains cours</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaire</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salle</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prochains_cours as $cours)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($cours->date_debut)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($cours->date_fin)->format('H:i') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($cours->date_debut)->locale('fr')->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $cours->classe->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $cours->matiere->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $cours->salle ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('cours.show', $cours->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</a>
                            <a href="{{ route('notes.create', ['cours_id' => $cours->id]) }}" class="text-indigo-600 hover:text-indigo-900">Notes</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucun cours à venir cette semaine
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dernières Notes Ajoutées -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dernières notes ajoutées</h3>
            <div class="space-y-4">
                @forelse($dernieres_notes as $note)
                <div class="border-b pb-3 mb-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $note->matiere->nom }} - {{ $note->eleve->name }}</p>
                            <p class="text-sm text-gray-500">{{ $note->commentaire ?? 'Aucun commentaire' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-blue-600">{{ $note->valeur }}/20</p>
                            <p class="text-xs text-gray-500">Ajouté le {{ $note->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">Aucune note récente</p>
                @endforelse
            </div>
            <div class="mt-4 text-right">
                <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Voir toutes les notes</a>
            </div>
        </div>

        <!-- Absences Récentes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Absences récentes</h3>
            <div class="space-y-4">
                @forelse($absences_non_justifiees as $absence)
                <div class="flex items-center justify-between p-3 {{ $absence->est_justifiee ? 'bg-gray-50' : 'bg-red-50' }} rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $absence->eleve->name }}</p>
                        <p class="text-sm text-gray-500">{{ $absence->eleve->classe->nom ?? 'Sans classe' }} - {{ $absence->matiere->nom }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ $absence->date_absence->format('d/m/Y') }}</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $absence->est_justifiee ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $absence->est_justifiee ? 'Justifiée' : 'Non justifiée' }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 p-3">Aucune absence récente</p>
                @endforelse
            </div>
            <div class="mt-4 text-right">
                <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Voir toutes les absences</a>
            </div>
        </div>
    </div>
@endsection
