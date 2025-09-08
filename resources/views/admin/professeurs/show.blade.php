@extends('layouts.admin')

@section('title', 'Détails du professeur')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Détails du professeur : {{ $professeur->nom }} {{ $professeur->prenom }}</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.professeurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('admin.professeurs.edit', $professeur) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Carte de profil -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="position-relative d-inline-block">
                            <img class="profile-user-img img-fluid rounded-circle"
                                 src="{{ $professeur->avatar ? asset('storage/' . $professeur->avatar) : asset('img/default-avatar.png') }}"
                                 alt="Photo de profil" style="width: 100px; height: 100px; object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 p-1 {{ $professeur->est_actif ? 'bg-success' : 'bg-danger' }} border border-white rounded-circle">
                                <span class="visually-hidden">Statut</span>
                            </span>
                        </div>
                    </div>

                    <h3 class="profile-username text-center mt-3">{{ $professeur->nom }} {{ $professeur->prenom }}</h3>

                    <p class="text-muted text-center">
                        @if($professeur->matieres->count() > 0)
                            @foreach($professeur->matieres as $matiere)
                                <span class="badge bg-primary me-1 mb-1">
                                    {{ $matiere->nom }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-danger">Aucune matière assignée</span>
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b><i class="fas fa-envelope mr-2"></i>Email</b>
                            <a href="mailto:{{ $professeur->email }}" class="float-right">{{ $professeur->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-phone mr-2"></i>Téléphone</b>
                            <span class="float-right">{{ $professeur->telephone ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-calendar-day mr-2"></i>Date de naissance</b>
                            <span class="float-right">
                                {{ $professeur->date_naissance ? $professeur->date_naissance->format('d/m/Y') : 'Non renseignée' }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-info-circle mr-2"></i>Statut</b>
                            <span class="float-right">
                                @if($professeur->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fas fa-calendar-alt mr-2"></i>Créé le</b>
                            <span class="float-right">{{ $professeur->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
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
            </div>
            <!-- /.card -->

            <!-- À propos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations professionnelles</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-id-card mr-2"></i> Matricule</strong>
                    <p class="text-muted">{{ $professeur->matricule ?? 'Non défini' }}</p>
                    <hr>
                    
                    <strong><i class="fas fa-calendar-alt mr-2"></i> Date d'embauche</strong>
                    <p class="text-muted">
                        {{ $professeur->date_embauche ? $professeur->date_embauche->format('d/m/Y') : 'Non définie' }}
                        @if($professeur->date_embauche)
                            <small class="text-muted">({{ $professeur->anciennete }} ans d'ancienneté)</small>
                        @endif
                    </p>
                    <hr>
                    
                    <strong><i class="fas fa-percentage mr-2"></i> Pourcentage de rémunération</strong>
                    <p class="text-muted">{{ $professeur->pourcentage_remuneration }}%</p>
                    <hr>
                    
                    <strong><i class="fas fa-money-bill-wave mr-2"></i> Salaire mensuel estimé</strong>
                    <h4 class="text-primary">{{ number_format($professeur->salaire_mensuel, 2, ',', ' ') }} DH</h4>
                    <small class="text-muted">Basé sur les inscriptions actuelles</small>
                    <hr>
                    
                    <strong><i class="fas fa-info-circle mr-2"></i> Statut</strong>
                    <p>
                        @if($professeur->est_actif)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </p>
                    <strong><i class="fas fa-map-marker-alt mr-2"></i>Adresse</strong>
                    <p class="text-muted">
                        @if($professeur->adresse)
                            {{ $professeur->adresse }}
                            @if($professeur->pays)
                                <br>{{ $professeur->pays }}
                            @endif
                        @else
                            Non renseignée
                        @endif
                    </p>
                    <hr>

                    <strong><i class="fas fa-book mr-2"></i>Matières enseignées</strong>
                    <p class="mt-2">
                        @forelse($professeur->matieres as $matiere)
                            <span class="badge bg-primary mb-1">{{ $matiere->nom }}</span>
                        @empty
                            <span class="text-muted">Aucune matière assignée</span>
                        @endforelse
                    </p>
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#matieres" data-toggle="tab">
                                <i class="fas fa-book mr-1"></i> Matières
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#activity" data-toggle="tab">
                                <i class="fas fa-chart-line mr-1"></i> Activité
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#paiements" data-toggle="tab">
                                <i class="fas fa-money-bill-wave mr-1"></i> Paiements
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Onglet Matières -->
                        <div class="active tab-pane" id="matieres">
                            @if($professeur->matieres->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Matière</th>
                                                <th>Niveau</th>
                                                <th>Nombre d'élèves</th>
                                                <th>Prix mensuel/élève</th>
                                                <th>Pourcentage</th>
                                                <th>Montant mensuel</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($professeur->matieres as $matiere)
                                                <tr>
                                                    <td>{{ $matiere->nom }}</td>
                                                    <td>{{ $matiere->niveau ?? 'Tous niveaux' }}</td>
                                                    <td>{{ $matiere->eleves_count ?? 0 }}</td>
                                                    <td>{{ number_format($matiere->prix_mensuel, 2, ',', ' ') }} DH</td>
                                                    <td>{{ $matiere->pivot->pourcentage_remuneration ?? $professeur->pourcentage_remuneration }}%</td>
                                                    <td class="font-weight-bold">
                                                        {{ number_format(($matiere->prix_mensuel * $matiere->eleves_count * ($matiere->pivot->pourcentage_remuneration ?? $professeur->pourcentage_remuneration) / 100), 2, ',', ' ') }} DH
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-active">
                                                <td colspan="4" class="text-right"><strong>Total mensuel :</strong></td>
                                                <td colspan="2" class="font-weight-bold text-primary">
                                                    {{ number_format($professeur->salaire_mensuel, 2, ',', ' ') }} DH
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Aucune matière assignée à ce professeur.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Onglet Activité -->
                        <div class="tab-pane" id="activity">
                            <div class="row">
                                <div class="col-md-4 col-sm-6 col-12">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Cours ce mois</span>
                                            <span class="info-box-number">24</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-12">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-tasks"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Évaluations</span>
                                            <span class="info-box-number">8</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-12">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-users"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Élèves</span>
                                            <span class="info-box-number">156</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Dernières activités</h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2">
                                        <li class="item">
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">
                                                    Cours de Mathématiques
                                                    <span class="badge badge-info float-right">Aujourd'hui</span>
                                                </a>
                                                <span class="product-description">
                                                    14:00 - 16:00 | Salle B12
                                                </span>
                                            </div>
                                        </li>
                                        <li class="item">
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">
                                                    Correction des devoirs
                                                    <span class="badge badge-warning float-right">En retard</span>
                                                </a>
                                                <span class="product-description">
                                                    Devoir de Physique - Terminale S1
                                                </span>
                                            </div>
                                        </li>
                                        <li class="item">
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">
                                                    Réunion pédagogique
                                                    <span class="badge badge-success float-right">Terminé</span>
                                                </a>
                                                <span class="product-description">
                                                    Hier | 16:00 - 17:30
                                                </span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <!-- Onglet Paiements -->
                        <div class="tab-pane" id="paiements">
                            @if($professeur->paiements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Mois</th>
                                                <th>Montant</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($professeur->paiements as $paiement)
                                                <tr>
                                                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                                    <td>{{ $paiement->mois_paiement->format('m/Y') }}</td>
                                                    <td>{{ number_format($paiement->montant, 2, ',', ' ') }} DH</td>
                                                    <td>
                                                        @if($paiement->est_paye)
                                                            <span class="badge bg-success">Payé</span>
                                                        @else
                                                            <span class="badge bg-warning">En attente</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-info" title="Voir le reçu">
                                                            <i class="fas fa-receipt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Aucun paiement enregistré pour ce professeur.
                                </div>
                            @endif
                            
                            <div class="mt-3">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#ajouterPaiementModal">
                                    <i class="fas fa-plus"></i> Ajouter un paiement
                                </button>
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
@stop

@push('styles')
<style>
    .profile-user-img {
        border: 3px solid #e9ecef;
        margin: 0 auto;
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    
    .profile-username {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .list-group-unbordered > .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: 0.75rem 1.25rem;
        border-color: rgba(0,0,0,.125);
    }
    
    .list-group-unbordered > .list-group-item:first-child {
        border-top: 0;
    }
    
    .list-group-unbordered > .list-group-item:last-child {
        border-bottom: 0;
    }
    
    .nav-pills .nav-link {
        border-radius: 0.25rem;
        font-weight: 500;
        color: #6c757d;
    }
    
    .nav-pills .nav-link.active {
        background-color: #007bff;
    }
    
    .info-box {
        margin-bottom: 1.5rem;
    }
    
    .products-list .product-info {
        margin-left: 0;
    }
    
    .product-title {
        display: block;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .product-description {
        display: block;
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialisation des tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Gestion des onglets avec stockage de l'état
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('professeurTab', $(e.target).attr('href'));
    });
    
    // Restauration de l'onglet actif
    var activeTab = localStorage.getItem('professeurTab');
    if (activeTab) {
        $('.nav-pills a[href="' + activeTab + '"]').tab('show');
    }
});
</script>
@endpush
