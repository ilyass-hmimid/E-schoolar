@extends('admin.layouts.app')

@section('title', 'Configuration des Salaires')

@section('content')
@php
    $isAdmin = true; // Pour afficher la sidebar admin
@endphp
<div class="content-wrapper pt-4">
    <!-- En-tête de page -->
    <div class="content-header bg-white py-3 mb-4 border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Configuration des Salaires
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-primary">
                                <i class="fas fa-home"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-cog"></i> Configuration des Salaires
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content px-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-cog text-primary mr-2"></i>
                                Gestion des rémunérations
                            </h3>
                        </div>
                        <div class="card-body px-0 px-md-4">
                            <div class="border-bottom mb-4">
                                <ul class="nav nav-tabs border-0" id="salariesTabs" role="tablist">
                                    <li class="nav-item mr-2 mb-0">
                                        <button class="nav-link btn btn-outline-primary rounded-pill px-4 py-2 active" 
                                                id="matieres-tab" 
                                                data-toggle="tab" 
                                                href="#matieres" 
                                                role="tab" 
                                                aria-controls="matieres" 
                                                aria-selected="true">
                                            <i class="fas fa-book mr-1"></i> 
                                            <span class="font-weight-bold">Prix des Matières</span>
                                        </button>
                                    </li>
                                    <li class="nav-item mb-0">
                                        <button class="nav-link btn btn-outline-primary rounded-pill px-4 py-2" 
                                                id="professeurs-tab" 
                                                data-toggle="tab" 
                                                href="#professeurs" 
                                                role="tab" 
                                                aria-controls="professeurs" 
                                                aria-selected="false">
                                            <i class="fas fa-chalkboard-teacher mr-1"></i> 
                                            <span class="font-weight-bold">Pourcentages des Professeurs</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="tab-content" id="salariesTabsContent">
                                <!-- Onglet Prix des Matières -->
                                <div class="tab-pane fade show active" id="matieres" role="tabpanel" aria-labelledby="matieres-tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%;">Matière</th>
                                                    <th style="width: 20%;">Niveau</th>
                                                    <th style="width: 30%;">Prix Mensuel (DH)</th>
                                                    <th style="width: 20%;">Actions</th>
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
                                                                    class="form-control form-control-sm prix-matiere" 
                                                                    data-matiere-id="{{ $matiere->id }}" 
                                                                    value="{{ $matiere->prix_mensuel ?? 0 }}" 
                                                                    min="0" 
                                                                    step="0.01"
                                                                    style="max-width: 150px;">

                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <button class="btn btn-sm btn-primary btn-save-prix rounded-pill px-3 py-1 shadow-sm" 
                                                                    data-matiere-id="{{ $matiere->id }}"
                                                                    data-toggle="tooltip" 
                                                                    title="Enregistrer les modifications">
                                                                <i class="fas fa-save mr-1"></i> 
                                                                <span class="d-none d-md-inline">Enregistrer</span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                                <p>Aucune matière trouvée</p>
                                                            </div>
                                                        </td>
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
                                            <i class="icon fas fa-info mr-2"></i>
                                            Aucun professeur trouvé. Veuillez d'abord ajouter des professeurs et leur assigner des matières.
                                        </div>
                                    @else
                                        @foreach($professeurs as $professeur)
                                            <div class="card card-outline card-primary mb-3">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-user-tie mr-2"></i>
                                                        {{ $professeur->name }} {{ $professeur->first_name }}
                                                        @if($professeur->matieresEnseignees->isEmpty())
                                                            <span class="badge badge-warning ml-2">Aucune matière assignée</span>
                                                        @endif
                                                    </h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-{{ $loop->first ? 'minus' : 'plus' }}"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body {{ $loop->first ? 'show' : 'collapse' }}">
                                                    @if($professeur->matieresEnseignees->isEmpty())
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="icon fas fa-exclamation-triangle mr-2"></i>
                                                            Ce professeur n'enseigne aucune matière pour le moment.
                                                        </div>
                                                    @else
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped table-hover">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width: 50%;">Matière</th>
                                                                        <th style="width: 30%;">Pourcentage de rémunération</th>
                                                                        <th style="width: 20%;">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($professeur->matieresEnseignees as $matiere)
                                                                        <tr>
                                                                            <td>{{ $matiere->nom }}</td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="number" 
                                                                                        class="form-control form-control-sm pourcentage-professeur" 
                                                                                        data-professeur-id="{{ $professeur->id }}" 
                                                                                        data-matiere-id="{{ $matiere->id }}" 
                                                                                        value="{{ $matiere->pivot->pourcentage_remuneration ?? 0 }}" 
                                                                                        min="0" 
                                                                                        max="100" 
                                                                                        step="0.01"
                                                                                        style="max-width: 120px;">
                                                                                    <div class="input-group-append">
                                                                                        <span class="input-group-text">%</span>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <button class="btn btn-sm btn-primary btn-save-pourcentage rounded-pill px-3 py-1 shadow-sm" 
                                                                                        data-professeur-id="{{ $professeur->id }}" 
                                                                                        data-matiere-id="{{ $matiere->id }}"
                                                                                        data-toggle="tooltip" 
                                                                                        title="Enregistrer le pourcentage">
                                                                                    <i class="fas fa-save mr-1"></i> 
                                                                                    <span class="d-none d-md-inline">Enregistrer</span>
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
    </section>
