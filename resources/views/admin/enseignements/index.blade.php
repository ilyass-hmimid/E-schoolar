@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Enseignements</h3>
                    <a href="{{ route('admin.enseignements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un Enseignement
                    </a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Filtres et recherche -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('admin.enseignements.index') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Rechercher un enseignement..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="ml-2">
                                    <select name="niveau" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tous les niveaux</option>
                                        @foreach($niveaux as $niveau)
                                            <option value="{{ $niveau }}" {{ request('niveau') == $niveau ? 'selected' : '' }}>
                                                {{ $niveau }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="ml-2">
                                    <select name="annee_scolaire" class="form-control" onchange="this.form.submit()">
                                        <option value="">Toutes les années</option>
                                        @foreach($anneesScolaires as $annee)
                                            <option value="{{ $annee }}" {{ request('annee_scolaire') == $annee ? 'selected' : '' }}>
                                                {{ $annee }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.enseignements.export') }}" class="btn btn-success">
                                    <i class="fas fa-file-export"></i> Exporter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des enseignements -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Classe</th>
                                    <th>Professeur</th>
                                    <th>Année Scolaire</th>
                                    <th>Volume Horaire</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enseignements as $enseignement)
                                <tr>
                                    <td>{{ $enseignement->matiere->nom ?? 'Non défini' }}</td>
                                    <td>{{ $enseignement->classe->nom ?? 'Non défini' }}</td>
                                    <td>{{ $enseignement->professeur->name ?? 'Non attribué' }}</td>
                                    <td>{{ $enseignement->annee_scolaire }}</td>
                                    <td>{{ $enseignement->volume_horaire }}h</td>
                                    <td>
                                        <span class="badge bg-{{ $enseignement->is_active ? 'success' : 'secondary' }}">
                                            {{ $enseignement->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.enseignements.show', $enseignement) }}" 
                                               class="btn btn-sm btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.enseignements.edit', $enseignement) }}" 
                                               class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.enseignements.destroy', $enseignement) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignement ?')">
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
                                    <td colspan="7" class="text-center">Aucun enseignement trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $enseignements->appends(request()->query())->links() }}
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
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
    .form-inline .form-group {
        margin-right: 10px;
    }
</style>
@endpush
