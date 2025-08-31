@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Card d'information du centre -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ $centre->logo ? asset('storage/' . $centre->logo) : asset('img/default-centre.png') }}"
                             alt="Logo du centre" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <h3 class="profile-username text-center">{{ $centre->nom }}</h3>

                    <p class="text-muted text-center">
                        <span class="badge bg-{{ $centre->is_active ? 'success' : 'secondary' }}">
                            {{ $centre->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b><i class="fas fa-envelope mr-1"></i> Email</b>
                            <a href="mailto:{{ $centre->email }}" class="float-right">{{ $centre->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-phone mr-1"></i> Téléphone</b>
                            <a href="tel:{{ $centre->telephone }}" class="float-right">{{ $centre->telephone }}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-map-marker-alt mr-1"></i> Adresse</b>
                            <span class="float-right text-right">
                                {{ $centre->adresse }}<br>
                                {{ $centre->ville }}, {{ $centre->pays }}
                            </span>
                        </li>
                        @if($centre->responsable)
                        <li class="list-group-item">
                            <b><i class="fas fa-user-tie mr-1"></i> Responsable</b>
                            <a href="{{ route('admin.users.show', $centre->responsable) }}" class="float-right">
                                {{ $centre->responsable->name }}
                            </a>
                        </li>
                        @endif
                        <li class="list-group-item">
                            <b><i class="far fa-calendar-alt mr-1"></i> Date de création</b>
                            <span class="float-right">{{ $centre->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.centres.edit', $centre) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('admin.centres.destroy', $centre) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce centre ?')">
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

            <!-- A propos -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">À propos</h3>
                </div>
                <div class="card-body">
                    <p>{{ $centre->description ?? 'Aucune description disponible.' }}</p>
                    <hr>
                    <strong><i class="fas fa-info-circle mr-1"></i> Dernière mise à jour</strong>
                    <p class="text-muted">{{ $centre->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
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
                            <a class="nav-link" href="#enseignants" data-toggle="tab">Enseignants</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#eleves" data-toggle="tab">Élèves</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="activity">
                            <div class="post">
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm" 
                                         src="{{ $centre->logo ? asset('storage/' . $centre->logo) : asset('img/default-centre.png') }}" 
                                         alt="Logo du centre">
                                    <span class="username">
                                        <a href="#">{{ $centre->nom }}</a>
                                    </span>
                                    <span class="description">
                                        Créé le {{ $centre->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <!-- /.user-block -->
                                <div class="row mb-3">
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $centre->classes_count ?? 0 }}</h5>
                                            <span class="description-text">CLASSES</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $centre->enseignants_count ?? 0 }}</h5>
                                            <span class="description-text">ENSEIGNANTS</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $centre->eleves_count ?? 0 }}</h5>
                                            <span class="description-text">ÉLÈVES</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                            <th>Responsable</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($centre->classes as $classe)
                                        <tr>
                                            <td>{{ $classe->nom }}</td>
                                            <td>{{ $classe->niveau }}</td>
                                            <td>{{ $classe->eleves_count }} élèves</td>
                                            <td>{{ $classe->responsable->name ?? 'Non défini' }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucune classe trouvée pour ce centre</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="enseignants">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Classes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($centre->enseignants as $enseignant)
                                        <tr>
                                            <td>{{ $enseignant->name }}</td>
                                            <td>{{ $enseignant->email }}</td>
                                            <td>{{ $enseignant->telephone ?? 'Non défini' }}</td>
                                            <td>{{ $enseignant->classes_count ?? 0 }} classes</td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $enseignant) }}" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun enseignant trouvé pour ce centre</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="eleves">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Classe</th>
                                            <th>Téléphone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($centre->eleves as $eleve)
                                        <tr>
                                            <td>{{ $eleve->nom }}</td>
                                            <td>{{ $eleve->prenom }}</td>
                                            <td>{{ $eleve->classe->nom ?? 'Non défini' }}</td>
                                            <td>{{ $eleve->telephone ?? 'Non défini' }}</td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $eleve) }}" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun élève trouvé pour ce centre</td>
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
</style>
@endpush
