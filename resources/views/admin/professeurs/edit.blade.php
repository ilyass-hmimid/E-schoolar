@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le professeur : {{ $professeur->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.professeurs.show', $professeur) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="{{ route('admin.professeurs.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('admin.professeurs.update', $professeur) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom *</label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $professeur->nom) }}" required>
                                    @error('nom')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenom">Prénom *</label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" name="prenom" value="{{ old('prenom', $professeur->prenom) }}" required>
                                    @error('prenom')
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">Téléphone *</label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone', $professeur->telephone) }}" required>
                                    @error('telephone')
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lieu_naissance">Lieu de naissance</label>
                                    <input type="text" class="form-control @error('lieu_naissance') is-invalid @enderror" 
                                           id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance', $professeur->lieu_naissance) }}">
                                    @error('lieu_naissance')
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
                                    <label for="specialite">Spécialité *</label>
                                    <input type="text" class="form-control @error('specialite') is-invalid @enderror" 
                                           id="specialite" name="specialite" value="{{ old('specialite', $professeur->specialite) }}" required>
                                    @error('specialite')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diplome">Diplôme le plus élevé</label>
                                    <input type="text" class="form-control @error('diplome') is-invalid @enderror" 
                                           id="diplome" name="diplome" value="{{ old('diplome', $professeur->diplome) }}">
                                    @error('diplome')
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
                                    <label for="date_embauche">Date d'embauche *</label>
                                    <input type="date" class="form-control @error('date_embauche') is-invalid @enderror" 
                                           id="date_embauche" name="date_embauche" 
                                           value="{{ old('date_embauche', $professeur->date_embauche ? $professeur->date_embauche->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                                    @error('date_embauche')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="statut">Statut *</label>
                                    <select class="form-control @error('statut') is-invalid @enderror" 
                                            id="statut" name="statut" required>
                                        <option value="permanent" {{ old('statut', $professeur->statut) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="vacataire" {{ old('statut', $professeur->statut) == 'vacataire' ? 'selected' : '' }}>Vacataire</option>
                                        <option value="stagiaire" {{ old('statut', $professeur->statut) == 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                                    </select>
                                    @error('statut')
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ville">Ville</label>
                                    <input type="text" class="form-control @error('ville') is-invalid @enderror" 
                                           id="ville" name="ville" value="{{ old('ville', $professeur->ville) }}">
                                    @error('ville')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pays">Pays</label>
                                    <input type="text" class="form-control @error('pays') is-invalid @enderror" 
                                           id="pays" name="pays" value="{{ old('pays', $professeur->pays ?? 'Maroc') }}">
                                    @error('pays')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="avatar">Photo de profil</label>
                            @if($professeur->avatar)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $professeur->avatar) }}" alt="Photo de profil" 
                                         class="img-thumbnail" style="max-width: 150px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_avatar" name="remove_avatar" value="1">
                                        <label class="form-check-label" for="remove_avatar">Supprimer la photo</label>
                                    </div>
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

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', $professeur->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Compte activé</label>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('admin.professeurs.index') }}" class="btn btn-secondary">
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
