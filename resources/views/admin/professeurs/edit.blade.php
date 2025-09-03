@extends('layouts.admin')

@section('title', 'Modifier un professeur')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Modifier le professeur : {{ $professeur->name }}</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.professeurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
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
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.professeurs.update', $professeur) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nom complet *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $professeur->name) }}" required>
                                        @error('name')
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
                                               id="email" name="email" value="{{ old('email', $professeur->email) }}" required>
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
                                        <label for="telephone">Téléphone</label>
                                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                               id="telephone" name="telephone" value="{{ old('telephone', $professeur->telephone) }}">
                                        @error('telephone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_naissance">Date de naissance</label>
                                        <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" 
                                               id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $professeur->date_naissance ? $professeur->date_naissance->format('Y-m-d') : '') }}">
                                        @error('date_naissance')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                          id="adresse" name="adresse" rows="2">{{ old('adresse', $professeur->adresse) }}</textarea>
                                @error('adresse')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="matieres">Matières enseignées *</label>
                                <select class="form-control select2 @error('matieres') is-invalid @enderror" 
                                        id="matieres" name="matieres[]" multiple="multiple" required>
                                    @foreach($matieres as $matiere)
                                        <option value="{{ $matiere->id }}" 
                                            {{ in_array($matiere->id, old('matieres', $professeur->matieres->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $matiere->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('matieres')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Nouveau mot de passe</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password">
                                        <small class="form-text text-muted">Laissez vide pour ne pas changer</small>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" 
                                           name="is_active" value="1" {{ old('is_active', $professeur->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Compte activé</label>
                                </div>
                            </div>

                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                                <a href="{{ route('admin.professeurs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Annuler
                                </a>
                            </div>

                            <div class="form-group">
                                <label for="avatar">Photo de profil</label>
                                @if($professeur->avatar)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $professeur->avatar) }}" alt="Photo de profil" 
                                             class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" 
                                           id="avatar" name="avatar">
                                    <label class="custom-file-label" for="avatar">Changer la photo</label>
                                    @error('avatar')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG, JPEG. Taille max: 2MB</small>
                            </div>
                        </form>
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

@push('styles')
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialisation de Select2 pour les matières
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: 'Sélectionnez les matières',
        allowClear: true,
        width: '100%'
    });
    
    // Gestion des messages d'erreur
    @if($errors->any())
        @if($errors->has('matieres'))
            $('.select2').addClass('is-invalid');
        @endif
    @endif
    
    // Désactiver le bouton de soumission après l'envoi du formulaire
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]')
               .prop('disabled', true)
               .html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');
    });
});
</script>
@endpush
