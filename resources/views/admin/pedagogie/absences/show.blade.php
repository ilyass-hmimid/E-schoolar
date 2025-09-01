@extends('layouts.admin')

@section('title', __('packs.absences.titles.show'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('packs.absences.titles.show') }}</h1>
            <div>
                <a href="{{ route('admin.absences.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('packs.absences.buttons.back_to_list') }}
                </a>
                <a href="{{ route('admin.absences.edit', $absence) }}" class="btn btn-primary ms-2">
                    <i class="fas fa-edit me-1"></i> {{ __('packs.absences.buttons.edit') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('packs.absences.titles.details') }}</h6>
                        <div>
                            <a href="{{ route('admin.absences.pdf', $absence) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> {{ __('packs.absences.buttons.export_pdf') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">{{ __('packs.absences.titles.student_info') }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>{{ __('packs.absences.fields.student') }}:</strong><br>
                                        {{ $absence->eleve->prenom }} {{ $absence->eleve->nom }}
                                    </p>
                                    <p>
                                        <strong>{{ __('packs.absences.fields.class') }}:</strong><br>
                                        {{ $absence->eleve->classe->nom ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong>{{ __('packs.absences.fields.date_naissance') }}:</strong><br>
                                        {{ $absence->eleve->date_naissance ? $absence->eleve->date_naissance->format('d/m/Y') : 'N/A' }}
                                    </p>
                                    <p>
                                        <strong>{{ __('packs.absences.fields.email') }}:</strong><br>
                                        {{ $absence->eleve->user->email ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">{{ __('packs.absences.titles.absence_info') }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>{{ __('packs.absences.fields.date') }}:</strong><br>
                                        {{ $absence->date_absence->format('d/m/Y') }}
                                    </p>
                                    <p>
                                        <strong>{{ __('packs.absences.fields.type') }}:</strong><br>
                                        @php
                                            $typeClass = [
                                                'absence' => 'bg-gray-100 text-gray-800',
                                                'retard' => 'bg-blue-100 text-blue-800',
                                                'sortie' => 'bg-purple-100 text-purple-800',
                                            ][$absence->type_absence] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="badge {{ $typeClass }} px-2 py-1">
                                            {{ __('packs.absences.types.' . $absence->type_absence) }}
                                            @if($absence->duree_absence && in_array($absence->type_absence, ['retard', 'sortie']))
                                                ({{ $absence->duree_absence }} min)
                                            @endif
                                        </span>
                                    </p>
                                    <p>
                                        <strong>{{ __('packs.absences.fields.status') }}:</strong><br>
                                        @if($absence->justifiee)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> {{ __('packs.absences.status.justified') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-circle me-1"></i> {{ __('packs.absences.status.unjustified') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong>{{ __('packs.absences.fields.course') }}:</strong><br>
                                        {{ $absence->cours->matiere->nom ?? 'N/A' }}
                                    </p>
                                    <p>
                                        <strong>{{ __('packs.absences.fields.teacher') }}:</strong><br>
                                        {{ $absence->cours->professeur->user->name ?? 'N/A' }}
                                    </p>
                                    <p>
                                        <strong>{{ __('packs.absences.fields.schedule') }}:</strong><br>
                                        {{ $absence->cours->jour_semaine ?? '' }}
                                        {{ $absence->cours->heure_debut ?? '' }} - {{ $absence->cours->heure_fin ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($absence->motif || $absence->commentaire)
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">{{ __('packs.absences.titles.additional_info') }}</h5>
                                @if($absence->motif)
                                    <p>
                                        <strong>{{ __('packs.absences.fields.reason') }}:</strong><br>
                                        {{ $absence->motif }}
                                    </p>
                                @endif
                                @if($absence->commentaire)
                                    <p class="mb-0">
                                        <strong>{{ __('packs.absences.fields.comments') }}:</strong><br>
                                        {{ $absence->commentaire }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        @if($absence->chemin_justificatif)
                            <div class="mt-4">
                                <h5 class="border-bottom pb-2 mb-3">{{ __('packs.absences.titles.justificatif') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-file-alt fa-3x text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1">
                                            <strong>{{ basename($absence->chemin_justificatif) }}</strong>
                                        </p>
                                        <p class="mb-0">
                                            <a href="{{ route('admin.absences.justificatif.download', $absence) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i> {{ __('packs.absences.buttons.download_justificatif') }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Historique des absences de l'élève -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ __('packs.absences.titles.student_absences') }}
                            <span class="badge bg-secondary">{{ $studentAbsences->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @if($studentAbsences->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300 mb-2"></i>
                                <p class="text-muted mb-0">{{ __('packs.absences.messages.no_other_absences') }}</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($studentAbsences as $ab)
                                    <a href="{{ route('admin.absences.show', $ab) }}" 
                                       class="list-group-item list-group-item-action {{ $ab->id === $absence->id ? 'active' : '' }}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                {{ $ab->date_absence->format('d/m/Y') }}
                                                @if($ab->id === $absence->id)
                                                    <span class="badge bg-light text-dark">{{ __('packs.absences.current') }}</span>
                                                @endif
                                            </h6>
                                            <small>
                                                @if($ab->justifiee)
                                                    <span class="badge bg-success">{{ __('packs.absences.status.justified_short') }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">{{ __('packs.absences.status.unjustified_short') }}</span>
                                                @endif
                                            </small>
                                        </div>
                                        <p class="mb-1">
                                            {{ $ab->cours->matiere->nom ?? 'N/A' }}
                                            @if($ab->duree_absence && in_array($ab->type_absence, ['retard', 'sortie']))
                                                <small class="text-muted">({{ $ab->duree_absence }} min)</small>
                                            @endif
                                        </p>
                                        <small>
                                            <i class="fas fa-{{ $ab->type_absence === 'retard' ? 'clock' : ($ab->type_absence === 'sortie' ? 'sign-out-alt' : 'user-times') }} me-1"></i>
                                            {{ __('packs.absences.types.' . $ab->type_absence) }}
                                        </small>
                                    </a>
                                @endforeach
                            </div>
                            @if($studentAbsences->hasMorePages())
                                <div class="text-center py-2 bg-light">
                                    <a href="{{ route('admin.absences.index', ['eleve_id' => $absence->eleve_id]) }}" class="btn btn-sm btn-link">
                                        {{ __('packs.absences.buttons.view_all_absences') }}
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('packs.absences.titles.quick_actions') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if(!$absence->justifiee)
                                <form action="{{ route('admin.absences.justify', $absence) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success mb-2">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('packs.absences.buttons.justify') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.absences.unjustify', $absence) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning mb-2">
                                        <i class="fas fa-times-circle me-1"></i> {{ __('packs.absences.buttons.unjustify') }}
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('admin.eleves.show', $absence->eleve) }}" class="btn btn-outline-primary mb-2">
                                <i class="fas fa-user-graduate me-1"></i> {{ __('packs.absences.buttons.view_student') }}
                            </a>
                            
                            <a href="#" class="btn btn-outline-secondary mb-2" data-bs-toggle="modal" data-bs-target="#notifyModal">
                                <i class="fas fa-envelope me-1"></i> {{ __('packs.absences.buttons.notify_parents') }}
                            </a>
                            
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash-alt me-1"></i> {{ __('packs.absences.buttons.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de notification -->
    <div class="modal fade" id="notifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.absences.notify', $absence) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('packs.absences.notification.title') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('packs.absences.notification.description') }}</p>
                        
                        <div class="mb-3">
                            <label for="notification_type" class="form-label">{{ __('packs.absences.notification.type') }}</label>
                            <select name="type" id="notification_type" class="form-select">
                                <option value="email">{{ __('packs.absences.notification.types.email') }}</option>
                                <option value="sms">{{ __('packs.absences.notification.types.sms') }}</option>
                                <option value="both">{{ __('packs.absences.notification.types.both') }}</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="custom_message" class="form-label">{{ __('packs.absences.notification.custom_message') }}</label>
                            <textarea name="message" id="custom_message" rows="4" class="form-control">{{ __('packs.absences.notification.default_message', [
                                'student' => $absence->eleve->prenom . ' ' . $absence->eleve->nom,
                                'date' => $absence->date_absence->format('d/m/Y'),
                                'type' => __('packs.absences.types.' . $absence->type_absence),
                                'course' => $absence->cours->matiere->nom ?? 'N/A'
                            ]) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('packs.absences.buttons.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> {{ __('packs.absences.buttons.send_notification') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('packs.absences.confirm.delete_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('packs.absences.confirm.delete_message') }}</p>
                    <p class="fw-bold">{{ $absence->eleve->prenom }} {{ $absence->eleve->nom }} - {{ $absence->date_absence->format('d/m/Y') }}</p>
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
@endsection
