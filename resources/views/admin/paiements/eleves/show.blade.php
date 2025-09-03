@extends('layouts.admin')

@section('title', 'Détails du Paiement')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.paiements.eleves.index') }}">Paiements des Élèves</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails du Paiement</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Détails du Paiement</h1>
        <div>
            <a href="{{ route('admin.paiements.eleves.edit', $paiement) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i> Modifier
            </a>
            <a href="{{ route('admin.paiements.eleves.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Paiement</h6>
                    <span class="badge bg-{{ $paiement->statut === 'paye' ? 'success' : ($paiement->statut === 'en_retard' ? 'warning' : 'danger') }}">
                        {{ ucfirst(str_replace('_', ' ', $paiement->statut)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-3">Informations de l'Élève</h5>
                            <p><strong>Nom Complet:</strong> {{ $paiement->eleve->nom_complet }}</p>
                            <p><strong>Classe:</strong> {{ $paiement->eleve->classe->nom ?? 'Non défini' }}</p>
                            <p><strong>Période Scolaire:</strong> {{ $paiement->eleve->annee_scolaire ?? 'Non défini' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Détails du Paiement</h5>
                            <p><strong>Mois:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m', $paiement->mois)->format('F Y') }}</p>
                            <p><strong>Montant:</strong> {{ number_format($paiement->montant, 2, ',', ' ') }} DH</p>
                            <p><strong>Date de Paiement:</strong> {{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y') : 'Non payé' }}</p>
                            <p><strong>Mode de Paiement:</strong> {{ $paiement->mode_paiement ? ucfirst($paiement->mode_paiement) : 'Non spécifié' }}</p>
                            @if($paiement->reference_paiement)
                            <p><strong>Référence:</strong> {{ $paiement->reference_paiement }}</p>
                            @endif
                        </div>
                    </div>

                    @if($paiement->notes)
                    <div class="mt-4">
                        <h5>Notes</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($paiement->notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <small class="text-muted">
                            Créé le: {{ $paiement->created_at->format('d/m/Y H:i') }}
                            @if($paiement->enregistrePar)
                            par {{ $paiement->enregistrePar->name }}
                            @endif
                        </small>
                    </div>
                    <div>
                        <small class="text-muted">
                            Dernière mise à jour: {{ $paiement->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Historique des Paiements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des Paiements</h6>
                </div>
                <div class="card-body">
                    @php
                        $historique = \App\Models\PaiementEleve::where('eleve_id', $paiement->eleve_id)
                            ->where('id', '!=', $paiement->id)
                            ->orderBy('mois', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($historique->isEmpty())
                        <p class="text-muted text-center my-3">Aucun autre paiement trouvé</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($historique as $paiementHisto)
                                <a href="{{ route('admin.paiements.eleves.show', $paiementHisto) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            {{ \Carbon\Carbon::createFromFormat('Y-m', $paiementHisto->mois)->format('F Y') }}
                                        </h6>
                                        <span class="badge bg-{{ $paiementHisto->statut === 'paye' ? 'success' : ($paiementHisto->statut === 'en_retard' ? 'warning' : 'danger') }} ms-2">
                                            {{ ucfirst(str_replace('_', ' ', $paiementHisto->statut)) }}
                                        </span>
                                    </div>
                                    <p class="mb-1">{{ number_format($paiementHisto->montant, 2, ',', ' ') }} DH</p>
                                    <small class="text-muted">
                                        {{ $paiementHisto->date_paiement ? $paiementHisto->date_paiement->format('d/m/Y') : 'Non payé' }}
                                    </small>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('admin.paiements.eleves.index', ['eleve_id' => $paiement->eleve_id]) }}" class="btn btn-sm btn-outline-primary">
                                Voir tout l'historique
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($paiement->statut !== 'paye')
                            <form action="{{ route('admin.paiements.eleves.update-status', $paiement) }}" method="POST" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="statut" value="paye">
                                <button type="submit" class="btn btn-success mb-2">
                                    <i class="fas fa-check-circle me-2"></i>Marquer comme Payé
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.paiements.eleves.edit', $paiement) }}" class="btn btn-primary mb-2">
                            <i class="fas fa-edit me-2"></i>Modifier le Paiement
                        </a>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-2"></i>Supprimer le Paiement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce paiement ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.paiements.eleves.destroy', $paiement) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
