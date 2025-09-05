@extends('layouts.admin')

@section('title', 'Tableau de bord')

@push('admin-styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        .fc-event {
            cursor: pointer;
            border: none;
            font-size: 0.8em;
            padding: 2px 4px;
        }
        .fc-daygrid-event {
            white-space: normal !important;
            margin: 2px 0;
        }
        .fc-toolbar-title {
            font-size: 1.2em;
        }
        .stat-card {
            transition: transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('page-title', 'Tableau de bord')

@push('header-actions')
    <div class="flex space-x-2">
        <button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i>Nouvel événement
        </button>
        <a href="{{ route('admin.dashboard.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-file-export mr-2"></i>Exporter
        </a>
    </div>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Carte des élèves -->
        <div class="bg-white rounded-lg shadow p-6 stat-card hover:shadow-md">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Élèves</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $counts['eleves'] ?? 0 }}</p>
            </div>
        </div>
    </div>

        <!-- Carte des professeurs -->
        <div class="bg-white rounded-lg shadow p-6 stat-card hover:shadow-md">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Professeurs</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $counts['professeurs'] ?? 0 }}</p>
            </div>
        </div>
    </div>

        <!-- Carte des classes -->
        <div class="bg-white rounded-lg shadow p-6 stat-card hover:shadow-md">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-chalkboard text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Classes</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $counts['classes'] ?? 0 }}</p>
            </div>
        </div>
    </div>

        <!-- Carte des paiements ce mois-ci -->
        <div class="bg-white rounded-lg shadow p-6 stat-card hover:shadow-md">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Paiements ({{ now()->translatedFormat('F') }})</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['paiements_mois'] ?? 0, 2, ',', ' ') }} MAD</p>
            </div>
        </div>
    </div>

    <!-- Tableaux de données -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Derniers paiements -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Derniers paiements</h3>
                    <a href="{{ route('admin.paiements.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700">
                    @forelse($recentPaiements as $paiement)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $paiement->eleve->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $paiement->eleve->classe->nom ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $paiement->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-white">
                                {{ number_format($paiement->montant, 2, ',', ' ') }} MAD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $paiement->date_paiement->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-400">
                                Aucun paiement récent
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

        <!-- Dernières absences -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Dernières absences</h3>
                    <a href="{{ route('admin.absences.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-4 space-y-4">
            @forelse($recentAbsences as $absence)
                <div class="flex items-start p-4 rounded-lg bg-white border border-gray-200">
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

    <!-- Calendrier des événements -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Calendrier des événements</h3>
                <div class="flex space-x-2">
                    <button @click="showEventModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>Nouvel événement
                    </button>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div id="calendar" class="h-96"></div>
        </div>
    </div>

    <!-- Modal pour ajouter un événement -->
    <div x-data="{ showEventModal: false }" x-cloak>
        <!-- Modal Backdrop -->
        <div x-show="showEventModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEventModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="showEventModal = false"
                     aria-hidden="true">
                </div>

                <!-- Modal Content -->
                <div x-show="showEventModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Nouvel événement
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="event-title" class="block text-sm font-medium text-gray-700">Titre</label>
                                        <input type="text" id="event-title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="event-date" class="block text-sm font-medium text-gray-700">Date</label>
                                        <input type="date" id="event-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="event-description" class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea id="event-description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Enregistrer
                        </button>
                        <button @click="showEventModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('admin-scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le calendrier
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            firstDay: 1, // Lundi comme premier jour de la semaine
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour'
            },
            events: @json($evenements),
            eventClick: function(info) {
                // Afficher les détails de l'événement
                const event = info.event;
                Swal.fire({
                    title: event.title,
                    html: `
                        <p class="text-gray-600">${event.extendedProps.description || 'Aucune description'}</p>
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="far fa-calendar-alt mr-1"></i> 
                            ${event.start.toLocaleString('fr-FR', { 
                                weekday: 'long', 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}
                        </p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Modifier',
                    cancelButtonText: 'Fermer',
                    showDenyButton: true,
                    denyButtonText: 'Supprimer',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    denyButtonColor: '#ef4444'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Logique pour modifier l'événement
                        console.log('Modifier', event.id);
                    } else if (result.isDenied) {
                        // Logique pour supprimer l'événement
                        console.log('Supprimer', event.id);
                    }
                });
                
                info.jsEvent.preventDefault();
            },
            eventContent: function(arg) {
                const event = arg.event;
                const timeHtml = event.allDay ? '' : `<div class="fc-event-time">${arg.timeText}</div>`;
                
                return {
                    html: `
                        <div class="fc-event-main-frame">
                            ${timeHtml}
                            <div class="fc-event-title">${event.title}</div>
                        </div>
                    `
                };
            },
            eventClassNames: function(arg) {
                // Ajouter des classes CSS personnalisées en fonction du type d'événement
                const event = arg.event;
                const classes = [];
                
                if (event.extendedProps && event.extendedProps.type) {
                    classes.push(`event-type-${event.extendedProps.type}`);
                }
                
                return classes;
            },
            datesSet: function(dateInfo) {
                // Charger les événements de manière dynamique lors du changement de vue/date
                console.log('Chargement des événements pour:', dateInfo.view.type, dateInfo.start, dateInfo.end);
                // Implémenter le chargement AJAX si nécessaire
            }
        });
        
        // Rendre le calendrier
        calendar.render();
        
        // Gestion du formulaire d'ajout d'événement
        document.addEventListener('DOMContentLoaded', function() {
            const eventForm = document.getElementById('event-form');
            if (eventForm) {
                eventForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const title = document.getElementById('event-title').value;
                    const date = document.getElementById('event-date').value;
                    const description = document.getElementById('event-description').value;
                    
                    // Ici, vous pouvez ajouter la logique pour enregistrer l'événement
                    console.log('Nouvel événement:', { title, date, description });
                    
                    // Fermer la modal après soumission
                    Alpine.store('modal').close();
                });
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* FullCalendar Styling */
    .fc {
        color: #1f2937;
    }
    .fc .fc-toolbar-title {
        font-size: 1.25em;
        margin: 0;
        color: #1f2937;
    }
    .fc .fc-button {
        background-color: #3b82f6;
        border: none;
        padding: 0.4em 0.8em;
        font-size: 0.9em;
        color: white;
    }
    .fc .fc-button:hover {
        background-color: #2563eb;
    }
    .fc .fc-button-active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #1d4ed8;
    }
    
    /* Table Styling */
    .table-container {
        max-height: 400px;
        overflow-y: auto;
    }
    
    /* Card Hover Effects */
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    } 
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
