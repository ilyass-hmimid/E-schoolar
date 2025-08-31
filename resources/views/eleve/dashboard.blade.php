@extends('layouts.app')

@section('title', 'Tableau de bord - Élève')

@section('header')
<div class="flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">
        Tableau de bord - Élève
    </h1>
    <div class="text-sm text-gray-500">
        {{ $aujourdhui }}
    </div>
</div>
@endsection

@section('content')
<!-- Statistiques rapides -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Carte Moyenne Générale -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Moyenne Générale</p>
                <p class="text-2xl font-bold text-gray-900">{{ $moyenne ?? 'N/A' }}<span class="text-sm text-gray-500">/20</span></p>
            </div>
        </div>
    </div>

    <!-- Carte Prochains Devoirs -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Devoirs à rendre</p>
                <p class="text-2xl font-bold text-gray-900">{{ count($prochains_devoirs) }}</p>
            </div>
        </div>
    </div>

    <!-- Carte Prochains Cours -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Cours aujourd'hui</p>
                <p class="text-2xl font-bold text-gray-900">{{ count($prochains_cours->filter(function($cours) { return \Carbon\Carbon::parse($cours->date_debut)->isToday(); })) }}</p>
            </div>
        </div>
    </div>

    <!-- Carte Absences -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-50 text-red-600 mr-4">
                <i class="fas fa-user-times text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Absences non justifiées</p>
                <p class="text-2xl font-bold text-gray-900">{{ count($absences_non_justifiees) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Prochains cours -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Prochains cours</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($prochains_cours as $presence)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">
                                    {{ $presence->matiere->nom ?? 'Matière non définie' }}
                                    @if($presence->classe)
                                        <span class="text-sm font-normal text-gray-500">({{ $presence->classe->nom ?? '' }})</span>
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $presence->date_seance->format('d/m/Y') }} • 
                                    {{ $presence->heure_debut }} - {{ $presence->heure_fin }}
                                </p>
                                @if($presence->professeur)
                                    <p class="mt-1 text-sm text-gray-500">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i>
                                        {{ $presence->professeur->name }}
                                    </p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($presence->statut === 'present') bg-green-100 text-green-800
                                @elseif($presence->statut === 'absent') bg-red-100 text-red-800
                                @elseif($presence->statut === 'retard') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $presence->statut_libelle }}
                            </span>
                        </div>
                        @if($presence->commentaire)
                            <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                {{ $presence->commentaire }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Aucun cours prévu pour le moment.
                    </div>
                @endforelse
            </div>
            @if(count($prochains_cours) > 0)
                <div class="px-6 py-3 bg-gray-50 text-right text-sm">
                    <a href="{{ route('eleve.cours.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Voir tout le planning
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Dernières notes -->
    <div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Dernières notes</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($dernieres_notes as $note)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $note->matiere->nom ?? 'Matière non définie' }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $note->titre ?? 'Sans titre' }}
                                    @if($note->date_evaluation)
                                        • {{ \Carbon\Carbon::parse($note->date_evaluation)->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $note->valeur >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ number_format($note->valeur, 2) }}/20
                            </span>
                        </div>
                        @if($note->commentaire)
                            <p class="mt-2 text-sm text-gray-600">{{ $note->commentaire }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Aucune note disponible pour le moment.
                    </div>
                @endforelse
            </div>
            @if(count($dernieres_notes) > 0)
                <div class="px-6 py-3 bg-gray-50 text-right text-sm">
                    <a href="{{ route('eleve.notes.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Voir toutes les notes
                    </a>
                </div>
            @endif
        </div>

        <!-- Prochains devoirs -->
        <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Devoirs à rendre</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($prochains_devoirs as $devoir)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $devoir->titre }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $devoir->matiere->nom ?? 'Matière non définie' }}
                                    • À rendre pour le {{ \Carbon\Carbon::parse($devoir->date_limite)->format('d/m/Y') }}
                                </p>
                            </div>
                            @if($devoir->date_limite->isToday())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Aujourd'hui
                                </span>
                            @elseif($devoir->date_limite->isPast())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    En retard
                                </span>
                            @endif
                        </div>
                        @if($devoir->description)
                            <p class="mt-2 text-sm text-gray-600">{{ Str::limit($devoir->description, 100) }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Aucun devoir à rendre pour le moment.
                    </div>
                @endforelse
            </div>
            @if(count($prochains_devoirs) > 0)
                <div class="px-6 py-3 bg-gray-50 text-right text-sm">
                    <a href="{{ route('eleve.devoirs.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Voir tous les devoirs
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Section des absences -->
@if(count($absences_non_justifiees) > 0)
<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Absences à justifier
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Vous avez des absences non justifiées à votre emploi du temps.
        </p>
    </div>
    <div class="border-t border-gray-200">
        <ul class="divide-y divide-gray-200">
            @foreach($absences_non_justifiees as $absence)
                <li class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-red-100 text-red-500">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $absence->matiere->nom ?? 'Matière non définie' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}
                                    • {{ $absence->heure_debut }} - {{ $absence->heure_fin }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('eleve.absences.justifier', $absence) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Justifier
                            </a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@endsection
