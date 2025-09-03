@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Carte des élèves -->
    <div class="bg-dark-800 rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-500/20 text-blue-400">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-400">Élèves</p>
                <p class="text-2xl font-semibold text-white">{{ $counts['eleves'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Carte des professeurs -->
    <div class="bg-dark-800 rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-500/20 text-green-400">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-400">Professeurs</p>
                <p class="text-2xl font-semibold text-white">{{ $counts['professeurs'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Carte des classes -->
    <div class="bg-dark-800 rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-500/20 text-purple-400">
                <i class="fas fa-chalkboard text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-400">Classes</p>
                <p class="text-2xl font-semibold text-white">{{ $counts['classes'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Carte des paiements ce mois-ci -->
    <div class="bg-dark-800 rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-500/20 text-yellow-400">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-400">Paiements ({{ now()->translatedFormat('F') }})</p>
                <p class="text-2xl font-semibold text-white">{{ number_format($stats['paiements_mois'] ?? 0, 2, ',', ' ') }} MAD</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Derniers paiements -->
    <div class="bg-dark-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-white">Derniers paiements</h3>
            <a href="{{ route('admin.paiements.index') }}" class="text-sm text-primary-500 hover:text-primary-400">
                Voir tout
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-700">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Élève</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700">
                    @forelse($recentPaiements as $paiement)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $paiement->eleve->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $paiement->eleve->classe->nom ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $paiement->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-white">
                                {{ number_format($paiement->montant, 2, ',', ' ') }} MAD
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400">
                                {{ $paiement->date_paiement->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-400">
                                Aucun paiement récent
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dernières absences -->
    <div class="bg-dark-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-white">Dernières absences</h3>
            <a href="{{ route('admin.absences.index') }}" class="text-sm text-primary-500 hover:text-primary-400">
                Voir tout
            </a>
        </div>
        
        <div class="space-y-4">
            @forelse($recentAbsences as $absence)
                <div class="flex items-start p-3 rounded-lg bg-dark-700">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-500/20 flex items-center justify-center text-red-400">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-white">
                            {{ $absence->eleve->user->name ?? 'Élève inconnu' }}
                            <span class="text-gray-400 text-xs ml-2">{{ $absence->date_absence->diffForHumans() }}</span>
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ $absence->matiere->nom ?? 'Matière non spécifiée' }} - 
                            {{ $absence->heure_debut->format('H:i') }} - {{ $absence->heure_fin->format('H:i') }}
                        </div>
                        @if($absence->justificatif)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                <i class="fas fa-file-alt mr-1"></i> Justifié
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-gray-400">
                    Aucune absence récente
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Calendrier des événements -->
<div class="bg-dark-800 rounded-lg shadow-lg p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-white">Calendrier des événements</h3>
        <div class="flex space-x-2">
            <button class="px-3 py-1 rounded-lg bg-primary-600 text-white text-sm">
                <i class="fas fa-plus mr-1"></i> Nouvel événement
            </button>
        </div>
    </div>
    
    <div id="calendar" class="h-96">
        <!-- Le calendrier sera initialisé par JavaScript -->
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            themeSystem: 'bootstrap5',
            events: @json($evenements),
            eventContent: function(arg) {
                return {
                    html: `
                        <div class="fc-event-main">
                            <div class="fc-event-title">${arg.event.title}</div>
                            <div class="fc-event-time">${arg.timeText}</div>
                        </div>
                    `
                };
            }
        });
        calendar.render();
    });
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    .fc {
        color: #e5e7eb;
    }
    .fc .fc-toolbar-title {
        color: #fff;
    }
    .fc .fc-button {
        background-color: #374151;
        border-color: #4b5563;
        color: #e5e7eb;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    .fc .fc-daygrid-day.fc-day-today {
        background-color: rgba(99, 102, 241, 0.1);
    }
    .fc .fc-daygrid-day-number {
        color: #e5e7eb;
    }
    .fc .fc-col-header-cell {
        background-color: #1f2937;
    }
    .fc .fc-col-header-cell-cushion {
        color: #9ca3af;
    }
    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        color: #4f46e5;
        font-weight: bold;
    }
</style>
@endpush
@endsection
