@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ $professeur->avatar ? asset('storage/' . $professeur->avatar) : asset('img/default-avatar.png') }}"
                             alt="Photo de profil" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <h3 class="profile-username text-center">{{ $professeur->name }}</h3>

                    <p class="text-muted text-center">
                        <span class="badge bg-primary">
                            {{ ucfirst($professeur->statut) }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b><i class="fas fa-envelope mr-1"></i> Email</b>
                            <a href="mailto:{{ $professeur->email }}" class="float-right">{{ $professeur->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-phone mr-1"></i> Téléphone</b>
                            <a href="tel:{{ $professeur->telephone }}" class="float-right">{{ $professeur->telephone ?? 'Non défini' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-graduation-cap mr-1"></i> Spécialité</b>
                            <span class="float-right">{{ $professeur->specialite ?? 'Non définie' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-calendar-alt mr-1"></i> Date d'embauche</b>
                            <span class="float-right">{{ $professeur->date_embauche->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-info-circle mr-1"></i> Statut</b>
                            <span class="float-right">
                                @if($professeur->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.professeurs.edit', $professeur) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('admin.professeurs.destroy', $professeur) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce professeur ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Box -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">À propos</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <strong><i class="fas fa-book mr-1"></i> Diplôme</strong>
                    <p class="text-muted">
                        {{ $professeur->diplome ?? 'Non spécifié' }}
                    </p>
                    <hr>

                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Adresse</strong>
                    <p class="text-muted">
                        @if($professeur->adresse)
                            {{ $professeur->adresse }}<br>
                            {{ $professeur->ville ?? '' }} {{ $professeur->pays ? ', ' . $professeur->pays : '' }}
                        @else
                            Non spécifiée
                        @endif
                    </p>
                    <hr>

                    <strong><i class="far fa-calendar-alt mr-1"></i> Date de naissance</strong>
                    <p class="text-muted">
                        @if($professeur->date_naissance)
                            {{ $professeur->date_naissance->format('d/m/Y') }}
                            @if($professeur->lieu_naissance)
                                à {{ $professeur->lieu_naissance }}
                            @endif
                        @else
                            Non spécifiée
                        @endif
                    </p>
                    <hr>

                    <strong><i class="fas fa-info-circle mr-1"></i> Dernière mise à jour</strong>
                    <p class="text-muted">{{ $professeur->updated_at->format('d/m/Y H:i') }}</p>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#activity" data-toggle="tab">Activité</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#classes" data-toggle="tab">Classes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#cours" data-toggle="tab">Cours</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#evaluations" data-toggle="tab">Évaluations</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="activity">
                            <div class="post">
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm" 
                                         src="{{ $professeur->avatar ? asset('storage/' . $professeur->avatar) : asset('img/default-avatar.png') }}" 
                                         alt="Photo de profil">
                                    <span class="username">
                                        <a href="#">{{ $professeur->name }}</a>
                                    </span>
                                    <span class="description">
                                        Membre depuis {{ $professeur->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <!-- /.user-block -->
                                <div class="row mb-3">
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $professeur->classes_count ?? 0 }}</h5>
                                            <span class="description-text">CLASSES</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $professeur->cours_count ?? 0 }}</h5>
                                            <span class="description-text">COURS</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $professeur->evaluations_count ?? 0 }}</h5>
                                            <span class="description-text">ÉVALUATIONS</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.post -->
                        </div>
                        <!-- /.tab-pane -->
                        
                        <div class="tab-pane" id="classes">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nom de la classe</th>
                                            <th>Niveau</th>
                                            <th>Effectif</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($professeur->classes as $classe)
                                        <tr>
                                            <td>{{ $classe->nom }}</td>
                                            <td>{{ $classe->niveau }}</td>
                                            <td>{{ $classe->eleves_count }} élèves</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune classe trouvée</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="cours">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Matière</th>
                                            <th>Classe</th>
                                            <th>Jour</th>
                                            <th>Heure de début</th>
                                            <th>Heure de fin</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($professeur->cours as $cours)
                                        <tr>
                                            <td>{{ $cours->matiere->nom ?? 'Non spécifiée' }}</td>
                                            <td>{{ $cours->classe->nom ?? 'Non spécifiée' }}</td>
                                            <td>{{ $cours->jour_semaine }}</td>
                                            <td>{{ $cours->heure_debut }}</td>
                                            <td>{{ $cours->heure_fin }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucun cours trouvé</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="evaluations">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Matière</th>
                                            <th>Classe</th>
                                            <th>Date</th>
                                            <th>Note maximale</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($professeur->evaluations as $evaluation)
                                        <tr>
                                            <td>{{ $evaluation->type }}</td>
                                            <td>{{ $evaluation->matiere->nom ?? 'Non spécifiée' }}</td>
                                            <td>{{ $evaluation->classe->nom ?? 'Non spécifiée' }}</td>
                                            <td>{{ $evaluation->date->format('d/m/Y') }}</td>
                                            <td>{{ $evaluation->note_maximale }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucune évaluation trouvée</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
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
</style>
@endpush
