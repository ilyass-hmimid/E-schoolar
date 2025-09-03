@extends('layouts.admin')

@section('title', 'Paiements des Élèves')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des Paiements des Élèves</h1>
        <a href="{{ route('admin.paiements.eleves.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Paiement
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Paiements</h6>
            <div class="d-flex">
                <input type="month" id="filter-month" class="form-control me-2" value="{{ request('mois', now()->format('Y-m')) }}">
                <select id="filter-status" class="form-select me-2" style="width: 150px;">
                    <option value="">Tous les statuts</option>
                    <option value="paye" {{ request('statut') === 'paye' ? 'selected' : '' }}>Payé</option>
                    <option value="en_retard" {{ request('statut') === 'en_retard' ? 'selected' : '' }}>En retard</option>
                    <option value="impaye" {{ request('statut') === 'impaye' ? 'selected' : '' }}>Impayé</option>
                </select>
                <button id="apply-filters" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="paiementsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Élève</th>
                            <th>Classe</th>
                            <th>Mois</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date Paiement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $paiement)
                        <tr>
                            <td>{{ $paiement->eleve->nom_complet }}</td>
                            <td>{{ $paiement->eleve->classe->nom ?? 'Non défini' }}</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $paiement->mois)->format('m/Y') }}</td>
                            <td>{{ number_format($paiement->montant, 2, ',', ' ') }} DH</td>
                            <td>
                                <span class="badge bg-{{ $paiement->statut === 'paye' ? 'success' : ($paiement->statut === 'en_retard' ? 'warning' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $paiement->statut)) }}
                                </span>
                            </td>
                            <td>{{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y') : '-' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.paiements.eleves.edit', $paiement) }}" class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success" 
                                            onclick="updateStatus({{ $paiement->id }}, 'paye')" 
                                            title="Marquer comme payé">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning text-white" 
                                            onclick="updateStatus({{ $paiement->id }}, 'en_retard')" 
                                            title="Marquer en retard">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal" 
                                            data-id="{{ $paiement->id }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucun paiement trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($paiements->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $paiements->withQueryString()->links() }}
            </div>
            @endif
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
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form -->
<form id="statusUpdateForm" method="POST" action="">
    @csrf
    @method('PATCH')
    <input type="hidden" name="statut" id="statusInput">
</form>

@endsection

@push('scripts')
<script>
    // Delete modal
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const form = document.getElementById('deleteForm');
                form.action = `/admin/paiements/eleves/${id}`;
            });
        }

        // Apply filters
        document.getElementById('apply-filters').addEventListener('click', function() {
            const mois = document.getElementById('filter-month').value;
            const statut = document.getElementById('filter-status').value;
            
            let url = new URL(window.location.href.split('?')[0]);
            let params = new URLSearchParams();
            
            if (mois) params.append('mois', mois);
            if (statut) params.append('statut', statut);
            
            window.location.href = `${url}?${params.toString()}`;
        });
    });

    // Update payment status
    function updateStatus(id, statut) {
        if (confirm('Voulez-vous vraiment mettre à jour le statut de ce paiement ?')) {
            const form = document.getElementById('statusUpdateForm');
            form.action = `/admin/paiements/eleves/${id}/status`;
            document.getElementById('statusInput').value = statut;
            form.submit();
        }
    }
</script>
@endpush
