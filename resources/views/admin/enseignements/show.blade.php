@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Card d'information sur l'enseignement -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">{{ $enseignement->matiere->nom ?? 'Matière non définie' }}</h3>
                    <p class="text-muted text-center">
                        <span class="badge bg-{{ $enseignement->is_active ? 'success' : 'secondary' }}">
                            {{ $enseignement->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b><i class="fas fa-chalkboard-teacher mr-1"></i> Professeur</b>
                            <span class="float-right">
                                {{ $enseignement->professeur->name ?? 'Non attribué' }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-users-class mr-1"></i> Classe</b>
                            <span class="float-right">
                                {{ $enseignement->classe->nom ?? 'Non définie' }} ({{ $enseignement->classe->niveau ?? '' }})
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-calendar-alt mr-1"></i> Année Scolaire</b>
                            <span class="float-right">{{ $enseignement->annee_scolaire }}</span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-clock mr-1"></i> Volume Horaire</b>
                            <span class="float-right">{{ $enseignement->volume_horaire }} heures</span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-percentage mr-1"></i> Coefficient</b>
                            <span class="float-right">{{ $enseignement->coefficient }}</span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-calendar-check mr-1"></i> Semestre</b>
                            <span class="float-right">Semestre {{ $enseignement->semestre }}</span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.enseignements.edit', $enseignement) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('admin.enseignements.destroy', $enseignement) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignement ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- A propos de la matière -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">À propos de la matière</h3>
                </div>
                <div class="card-body">
                    @if($enseignement->matiere)
                        <strong><i class="fas fa-book mr-1"></i> Code</strong>
                        <p class="text-muted">
                            {{ $enseignement->matiere->code ?? 'Non défini' }}
                        </p>
                        <hr>

                        <strong><i class="fas fa-info-circle mr-1"></i> Description</strong>
                        <p class="text-muted">
                            {{ $enseignement->matiere->description ?? 'Aucune description disponible' }}
                        </p>
                    @else
                        <p class="text-muted">Aucune information sur la matière disponible</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#details" data-toggle="tab">Détails</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#cours" data-toggle="tab">Cours</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#evaluations" data-toggle="tab">Évaluations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#historique" data-toggle="tab">Historique</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="details">
                            <div class="post">
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm" 
                                         src="{{ $enseignement->professeur->avatar ? asset('storage/' . $enseignement->professeur->avatar) : asset('img/default-avatar.png') }}" 
                                         alt="Photo du professeur">
                                    <span class="username">
                                        <a href="#">{{ $enseignement->professeur->name ?? 'Professeur non défini' }}</a>
                                    </span>
                                    <span class="description">
                                        Responsable de cet enseignement
                                    </span>
                                </div>
                                
                                <h4>Description de l'enseignement</h4>
                                <p>{{ $enseignement->description ?? 'Aucune description fournie.' }}</p>
                                
                                <div class="row">
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $enseignement->cours_count ?? 0 }}</h5>
                                            <span class="description-text">COURS</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $enseignement->evaluations_count ?? 0 }}</h5>
                                            <span class="description-text">ÉVALUATIONS</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $enseignement->etudiants_count ?? 0 }}</h5>
                                            <span class="description-text">ÉTUDIANTS</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="cours">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Heure de début</th>
                                            <th>Heure de fin</th>
                                            <th>Sujet</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enseignement->cours as $cours)
                                        <tr>
                                            <td>{{ $cours->date->format('d/m/Y') }}</td>
                                            <td>{{ $cours->heure_debut }}</td>
                                            <td>{{ $cours->heure_fin }}</td>
                                            <td>{{ $cours->sujet ?? 'Non spécifié' }}</td>
                                            <td>
                                                @if($cours->est_termine)
                                                    <span class="badge bg-success">Terminé</span>
                                                @elseif($cours->est_annule)
                                                    <span class="badge bg-danger">Annulé</span>
                                                @elseif($cours->date < now())
                                                    <span class="badge bg-warning">À valider</span>
                                                @else
                                                    <span class="badge bg-info">Planifié</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucun cours planifié pour le moment</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Planifier un cours
                                </a>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="evaluations">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Note maximale</th>
                                            <th>Moyenne</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enseignement->evaluations as $evaluation)
                                        <tr>
                                            <td>{{ ucfirst($evaluation->type) }}</td>
                                            <td>{{ $evaluation->date->format('d/m/Y') }}</td>
                                            <td>{{ $evaluation->note_maximale }}</td>
                                            <td>{{ $evaluation->moyenne ?? '-' }}/{{ $evaluation->note_maximale }}</td>
                                            <td>
                                                @if($evaluation->est_termine)
                                                    <span class="badge bg-success">Terminé</span>
                                                @elseif($evaluation->est_annule)
                                                    <span class="badge bg-danger">Annulé</span>
                                                @elseif($evaluation->date < now())
                                                    <span class="badge bg-warning">En attente de correction</span>
                                                @else
                                                    <span class="badge bg-info">À venir</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-primary" title="Saisir les notes">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucune évaluation pour le moment</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Créer une évaluation
                                </a>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="historique">
                            <div class="timeline">
                                @forelse($enseignement->historique as $historique)
                                <div>
                                    <i class="fas {{ $historique->icone }} bg-{{ $historique->couleur }}"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="far fa-clock"></i> 
                                            {{ $historique->created_at->diffForHumans() }}
                                        </span>
                                        <h3 class="timeline-header">
                                            <a href="#">{{ $historique->utilisateur->name ?? 'Système' }}</a> 
                                            {{ $historique->action }}
                                        </h3>
                                        @if($historique->details)
                                        <div class="timeline-body">
                                            {{ $historique->details }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-muted text-center py-4">Aucun historique disponible</div>
                                @endforelse
                                
                                <div>
                                    <i class="far fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-user-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    .list-group-item {
        border-left: 0;
        border-right: 0;
    }
    .list-group-item:first-child {
        border-top: 0;
    }
    .list-group-item:last-child {
        border-bottom: 0;
    }
    .description-block {
        display: block;
        margin: 10px 0;
        text-align: center;
    }
    .description-block > .description-header {
        margin: 0;
        padding: 0;
        font-weight: 600;
        font-size: 16px;
    }
    .description-block > .description-text {
        text-transform: uppercase;
        font-weight: 500;
        font-size: 12px;
        color: #6c757d;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    /* Style pour la timeline */
    .timeline {
        position: relative;
        padding: 0 0 0 30px;
        margin: 0;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
        left: 31px;
        margin: 0;
    }
    .timeline > div {
        position: relative;
        margin-bottom: 15px;
    }
    .timeline > div > i {
        position: absolute;
        left: -30px;
        width: 20px;
        text-align: center;
        top: 0;
        border-radius: 50%;
        padding: 5px;
        color: #fff;
    }
    .timeline-item {
        margin-left: 35px;
        background: #fff;
        padding: 10px;
        border-radius: 3px;
        border: 1px solid #dee2e6;
    }
    .time {
        color: #6c757d;
        font-size: 12px;
    }
    .timeline-header {
        font-size: 14px;
        margin: 0 0 10px 0;
        padding-bottom: 5px;
        border-bottom: 1px solid #f4f4f4;
    }
    .timeline-body {
        padding: 10px 0;
    }
</style>
@endpush
