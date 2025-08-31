@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le centre : {{ $centre->nom }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.centres.show', $centre) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="{{ route('admin.centres.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('admin.centres.update', $centre) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom du centre *</label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $centre->nom) }}" required>
                                    @error('nom')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $centre->email) }}" required>
                                    @error('email')
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
                                    <label for="telephone">Téléphone *</label>
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone', $centre->telephone) }}" required>
                                    @error('telephone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="responsable_id">Responsable</label>
                                    <select class="form-control @error('responsable_id') is-invalid @enderror" 
                                            id="responsable_id" name="responsable_id">
                                        <option value="">Sélectionner un responsable</option>
                                        @foreach($responsables as $responsable)
                                            <option value="{{ $responsable->id }}" 
                                                {{ old('responsable_id', $centre->responsable_id) == $responsable->id ? 'selected' : '' }}>
                                                {{ $responsable->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('responsable_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="adresse">Adresse *</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="3" required>{{ old('adresse', $centre->adresse) }}</textarea>
                            @error('adresse')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ville">Ville *</label>
                                    <input type="text" class="form-control @error('ville') is-invalid @enderror" 
                                           id="ville" name="ville" value="{{ old('ville', $centre->ville) }}" required>
                                    @error('ville')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pays">Pays *</label>
                                    <input type="text" class="form-control @error('pays') is-invalid @enderror" 
                                           id="pays" name="pays" value="{{ old('pays', $centre->pays) }}" required>
                                    @error('pays')
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
                                      id="description" name="description" rows="3">{{ old('description', $centre->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', $centre->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Centre actif</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="logo">Logo</label>
                            @if($centre->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $centre->logo) }}" alt="Logo" class="img-thumbnail" style="max-width: 150px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo" value="1">
                                        <label class="form-check-label" for="remove_logo">Supprimer le logo</label>
                                    </div>
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" 
                                       id="logo" name="logo">
                                <label class="custom-file-label" for="logo">Changer le logo</label>
                                @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Format: JPG, PNG, JPEG. Taille max: 2MB</small>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('admin.centres.index') }}" class="btn btn-secondary">
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

@push('scripts')
<!-- bs-custom-file-input -->
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<script>
$(function () {
    // Gestion de l'affichage du nom du fichier sélectionné
    bsCustomFileInput.init();
    
    // Validation du formulaire côté client
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });
});
</script>
@endpush
