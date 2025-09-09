@extends('admin.layouts.app')

@section('title', 'Configuration des Salaires')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configuration des Salaires</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="salariesTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="matieres-tab" data-toggle="tab" href="#matieres" role="tab" aria-controls="matieres" aria-selected="true">
                                <i class="fas fa-book"></i> Prix des Matières
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="professeurs-tab" data-toggle="tab" href="#professeurs" role="tab" aria-controls="professeurs" aria-selected="false">
                                <i class="fas fa-chalkboard-teacher"></i> Pourcentages des Professeurs
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3" id="salariesTabsContent">
                        <!-- Onglet Prix des Matières -->
                        <div class="tab-pane fade show active" id="matieres" role="tabpanel" aria-labelledby="matieres-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Matière</th>
                                            <th>Niveau</th>
                                            <th>Prix Mensuel (DH)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($matieres as $matiere)
                                            <tr>
                                                <td>{{ $matiere->nom }}</td>
                                                <td>{{ $matiere->niveau_libelle ?? 'Non spécifié' }}</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" 
                                                               class="form-control prix-matiere" 
                                                               data-matiere-id="{{ $matiere->id }}" 
                                                               value="{{ $matiere->prix_mensuel ?? 0 }}" 
                                                               min="0" 
                                                               step="0.01">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">DH</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary btn-save-prix" data-matiere-id="{{ $matiere->id }}">
                                                        <i class="fas fa-save"></i> Enregistrer
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Aucune matière trouvée</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Onglet Pourcentages des Professeurs -->
                        <div class="tab-pane fade" id="professeurs" role="tabpanel" aria-labelledby="professeurs-tab">
                            @if($professeurs->isEmpty())
                                <div class="alert alert-info">
                                    Aucun professeur trouvé. Veuillez d'abord ajouter des professeurs et leur assigner des matières.
                                </div>
                            @else
                                @foreach($professeurs as $professeur)
                                    <div class="card card-outline card-primary mt-3">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                {{ $professeur->name }} {{ $professeur->first_name }}
                                                @if($professeur->matieresEnseignees->isEmpty())
                                                    <span class="badge badge-warning ml-2">Aucune matière assignée</span>
                                                @endif
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if($professeur->matieresEnseignees->isEmpty())
                                                <div class="alert alert-warning">
                                                    Ce professeur n'enseigne aucune matière pour le moment.
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Matière</th>
                                                                <th>Niveau</th>
                                                                <th>Prix Mensuel (DH)</th>
                                                                <th>Pourcentage (%)</th>
                                                                <th>Gain Mensuel (DH)</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($professeur->matieresEnseignees as $matiere)
                                                                <tr>
                                                                    <td>{{ $matiere->nom }}</td>
                                                                    <td>{{ $matiere->niveau_libelle ?? 'Non spécifié' }}</td>
                                                                    <td>{{ number_format($matiere->prix_mensuel ?? 0, 2, ',', ' ') }}</td>
                                                                    <td>
                                                                        <div class="input-group" style="width: 150px;">
                                                                            <input type="number" 
                                                                                   class="form-control pourcentage-professeur" 
                                                                                   data-professeur-id="{{ $professeur->id }}" 
                                                                                   data-matiere-id="{{ $matiere->id }}" 
                                                                                   value="{{ $matiere->pivot->pourcentage_remuneration ?? 30 }}" 
                                                                                   min="0" 
                                                                                   max="100" 
                                                                                   step="0.01">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        {{ number_format((($matiere->prix_mensuel ?? 0) * ($matiere->pivot->pourcentage_remuneration ?? 30) / 100), 2, ',', ' ') }}
                                                                    </td>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-primary btn-save-pourcentage" 
                                                                                data-professeur-id="{{ $professeur->id }}" 
                                                                                data-matiere-id="{{ $matiere->id }}">
                                                                            <i class="fas fa-save"></i> Enregistrer
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gestion de la sauvegarde du prix d'une matière
    $('.btn-save-prix').click(function() {
        const button = $(this);
        const matiereId = button.data('matiere-id');
        const prixInput = $('.prix-matiere[data-matiere-id="' + matiereId + '"]');
        const prix = parseFloat(prixInput.val());
        
        if (isNaN(prix) || prix < 0) {
            toastr.error('Veuillez entrer un prix valide');
            return;
        }
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');
        
        $.ajax({
            url: '{{ route("admin.configuration.salaires.matieres.update-prix", ["matiereId" => "__MATIERE_ID__"]) }}'.replace('__MATIERE_ID__', matiereId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prix_mensuel: prix
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Mettre à jour l'affichage du prix formaté
                    prixInput.val(prix);
                } else {
                    toastr.error(response.message || 'Une erreur est survenue');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(error => {
                        toastr.error(error[0]);
                    });
                } else {
                    toastr.error('Une erreur est survenue lors de la mise à jour du prix');
                }
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-save"></i> Enregistrer');
            }
        });
    });
    
    // Gestion de la sauvegarde du pourcentage d'un professeur pour une matière
    $('.btn-save-pourcentage').click(function() {
        const button = $(this);
        const professeurId = button.data('professeur-id');
        const matiereId = button.data('matiere-id');
        const pourcentageInput = $(`.pourcentage-professeur[data-professeur-id="${professeurId}"][data-matiere-id="${matiereId}"]`);
        const pourcentage = parseFloat(pourcentageInput.val());
        
        if (isNaN(pourcentage) || pourcentage < 0 || pourcentage > 100) {
            toastr.error('Le pourcentage doit être compris entre 0 et 100');
            return;
        }
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');
        
        $.ajax({
            url: '{{ route("admin.configuration.salaires.professeurs.update-pourcentage") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                professeur_id: professeurId,
                matiere_id: matiereId,
                pourcentage: pourcentage
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Recharger la page pour mettre à jour les calculs
                    location.reload();
                } else {
                    toastr.error(response.message || 'Une erreur est survenue');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(error => {
                        toastr.error(error[0]);
                    });
                } else {
                    toastr.error('Une erreur est survenue lors de la mise à jour du pourcentage');
                }
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-save"></i> Enregistrer');
            }
        });
    });
    
    // Permettre d'appuyer sur Entrée pour sauvegarder
    $('.prix-matiere, .pourcentage-professeur').keypress(function(e) {
        if (e.which === 13) { // Touche Entrée
            if ($(this).hasClass('prix-matiere')) {
                $(this).closest('tr').find('.btn-save-prix').click();
            } else {
                const professeurId = $(this).data('professeur-id');
                const matiereId = $(this).data('matiere-id');
                $(`.btn-save-pourcentage[data-professeur-id="${professeurId}"][data-matiere-id="${matiereId}"]`).click();
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card-outline {
    border-top: 3px solid #007bff;
}

.table th {
    white-space: nowrap;
}

.input-group {
    max-width: 200px;
}

.btn-save-prix, .btn-save-pourcentage {
    min-width: 100px;
}

/* Style pour les onglets */
.nav-tabs .nav-link {
    font-weight: 500;
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
    color: #007bff;
    border-bottom: 2px solid #007bff;
}

/* Style pour les cartes des professeurs */
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

/* Style pour les tableaux */
.table {
    font-size: 0.875rem;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

/* Style pour les boutons */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Style pour les champs de formulaire */
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Style pour les messages d'erreur */
.invalid-feedback {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #dc3545;
}

/* Style pour les alertes */
.alert {
    margin-bottom: 1rem;
    padding: 0.75rem 1.25rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeeba;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
</style>
@endpush
