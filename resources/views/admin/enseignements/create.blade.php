@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ajouter un nouvel enseignement</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.enseignements.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('admin.enseignements.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="matiere_id">Matière *</label>
                                    <select class="form-control select2 @error('matiere_id') is-invalid @enderror" 
                                            id="matiere_id" name="matiere_id" required>
                                        <option value="">Sélectionner une matière</option>
                                        @foreach($matieres as $matiere)
                                            <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                                {{ $matiere->nom }} ({{ $matiere->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('matiere_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="classe_id">Classe *</label>
                                    <select class="form-control select2 @error('classe_id') is-invalid @enderror" 
                                            id="classe_id" name="classe_id" required>
                                        <option value="">Sélectionner une classe</option>
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                                {{ $classe->nom }} ({{ $classe->niveau }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('classe_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="professeur_id">Professeur *</label>
                                    <select class="form-control select2 @error('professeur_id') is-invalid @enderror" 
                                            id="professeur_id" name="professeur_id" required>
                                        <option value="">Sélectionner un professeur</option>
                                        @foreach($professeurs as $professeur)
                                            <option value="{{ $professeur->id }}" {{ old('professeur_id') == $professeur->id ? 'selected' : '' }}>
                                                {{ $professeur->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('professeur_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="annee_scolaire">Année Scolaire *</label>
                                    <input type="text" class="form-control @error('annee_scolaire') is-invalid @enderror" 
                                           id="annee_scolaire" name="annee_scolaire" 
                                           value="{{ old('annee_scolaire', $anneeScolaireActuelle) }}" required>
                                    @error('annee_scolaire')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="volume_horaire">Volume Horaire (heures) *</label>
                                    <input type="number" class="form-control @error('volume_horaire') is-invalid @enderror" 
                                           id="volume_horaire" name="volume_horaire" 
                                           value="{{ old('volume_horaire', 0) }}" min="0" step="0.5" required>
                                    @error('volume_horaire')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="coefficient">Coefficient *</label>
                                    <input type="number" class="form-control @error('coefficient') is-invalid @enderror" 
                                           id="coefficient" name="coefficient" 
                                           value="{{ old('coefficient', 1) }}" min="1" required>
                                    @error('coefficient')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="semestre">Semestre *</label>
                                    <select class="form-control @error('semestre') is-invalid @enderror" 
                                            id="semestre" name="semestre" required>
                                        <option value="1" {{ old('semestre') == '1' ? 'selected' : '' }}>Semestre 1</option>
                                        <option value="2" {{ old('semestre') == '2' ? 'selected' : '' }}>Semestre 2</option>
                                    </select>
                                    @error('semestre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Enseignement actif</label>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="{{ route('admin.enseignements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script>
$(function () {
    // Initialiser Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Sélectionner une option',
        allowClear: true
    });
    
    // Validation du formulaire côté client
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });
});
</script>
@endpush
