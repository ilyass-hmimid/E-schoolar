@extends('layouts.admin')

@section('title', 'Gestion des Paiements des Enseignants')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Paiements des Enseignants</h1>
        <a href="{{ route('admin.paiements.professeurs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Paiement
        </a>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.paiements.professeurs.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="professeur_id" class="form-label">Enseignant</label>
                    <select name="professeur_id" id="professeur_id" class="form-select">
                        <option value="">Tous les enseignants</option>
                        @foreach($professeurs as $prof)
                            <option value="{{ $prof->id }}" {{ request('professeur_id') == $prof->id ? 'selected' : '' }}>
                                {{ $prof->nom }} {{ $prof->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="mois" class="form-label">Mois</label>
                    <select name="mois" id="mois" class="form-select">
                        <option value="">Tous les mois</option>
                        @foreach($months as $month)
                            <option value="{{ $month['value'] }}" {{ request('mois') == $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select name="statut" id="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach($statuts as $key => $statut)
                            <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>
                                {{ $statut }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.paiements.professeurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des paiements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Paiements</h6>
            <div class="d-flex">
                <form action="{{ route('admin.paiements.professeurs.index') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Rechercher..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Enseignant</th>
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
                                <td>{{ $paiement->id }}</td>
                                <td>
                                    <a href="{{ route('admin.professeurs.show', $paiement->professeur_id) }}">
                                        {{ $paiement->professeur->nom }} {{ $paiement->professeur->prenom }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $paiement->mois)->format('F Y') }}</td>
                                <td>{{ number_format($paiement->montant, 2, ',', ' ') }} DH</td>
                                <td>
                                    <span class="badge bg-{{ $paiement->statut === 'paye' ? 'success' : ($paiement->statut === 'en_retard' ? 'warning' : 'danger') }}">
                                        {{ $statuts[$paiement->statut] ?? $paiement->statut }}
                                    </span>
                                </td>
                                <td>{{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.paiements.professeurs.show', $paiement) }}" class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.paiements.professeurs.edit', $paiement) }}" class="btn btn-sm btn-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" title="Supprimer" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $paiement->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de confirmation de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $paiement->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $paiement->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $paiement->id }}">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer ce paiement ? Cette action est irréversible.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('admin.paiements.professeurs.destroy', $paiement) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $paiements->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mise à jour du statut par AJAX
    document.querySelectorAll('.status-update').forEach(select => {
        select.addEventListener('change', function() {
            const paiementId = this.dataset.id;
            const newStatus = this.value;
            const url = `/admin/paiements/professeurs/${paiementId}/status`;
            
            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ statut: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour voir les changements
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la mise à jour du statut');
            });
        });
    });
</script>
@endpush
@endsection