</div>
@endsection

@push('styles')
<style>
    /* Styles personnalisés pour la page de configuration des salaires */
    
    /* Correction de l'espacement avec la barre de navigation */
    .content-wrapper {
        padding-top: 1rem;
        margin-top: 60px; /* Ajustement pour la barre de navigation fixe */
    }
    
    /* Styles pour les boutons de sauvegarde spécifiques */
    .btn-save-prix, .btn-save-pourcentage {
        min-width: 100px;
    }
    
    /* Style des cartes */
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .card-primary.card-outline {
        border-top: 3px solid #007bff;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.25rem;
    }
    
    .card-title {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0;
    }
    
    /* Style des onglets personnalisés */
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
    }
    
    .nav-tabs .nav-item .nav-link {
        border: 1px solid #dee2e6;
        border-radius: 1.5rem !important;
        margin-right: 0.5rem;
        color: #6c757d;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
    }
    
    .nav-tabs .nav-item .nav-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #495057;
    }
    
    .nav-tabs .nav-item .nav-link.active {
        background-color: #007bff;
        color: white !important;
        border-color: #007bff;
        box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
    }
    
    /* Style des tableaux */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        white-space: nowrap;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .table td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
    }
    
    /* Style des champs de formulaire */
    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        max-width: 200px;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        font-size: 0.875rem;
    }
    
    /* Style des boutons */
    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .btn-primary:hover, .btn-primary:focus {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
        background-color: transparent;
    }
    
    .btn-outline-primary:hover, .btn-outline-primary:focus {
        background-color: #007bff;
        color: #fff;
    }
    
    /* Style des onglets de contenu */
    .tab-content {
        padding: 1.5rem 0;
    }
    
    /* Style des messages d'alerte */
    .alert {
        border-radius: 0.5rem;
        border-left: 4px solid transparent;
        padding: 1rem 1.25rem;
    }
    
    .alert i {
        margin-right: 0.5rem;
    }
    
    /* Animation pour les boutons de chargement */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Ajustements responsifs */
    @media (max-width: 767.98px) {
        .content-wrapper {
            margin-top: 56px; /* Ajustement pour les écrans mobiles */
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        
        /* Ajustement des boutons pour les petits écrans */
        .btn .d-none.d-md-inline {
            display: none !important;
        }
        
        .btn i {
            margin-right: 0 !important;
        }
    }
    
    .card-header {
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    /* Styles pour les messages d'alerte */
    .alert {
        border-left: 4px solid transparent;
        border-radius: 0.25rem;
    }
    
    .alert-info {
        border-left-color: #17a2b8;
    }
    
    .alert-warning {
        border-left-color: #ffc107;
    }
    
    /* Amélioration de l'espacement */
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    .mt-3 {
        margin-top: 1rem !important;
    }
    
    .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    /* Style pour les icônes */
    .fas, .far {
        margin-right: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialisation des tooltips
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    });
    
    // Fonction pour afficher les notifications
    function showAlert(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
    
    // Fonction pour gérer les erreurs
    function handleError(xhr, status, error) {
        let errorMessage = 'Une erreur est survenue';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            // Gestion des erreurs de validation
            const errors = xhr.responseJSON.errors;
            errorMessage = Object.values(errors).flat().join('<br>');
        } else if (xhr.status === 0) {
            errorMessage = 'Erreur de connexion. Veuillez vérifier votre connexion Internet.';
        }
        showAlert(errorMessage, 'error');
    }
    
    // Gestion de la sauvegarde du prix d'une matière
    function saveMatierePrix(button) {
        const matiereId = button.data('matiere-id');
        const prixInput = $('.prix-matiere[data-matiere-id="' + matiereId + '"]');
        const prix = parseFloat(prixInput.val());
        
        // Validation du prix
        if (isNaN(prix) || prix < 0) {
            showAlert('Veuillez entrer un prix valide (nombre positif)', 'error');
            prixInput.addClass('is-invalid');
            return false;
        }
        
        // Désactiver le bouton pendant la requête
        const originalContent = button.html();
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Envoyer la requête AJAX
        $.ajax({
            url: '{{ route("admin.configuration.salaires.matieres.update-prix", "") }}/' + matiereId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prix_mensuel: prix
            },
            success: function(response) {
                showAlert('Prix mis à jour avec succès', 'success');
                prixInput.removeClass('is-invalid');
                
                // Mettre à jour l'affichage si nécessaire
                if (response.prix_mensuel !== undefined) {
                    prixInput.val(parseFloat(response.prix_mensuel).toFixed(2));
                }
            },
            error: handleError,
            complete: function() {
                // Réactiver le bouton
                button.prop('disabled', false).html(originalContent);
            }
        });
        
        return false;
    }
    
    // Gestionnaire d'événement pour le bouton de sauvegarde du prix
    $(document).on('click', '.btn-save-prix', function(e) {
        e.preventDefault();
        saveMatierePrix($(this));
    });
    
    // Sauvegarde lors de la touche Entrée dans le champ de prix
    $(document).on('keypress', '.prix-matiere', function(e) {
        if (e.which === 13) { // Touche Entrée
            e.preventDefault();
            const matiereId = $(this).data('matiere-id');
            const button = $('.btn-save-prix[data-matiere-id="' + matiereId + '"]');
            saveMatierePrix(button);
        }
    });
    
    // Fonction pour sauvegarder le pourcentage d'un professeur
    function saveProfesseurPourcentage(button) {
        const professeurId = button.data('professeur-id');
        const matiereId = button.data('matiere-id');
        const pourcentageInput = $('.pourcentage-professeur[data-professeur-id="' + professeurId + '"][data-matiere-id="' + matiereId + '"]');
        const pourcentage = parseFloat(pourcentageInput.val());
        
        // Validation du pourcentage
        if (isNaN(pourcentage) || pourcentage < 0 || pourcentage > 100) {
            showAlert('Veuillez entrer un pourcentage valide (entre 0 et 100)', 'error');
            pourcentageInput.addClass('is-invalid');
            return false;
        }
        
        // Désactiver le bouton pendant la requête
        const originalContent = button.html();
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Envoyer la requête AJAX
        $.ajax({
            url: '{{ route("admin.configuration.salaires.professeurs.update-pourcentage") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                professeur_id: professeurId,
                matiere_id: matiereId,
                pourcentage_remuneration: pourcentage
            },
            success: function(response) {
                showAlert('Pourcentage mis à jour avec succès', 'success');
                pourcentageInput.removeClass('is-invalid');
                
                // Mettre à jour l'affichage si nécessaire
                if (response.pourcentage_remuneration !== undefined) {
                    pourcentageInput.val(parseFloat(response.pourcentage_remuneration).toFixed(2));
                }
            },
            error: handleError,
            complete: function() {
                // Réactiver le bouton
                button.prop('disabled', false).html(originalContent);
            }
        });
        
        return false;
    }
    
    // Gestionnaire d'événement pour le bouton de sauvegarde du pourcentage
    $(document).on('click', '.btn-save-pourcentage', function(e) {
        e.preventDefault();
        saveProfesseurPourcentage($(this));
    });
    
    // Sauvegarde lors de la touche Entrée dans le champ de pourcentage
    $(document).on('keypress', '.pourcentage-professeur', function(e) {
        if (e.which === 13) { // Touche Entrée
            e.preventDefault();
            const professeurId = $(this).data('professeur-id');
            const matiereId = $(this).data('matiere-id');
            const button = $('.btn-save-pourcentage[data-professeur-id="' + professeurId + '"][data-matiere-id="' + matiereId + '"]');
            saveProfesseurPourcentage(button);
        }
    });
    
    // Initialisation des tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Gestion de l'ouverture/fermeture des cartes des professeurs
    $(document).on('click', '[data-card-widget="collapse"]', function() {
        const icon = $(this).find('i');
        icon.toggleClass('fa-plus fa-minus');
    });
    
    // Gestion des erreurs de validation côté client
    $('.prix-matiere, .pourcentage-professeur').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush

