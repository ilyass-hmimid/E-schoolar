@extends('layouts.admin')

@section('title', __('packs.absences.titles.index'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
    <style>
        .badge-justified {
            background-color: #10b981;
        }
        .badge-unjustified {
            background-color: #ef4444;
        }
        .filter-card {
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .filter-header {
            background-color: #f8fafc;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .filter-body {
            padding: 1.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('packs.absences.titles.index') }}</h1>
            <div>
                <a href="{{ route('admin.absences.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('packs.absences.buttons.create') }}
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-file-export me-1"></i> {{ __('packs.absences.buttons.export') }}
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card filter-card mb-4">
            <div class="filter-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-filter me-2"></i>{{ __('packs.absences.filters.title') }}</span>
                <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="filter-body">
                    <form action="{{ route('admin.absences.index') }}" method="GET" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="student" class="form-label small">{{ __('packs.absences.filters.student') }}</label>
                                <select name="eleve_id" id="student" class="form-select form-select-sm">
                                    <option value="">{{ __('packs.absences.filters.all_students') }}</option>
                                    @foreach($eleves as $eleve)
                                        <option value="{{ $eleve->id }}" {{ request('eleve_id') == $eleve->id ? 'selected' : '' }}>
                                            {{ $eleve->prenom }} {{ $eleve->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="classe" class="form-label small">{{ __('packs.absences.filters.class') }}</label>
                                <select name="classe_id" id="classe" class="form-select form-select-sm">
                                    <option value="">{{ __('packs.absences.filters.all_classes') }}</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                            {{ $classe->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_range" class="form-label small">{{ __('packs.absences.filters.date_range') }}</label>
                                <input type="text" class="form-control form-control-sm" id="date_range" name="date_range" 
                                       value="{{ request('date_debut') && request('date_fin') ? request('date_debut') . ' - ' . request('date_fin') : '' }}" 
                                       placeholder="{{ __('packs.absences.filters.select_date_range') }}">
                                <input type="hidden" name="date_debut" id="date_debut" value="{{ request('date_debut') }}">
                                <input type="hidden" name="date_fin" id="date_fin" value="{{ request('date_fin') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label small">{{ __('packs.absences.filters.status') }}</label>
                                <select name="justifiee" id="status" class="form-select form-select-sm">
                                    <option value="">{{ __('packs.absences.filters.all_status') }}</option>
                                    <option value="1" {{ request('justifiee') === '1' ? 'selected' : '' }}>{{ __('packs.absences.status.justified') }}</option>
                                    <option value="0" {{ request('justifiee') === '0' ? 'selected' : '' }}>{{ __('packs.absences.status.unjustified') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-sm btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> {{ __('packs.absences.buttons.apply_filters') }}
                                </button>
                                <a href="{{ route('admin.absences.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> {{ __('packs.absences.buttons.reset_filters') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ __('packs.absences.stats.total') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absences->total() }}</div>
                            </div>
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-clipboard-list text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ __('packs.absences.stats.justified') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['justified'] }}</div>
                            </div>
                            <div class="icon-circle bg-success">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-danger h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    {{ __('packs.absences.stats.unjustified') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['unjustified'] }}</div>
                            </div>
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-times-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ __('packs.absences.stats.rate') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rate'] }}%</div>
                            </div>
                            <div class="icon-circle bg-info">
                                <i class="fas fa-chart-pie text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des absences -->
        <div class="card shadow mb-4">
            <div class="card-body">
                @if($absences->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">{{ __('packs.absences.messages.no_absences') }}</h5>
                        <p class="text-muted">{{ __('packs.absences.messages.no_absences_description') }}</p>
                        <a href="{{ route('admin.absences.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-1"></i> {{ __('packs.absences.buttons.create_first') }}
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover" id="absencesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('packs.absences.fields.date') }}</th>
                                    <th>{{ __('packs.absences.fields.student') }}</th>
                                    <th>{{ __('packs.absences.fields.class') }}</th>
                                    <th>{{ __('packs.absences.fields.course') }}</th>
                                    <th>{{ __('packs.absences.fields.type') }}</th>
                                    <th class="text-center">{{ __('packs.absences.fields.status') }}</th>
                                    <th class="text-end">{{ __('packs.absences.fields.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($absences as $absence)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $absence->date_absence->format('d/m/Y') }}</div>
                                            <div class="small text-muted">{{ $absence->cours->heure_debut ?? '' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $absence->eleve->prenom }} {{ $absence->eleve->nom }}</div>
                                            <div class="small text-muted">{{ $absence->eleve->classe->nom ?? '' }}</div>
                                        </td>
                                        <td>{{ $absence->eleve->classe->nom ?? 'N/A' }}</td>
                                        <td>
                                            <div>{{ $absence->cours->matiere->nom ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $absence->cours->professeur->user->name ?? '' }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $typeClass = [
                                                    'absence' => 'bg-gray-100 text-gray-800',
                                                    'retard' => 'bg-blue-100 text-blue-800',
                                                    'sortie' => 'bg-purple-100 text-purple-800',
                                                ][$absence->type_absence] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="badge {{ $typeClass }} px-2 py-1">
                                                {{ __('packs.absences.types.' . $absence->type_absence) }}
                                            </span>
                                            @if($absence->duree_absence)
                                                <div class="small text-muted mt-1">
                                                    {{ $absence->duree_absence }} min
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($absence->justifiee)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> {{ __('packs.absences.status.justified') }}
                                                </span>
                                                @if($absence->chemin_justificatif)
                                                    <div class="small mt-1">
                                                        <a href="{{ route('admin.absences.justificatif.download', $absence) }}" class="text-primary">
                                                            <i class="fas fa-paperclip me-1"></i> {{ __('packs.absences.buttons.justificatif') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-circle me-1"></i> {{ __('packs.absences.status.unjustified') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.absences.show', $absence) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="{{ __('packs.absences.buttons.show') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.absences.edit', $absence) }}" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="{{ __('packs.absences.buttons.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $absence->id }}"
                                                        title="{{ __('packs.absences.buttons.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $absence->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('packs.absences.confirm.delete_title') }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ __('packs.absences.confirm.delete_message') }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                {{ __('packs.absences.buttons.cancel') }}
                                                            </button>
                                                            <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fas fa-trash me-1"></i> {{ __('packs.absences.buttons.confirm_delete') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            {{ __('packs.absences.messages.showing') }} {{ $absences->firstItem() }} {{ __('packs.absences.messages.to') }} {{ $absences->lastItem() }} {{ __('packs.absences.messages.of') }} {{ $absences->total() }} {{ __('packs.absences.messages.entries') }}
                        </div>
                        <div>
                            {{ $absences->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal d'export -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('packs.absences.export.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.absences.export') }}" method="GET" id="exportForm">
                    <div class="modal-body">
                        <p class="text-muted">{{ __('packs.absences.export.description') }}</p>
                        
                        <div class="mb-3">
                            <label for="exportFormat" class="form-label">{{ __('packs.absences.export.format') }}</label>
                            <select name="format" id="exportFormat" class="form-select">
                                @foreach(__('packs.absences.export.options') as $key => $option)
                                    <option value="{{ $key }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('packs.absences.export.filters_applied') }}:
                            <ul class="mb-0 mt-2">
                                @if(request('eleve_id'))
                                    <li>{{ __('packs.absences.filters.student') }}: {{ $eleves->firstWhere('id', request('eleve_id'))->prenom ?? '' }} {{ $eleves->firstWhere('id', request('eleve_id'))->nom ?? '' }}</li>
                                @endif
                                @if(request('classe_id'))
                                    <li>{{ __('packs.absences.filters.class') }}: {{ $classes->firstWhere('id', request('classe_id'))->nom ?? '' }}</li>
                                @endif
                                @if(request('date_debut') && request('date_fin'))
                                    <li>{{ __('packs.absences.filters.date_range') }}: {{ request('date_debut') }} - {{ request('date_fin') }}</li>
                                @endif
                                @if(request('justifiee') !== null)
                                    <li>{{ __('packs.absences.filters.status') }}: {{ request('justifiee') ? __('packs.absences.status.justified') : __('packs.absences.status.unjustified') }}</li>
                                @endif
                                @if(!request()->any())
                                    <li>{{ __('packs.absences.export.all_records') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('packs.absences.buttons.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary" id="exportButton">
                            <i class="fas fa-file-export me-1"></i> {{ __('packs.absences.export.generate') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        // Initialiser le date range picker
        $(function() {
            // Traductions pour le date range picker
            moment.locale('fr');
            
            // Configuration du date range picker
            $('input[name="date_range"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: "{{ __('packs.absences.buttons.apply') }}",
                    cancelLabel: "{{ __('packs.absences.buttons.cancel') }}",
                    fromLabel: "{{ __('packs.absences.filters.from') }}",
                    toLabel: "{{ __('packs.absences.filters.to') }}",
                    customRangeLabel: "{{ __('packs.absences.filters.custom') }}",
                    daysOfWeek: [
                        "{{ __('packs.absences.days.sun') }}",
                        "{{ __('packs.absences.days.mon') }}",
                        "{{ __('packs.absences.days.tue') }}",
                        "{{ __('packs.absences.days.wed') }}",
                        "{{ __('packs.absences.days.thu') }}",
                        "{{ __('packs.absences.days.fri') }}",
                        "{{ __('packs.absences.days.sat') }}"
                    ],
                    monthNames: [
                        "{{ __('packs.absences.months.jan') }}",
                        "{{ __('packs.absences.months.feb') }}",
                        "{{ __('packs.absences.months.mar') }}",
                        "{{ __('packs.absences.months.apr') }}",
                        "{{ __('packs.absences.months.may') }}",
                        "{{ __('packs.absences.months.jun') }}",
                        "{{ __('packs.absences.months.jul') }}",
                        "{{ __('packs.absences.months.aug') }}",
                        "{{ __('packs.absences.months.sep') }}",
                        "{{ __('packs.absences.months.oct') }}",
                        "{{ __('packs.absences.months.nov') }}",
                        "{{ __('packs.absences.months.dec') }}"
                    ],
                    firstDay: 1
                },
                opens: 'left',
                drops: 'down',
                linkedCalendars: false,
                showDropdowns: true,
                minYear: moment().subtract(10, 'years').year(),
                maxYear: moment().add(1, 'year').year(),
                autoApply: true,
                @if(request('date_debut') && request('date_fin'))
                    startDate: '{{ request('date_debut') }}',
                    endDate: '{{ request('date_fin') }}',
                @else
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                @endif
                ranges: {
                    "{{ __('packs.absences.filters.today') }}": [moment(), moment()],
                    "{{ __('packs.absences.filters.yesterday') }}": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    "{{ __('packs.absences.filters.this_week') }}": [moment().startOf('week'), moment().endOf('week')],
                    "{{ __('packs.absences.filters.last_week') }}": [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                    "{{ __('packs.absences.filters.this_month') }}": [moment().startOf('month'), moment().endOf('month')],
                    "{{ __('packs.absences.filters.last_month') }}": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    "{{ __('packs.absences.filters.this_year') }}": [moment().startOf('year'), moment().endOf('year')],
                }
            }, function(start, end, label) {
                // Mettre à jour les champs de date cachés
                $('input[name="date_debut"]').val(start.format('YYYY-MM-DD'));
                $('input[name="date_fin"]').val(end.format('YYYY-MM-DD'));
                
                // Mettre à jour l'affichage
                $('input[name="date_range"]').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });
            
            // Initialiser les tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Gérer la soumission du formulaire d'export
            $('#exportForm').on('submit', function(e) {
                // Afficher un indicateur de chargement
                const exportButton = $('#exportButton');
                const originalText = exportButton.html();
                exportButton.prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    {{ __('packs.absences.export.downloading') }}
                `);
                
                // Laisser le temps à l'utilisateur de voir le feedback
                setTimeout(() => {
                    exportButton.prop('disabled', false).html(originalText);
                }, 3000);
            });
        });
    </script>
@endpush
