@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <!-- Afficher les erreurs d'importation si présentes -->
    @if(isset($import_errors) && count($import_errors) > 0)
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Erreurs d'importation</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Ligne</th>
                                <th>Message</th>
                                <th>Valeurs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($import_errors as $index => $error)
                                <tr class="{{ isset($error['type']) && $error['type'] === 'warning' ? 'bg-warning-light' : 'bg-danger-light' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>Ligne {{ $error['row'] ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($error['message']))
                                            {{ $error['message'] }}
                                        @elseif(isset($error['errors']))
                                            @if(is_array($error['errors']))
                                                @foreach($error['errors'] as $err)
                                                    <div>{{ $err }}</div>
                                                @endforeach
                                            @else
                                                {{ $error['errors'] }}
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($error['values']))
                                            <pre class="mb-0">{{ json_encode($error['values'], JSON_PRETTY_PRINT) }}</pre>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Centres</h3>
                    <div class="d-flex gap-2">
                        <!-- Bouton d'export -->
                        <a href="{{ route('admin.centres.export') }}" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                        
                        <!-- Bouton d'import avec modal -->
                        <button type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-import"></i> Importer
                        </button>
                        
                        <!-- Bouton d'ajout -->
                        <a href="{{ route('admin.centres.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter un Centre
                        </a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Filtres et recherche -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('admin.centres.index') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Rechercher un centre..." value="{{ request('search') }}">
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
                                <!-- Bouton Import avec modal -->
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#importModal">
                                    <i class="fas fa-file-import"></i> Importer
                                </button>
                                <!-- Bouton Export -->
                                <a href="{{ route('admin.centres.export') }}" class="btn btn-success">
                                    <i class="fas fa-file-export"></i> Exporter
                                </a>
                            </div>
                        </div>
                        
                        <!-- Modal d'importation -->
                        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="importModalLabel">Importer des centres</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.centres.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="file">Sélectionner un fichier Excel (.xlsx, .xls, .csv)</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                                                    <label class="custom-file-label" for="file">Choisir un fichier</label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Téléchargez notre <a href="#" id="downloadTemplate">modèle Excel</a> pour un format correct.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">Importer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des centres -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Responsable</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($centres as $centre)
                                <tr>
                                    <td>{{ $centre->nom }}</td>
                                    <td>{{ $centre->adresse }}</td>
                                    <td>{{ $centre->telephone }}</td>
                                    <td>{{ $centre->email }}</td>
                                    <td>{{ $centre->responsable->name ?? 'Non défini' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $centre->is_active ? 'success' : 'secondary' }}">
                                            {{ $centre->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.centres.show', $centre) }}" 
                                               class="btn btn-sm btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.centres.edit', $centre) }}" 
                                               class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.centres.destroy', $centre) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce centre ?')">
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
                                    <td colspan="7" class="text-center">Aucun centre trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $centres->links() }}
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
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import mr-2"></i>Importer des centres
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.centres.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="importFile">Sélectionner un fichier Excel/CSV</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="importFile" id="importFileLabel">Choisir un fichier</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Formats acceptés: .xlsx, .xls, .csv (max: 10 Mo)
                        </small>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Instructions d'importation</h6>
                        <ul class="mb-0 pl-3">
                            <li>Téléchargez d'abord le <a href="#" id="downloadTemplate">modèle d'importation</a></li>
                            <li>Les champs marqués d'un astérisque (*) sont obligatoires</li>
                            <li>Assurez-vous que les adresses email sont uniques</li>
                            <li>Le statut peut être 'Actif' ou 'Inactif' (par défaut: Actif)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-upload mr-1"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Afficher le nom du fichier sélectionné
    document.getElementById('importFile').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir un fichier';
        document.getElementById('importFileLabel').textContent = fileName;
    });
    
    // Télécharger le modèle d'importation
    document.getElementById('downloadTemplate').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Créer un tableau avec les en-têtes
        const headers = [
            'Nom*', 'Adresse*', 'Ville*', 'Pays', 'Téléphone*', 
            'Email*', 'Responsable (email)', 'Description', 'Statut (Actif/Inactif)'
        ];
        
        // Créer un exemple de données
        const data = [
            headers,
            [
                'Centre Principal', '123 Rue des Écoles', 'Casablanca', 'Maroc', 
                '+212600000000', 'contact@ecole.ma', 'admin@ecole.ma', 
                'Description du centre principal', 'Actif'
            ],
            [
                'Centre Secondaire', '456 Avenue des Étudiants', 'Rabat', 'Maroc', 
                '+212600000001', 'contact2@ecole.ma', 'responsable@ecole.ma', 
                'Description du centre secondaire', 'Actif'
            ]
        ];
        
        // Convertir en CSV
        let csvContent = data.map(row => 
            row.map(field => `"${field}"`).join(',')
        ).join('\n');
        
        // Créer un blob et un lien de téléchargement
        const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'modele_import_centres.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
@endpush

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
    .custom-file-label::after {
        content: "Parcourir";
    }
</style>
@endpush

@push('scripts')
<script>
    // Afficher le nom du fichier sélectionné
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = document.getElementById("file").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });

    // Gérer le téléchargement du modèle Excel
    document.getElementById('downloadTemplate').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Créer un fichier Excel vide avec les en-têtes
        const headers = [
            'Nom', 'Adresse', 'Ville', 'Pays', 'Téléphone', 'Email', 
            'Responsable (email)', 'Description', 'Statut (actif/inactif)'
        ];
        
        // Créer un fichier Excel temporaire
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += headers.join(",") + "\r\n";
        
        // Créer un lien de téléchargement
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "modele_import_centres.csv");
        document.body.appendChild(link);
        
        // Déclencher le téléchargement
        link.click();
        document.body.removeChild(link);
    });
</script>
@endpush
