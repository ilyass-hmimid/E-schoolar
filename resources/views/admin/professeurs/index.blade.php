@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Professeurs</h3>
                    <a href="{{ route('admin.professeurs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un Professeur
                    </a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Filtres et recherche -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('admin.professeurs.index') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Rechercher un professeur..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#importModal">
                                    <i class="fas fa-file-import"></i> Importer
                                </button>
                                <a href="{{ route('admin.professeurs.export') }}" class="btn btn-success">
                                    <i class="fas fa-file-export"></i> Exporter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des professeurs -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Nom & Prénom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Spécialité</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($professeurs as $professeur)
                                <tr>
                                    <td class="text-center">
                                        <img src="{{ $professeur->avatar ? asset('storage/' . $professeur->avatar) : asset('img/default-avatar.png') }}" 
                                             alt="Photo" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    </td>
                                    <td>{{ $professeur->name }}</td>
                                    <td>{{ $professeur->email }}</td>
                                    <td>{{ $professeur->telephone ?? 'Non défini' }}</td>
                                    <td>{{ $professeur->specialite ?? 'Non définie' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $professeur->is_active ? 'success' : 'secondary' }}">
                                            {{ $professeur->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.professeurs.show', $professeur) }}" 
                                               class="btn btn-sm btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.professeurs.edit', $professeur) }}" 
                                               class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.professeurs.destroy', $professeur) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce professeur ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucun professeur trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $professeurs->links() }}
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<!-- Modal d'importation -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Importer des professeurs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.professeurs.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Fichier Excel (.xlsx, .xls)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx, .xls" required>
                            <label class="custom-file-label" for="file">Choisir un fichier</label>
                        </div>
                        <small class="form-text text-muted">
                            Téléchargez le <a href="{{ asset('templates/import_professeurs.xlsx') }}">modèle d'importation</a> pour le format correct.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .img-circle {
        border-radius: 50%;
    }
</style>
@endpush

@push('scripts')
<!-- bs-custom-file-input -->
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<script>
$(function () {
    // Gestion de l'affichage du nom du fichier sélectionné
    bsCustomFileInput.init();
});
</script>
@endpush
