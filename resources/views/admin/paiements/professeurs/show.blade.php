@extends('layouts.admin')

@section('title', 'Détails du Paiement Enseignant #' . $paiement->id)

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.paiements.professeurs.index') }}">Paiements des Enseignants</a></li>
            <li class="breadcrumb-item active" aria-current="page">Paiement #{{ $paiement->id }}</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails du Paiement #{{ $paiement->id }}</h1>
        
        <div class="btn-group">
            <a href="{{ route('admin.paiements.professeurs.edit', $paiement) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.paiements.professeurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Paiement</h6>
                    <span class="badge bg-{{ $paiement->statut === 'paye' ? 'success' : ($paiement->statut === 'en_retard' ? 'warning' : 'danger') }}">
                        {{ $statuts[$paiement->statut] ?? $paiement->statut }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informations de l'Enseignant</h5>
                            <hr>
                            <p>
                                <strong>Nom complet:</strong> 
                                <a href="{{ route('admin.professeurs.show', $paiement->professeur_id) }}">
                                    {{ $paiement->professeur->nom }} {{ $paiement->professeur->prenom }}
                                </a>
                            </p>
                            <p><strong>Email:</strong> {{ $paiement->professeur->email }}</p>
                            <p><strong>Téléphone:</strong> {{ $paiement->professeur->telephone ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Détails du Paiement</h5>
                            <hr>
                            <p><strong>Référence:</strong> {{ $paiement->reference_paiement ?? 'Non spécifiée' }}</p>
                            <p><strong>Mois:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m', $paiement->mois)->format('F Y') }}</p>
                            <p><strong>Montant:</strong> {{ number_format($paiement->montant, 2, ',', ' ') }} DH</p>
                            <p><strong>Mode de paiement:</strong> {{ $paiement->mode_paiement ? MethodePaiement::from($paiement->mode_paiement)->label() : 'Non spécifié' }}</p>
                            <p><strong>Date de paiement:</strong> {{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>

                    @if($paiement->heures_travaillees || $paiement->taux_horaire)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Détails du Calcul</h5>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Heures Travaillées</th>
                                            <th>Taux Horaire</th>
                                            <th>Montant Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $paiement->heures_travaillees ?? 0 }} heures</td>
                                            <td>{{ number_format($paiement->taux_horaire ?? 0, 2, ',', ' ') }} DH/h</td>
                                            <td>{{ number_format($paiement->montant, 2, ',', ' ') }} DH</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($paiement->notes)
                    <div class="row">
                        <div class="col-12">
                            <h5>Notes</h5>
                            <hr>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($paiement->notes)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Dernière mise à jour</h6>
                        <p class="mb-1">{{ $paiement->updated_at->format('d/m/Y H:i') }}</p>
                        <small class="text-muted">Par: {{ $paiement->enregistrePar->name ?? 'Système' }}</small>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Créé le</h6>
                        <p>{{ $paiement->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <hr>
                    
                    <div>
                        <h6>Actions Rapides</h6>
                        <div class="d-grid gap-2">
                            @if($paiement->statut !== 'paye')
                                <form action="{{ route('admin.paiements.professeurs.update-status', $paiement) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="paye">
                                    <button type="submit" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-check"></i> Marquer comme Payé
                                    </button>
                                </form>
                            @endif
                            
                            @if($paiement->statut !== 'en_retard')
                                <form action="{{ route('admin.paiements.professeurs.update-status', $paiement) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="en_retard">
                                    <button type="submit" class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-clock"></i> Marquer en Retard
                                    </button>
                                </form>
                            @endif
                            
                            @if($paiement->statut !== 'impaye')
                                <form action="{{ route('admin.paiements.professeurs.update-status', $paiement) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="impaye">
                                    <button type="submit" class="btn btn-danger w-100 mb-2">
                                        <i class="fas fa-times"></i> Marquer comme Impayé
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Derniers Paiements</h6>
                </div>
                <div class="card-body">
                    @php
                        $derniersPaiements = \App\Models\PaiementProfesseur::where('professeur_id', $paiement->professeur_id)
                            ->where('id', '!=', $paiement->id)
                            ->orderBy('mois', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @if($derniersPaiements->isNotEmpty())
                        <div class="list-group">
                            @foreach($derniersPaiements as $dernierPaiement)
                                <a href="{{ route('admin.paiements.professeurs.show', $dernierPaiement) }}" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $dernierPaiement->mois)->format('F Y') }}</span>
                                    <span class="badge bg-{{ $dernierPaiement->statut === 'paye' ? 'success' : ($dernierPaiement->statut === 'en_retard' ? 'warning' : 'danger') }} rounded-pill">
                                        {{ number_format($dernierPaiement->montant, 0, ',', ' ') }} DH
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun autre paiement trouvé pour cet enseignant.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
