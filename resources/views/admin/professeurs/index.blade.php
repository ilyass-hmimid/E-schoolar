@extends('layouts.admin')

@section('title', 'Gestion des professeurs')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Gestion des professeurs</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.professeurs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un professeur
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Matières</th>
                                        <th>Élèves</th>
                                        <th>Salaire mensuel</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($professeurs as $professeur)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.professeurs.show', $professeur) }}">
                                                    {{ $professeur->nom }} {{ $professeur->prenom }}
                                                </a>
                                            </td>
                                            <td>{{ $professeur->email }}</td>
                                            <td>{{ $professeur->telephone }}</td>
                                            <td>{{ $professeur->matieres_count }}</td>
                                            <td>{{ $professeur->matieres->sum('eleves_count') }}</td>
                                            <td>{{ number_format($professeur->salaire_mensuel, 2, ',', ' ') }} DH</td>
                                            <td>
                                                @if($professeur->est_actif)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-danger">Inactif</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('admin.professeurs.show', $professeur) }}" class="btn btn-info btn-sm" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.professeurs.edit', $professeur) }}" class="btn btn-primary btn-sm" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.professeurs.destroy', $professeur) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce professeur ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Aucun professeur trouvé</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $professeurs->links() }}
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
@stop

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
    .badge {
        margin-right: 3px;
        margin-bottom: 3px;
        display: inline-block;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialisation des tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Gestion des messages flash
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
});
</script>
@endpush
