@php
    $isEdit = isset($absence) && $absence->exists;
    $route = $isEdit ? route('admin.absences.update', $absence) : route('admin.absences.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? __('packs.absences.titles.edit') : __('packs.absences.titles.create');
    $submitText = $isEdit ? __('packs.absences.buttons.update') : __('packs.absences.buttons.save');
    
    // Récupérer les éléments pour les sélecteurs
    $eleves = $eleves ?? collect();
    $cours = $cours ?? collect();
    $classes = $classes ?? collect();
    $professeurs = $professeurs ?? collect();
    
    // Valeurs par défaut pour le formulaire
    $defaults = [
        'eleve_id' => old('eleve_id', $absence->eleve_id ?? ''),
        'cours_id' => old('cours_id', $absence->cours_id ?? ''),
        'date_absence' => old('date_absence', $absence->date_absence ? $absence->date_absence->format('Y-m-d') : now()->format('Y-m-d')),
        'type_absence' => old('type_absence', $absence->type_absence ?? 'absence'),
        'duree_absence' => old('duree_absence', $absence->duree_absence ?? ''),
        'justifiee' => old('justifiee', $absence->justifiee ?? 0),
        'motif' => old('motif', $absence->motif ?? ''),
        'commentaire' => old('commentaire', $absence->commentaire ?? ''),
    ];
@endphp

@extends('layouts.admin')

@section('title', $title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap-5-theme/select2-bootstrap-5-theme.min.css') }}">
    <style>
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            display: block;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }
        .justificatif-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-upload-btn {
            border: 1px solid #ddd;
            color: #666;
            background-color: #f8f9fa;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .file-name {
            margin-left: 10px;
            color: #666;
        }
        .file-info {
            margin-top: 5px;
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ $title }}</h1>
            <div>
                <a href="{{ route('admin.absences.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('packs.absences.buttons.back_to_list') }}
                </a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ $route }}" method="POST" enctype="multipart/form-data" id="absenceForm">
                    @csrf
                    @method($method)
                    
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Élève -->
                            <div class="mb-3">
                                <label for="eleve_id" class="form-label required">{{ __('packs.absences.fields.student') }}</label>
                                <select name="eleve_id" id="eleve_id" class="form-select select2 @error('eleve_id') is-invalid @enderror" required>
                                    <option value="">{{ __('packs.absences.select_student') }}</option>
                                    @foreach($eleves as $eleve)
                                        <option value="{{ $eleve->id }}" 
                                            {{ $defaults['eleve_id'] == $eleve->id ? 'selected' : '' }}
                                            data-classe="{{ $eleve->classe_id }}">
                                            {{ $eleve->prenom }} {{ $eleve->nom }} 
                                            @if($eleve->classe)
                                                ({{ $eleve->classe->nom }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('eleve_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Cours -->
                            <div class="mb-3">
                                <label for="cours_id" class="form-label required">{{ __('packs.absences.fields.course') }}</label>
                                <select name="cours_id" id="cours_id" class="form-select select2 @error('cours_id') is-invalid @enderror" required>
                                    <option value="">{{ __('packs.absences.select_course') }}</option>
                                    @foreach($cours as $cour)
                                        <option value="{{ $cour->id }}" 
                                            {{ $defaults['cours_id'] == $cour->id ? 'selected' : '' }}
                                            data-professeur="{{ $cour->professeur_id }}"
                                            data-matiere="{{ $cour->matiere->nom ?? '' }}"
                                            data-jour="{{ $cour->jour_semaine }}"
                                            data-heure-debut="{{ $cour->heure_debut }}"
                                            data-heure-fin="{{ $cour->heure_fin }}">
                                            {{ $cour->matiere->nom ?? 'N/A' }} - 
                                            {{ $cour->jour_semaine }} 
                                            ({{ $cour->heure_debut }} - {{ $cour->heure_fin }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('cours_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Date d'absence -->
                            <div class="mb-3">
                                <label for="date_absence" class="form-label required">{{ __('packs.absences.fields.date') }}</label>
                                <input type="date" class="form-control @error('date_absence') is-invalid @enderror" 
                                       id="date_absence" name="date_absence" 
                                       value="{{ $defaults['date_absence'] }}" required>
                                @error('date_absence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Type d'absence -->
                            <div class="mb-3">
                                <label class="form-label required">{{ __('packs.absences.fields.type') }}</label>
                                <div class="btn-group w-100" role="group">
                                    @foreach(['absence' => 'fas fa-user-times', 'retard' => 'fas fa-clock', 'sortie' => 'fas fa-sign-out-alt'] as $type => $icon)
                                        <input type="radio" class="btn-check" name="type_absence" id="type_{{ $type }}" 
                                               value="{{ $type }}" autocomplete="off"
                                               {{ $defaults['type_absence'] == $type ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary d-flex flex-column align-items-center justify-content-center py-3" for="type_{{ $type }}">
                                            <i class="{{ $icon }} fa-2x mb-1"></i>
                                            <span>{{ __('packs.absences.types.' . $type) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('type_absence')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Durée (visible uniquement pour retard ou sortie) -->
                            <div class="mb-3" id="duree_container" style="display: none;">
                                <label for="duree_absence" class="form-label">
                                    {{ __('packs.absences.fields.duration') }} ({{ __('packs.absences.fields.minutes') }})
                                </label>
                                <input type="number" class="form-control @error('duree_absence') is-invalid @enderror" 
                                       id="duree_absence" name="duree_absence" 
                                       value="{{ $defaults['duree_absence'] }}" min="1" max="1440">
                                @error('duree_absence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    {{ __('packs.absences.help.duration') }}
                                </div>
                            </div>
                            
                            <!-- Statut de justification -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="justifiee" name="justifiee" value="1"
                                           {{ $defaults['justifiee'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="justifiee">
                                        {{ __('packs.absences.fields.justified') }}
                                    </label>
                                </div>
                                @error('justifiee')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Justificatif -->
                            <div class="mb-3" id="justificatif_container">
                                <label class="form-label">{{ __('packs.absences.fields.justificatif') }}</label>
                                
                                @if($isEdit && $absence->chemin_justificatif)
                                    <div class="mb-2">
                                        <a href="{{ route('admin.absences.justificatif.download', $absence) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> {{ __('packs.absences.buttons.download_justificatif') }}
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="remove_justificatif">
                                            <i class="fas fa-trash me-1"></i> {{ __('packs.absences.buttons.remove_justificatif') }}
                                        </button>
                                        <input type="hidden" name="remove_justificatif" id="remove_justificatif_input" value="0">
                                    </div>
                                    <div class="file-info">
                                        {{ basename($absence->chemin_justificatif) }}
                                    </div>
                                @else
                                    <div class="file-upload mb-2">
                                        <button type="button" class="file-upload-btn">
                                            <i class="fas fa-upload me-1"></i> {{ __('packs.absences.buttons.choose_file') }}
                                        </button>
                                        <input type="file" class="file-upload-input" id="justificatif" name="justificatif" 
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <span class="file-name" id="file-name">{{ __('packs.absences.buttons.no_file_chosen') }}</span>
                                    </div>
                                    <div class="form-text">
                                        {{ __('packs.absences.help.file_types') }}: PDF, JPG, PNG, DOC, DOCX
                                    </div>
                                    @error('justificatif')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                @endif
                                
                                <!-- Aperçu du fichier -->
                                <div class="mt-2">
                                    <img id="justificatif_preview" class="img-thumbnail justificatif-preview" 
                                         src="#" alt="{{ __('packs.absences.fields.justificatif_preview') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Motif -->
                    <div class="mb-3">
                        <label for="motif" class="form-label">{{ __('packs.absences.fields.reason') }}</label>
                        <input type="text" class="form-control @error('motif') is-invalid @enderror" 
                               id="motif" name="motif" value="{{ $defaults['motif'] }}">
                        @error('motif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Commentaire -->
                    <div class="mb-3">
                        <label for="commentaire" class="form-label">{{ __('packs.absences.fields.comments') }}</label>
                        <textarea class="form-control @error('commentaire') is-invalid @enderror" 
                                  id="commentaire" name="commentaire" rows="3">{{ $defaults['commentaire'] }}</textarea>
                        @error('commentaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.absences.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> {{ __('packs.absences.buttons.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ $submitText }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __('packs.absences.select_placeholder') }}',
                allowClear: true,
                dropdownParent: $('#absenceForm')
            });
            
            // Afficher/masquer le champ durée en fonction du type d'absence
            function toggleDureeField() {
                const typeAbsence = document.querySelector('input[name="type_absence"]:checked').value;
                const dureeContainer = document.getElementById('duree_container');
                const dureeInput = document.getElementById('duree_absence');
                
                if (typeAbsence === 'retard' || typeAbsence === 'sortie') {
                    dureeContainer.style.display = 'block';
                    dureeInput.required = true;
                } else {
                    dureeContainer.style.display = 'none';
                    dureeInput.required = false;
                }
            }
            
            // Écouter les changements sur les boutons radio de type d'absence
            document.querySelectorAll('input[name="type_absence"]').forEach(radio => {
                radio.addEventListener('change', toggleDureeField);
            });
            
            // Initialiser l'état du champ durée
            toggleDureeField();
            
            // Gestion du téléversement de fichier
            const fileInput = document.getElementById('justificatif');
            const fileName = document.getElementById('file-name');
            const preview = document.getElementById('justificatif_preview');
            
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        fileName.textContent = this.files[0].name;
                        
                        // Afficher un aperçu pour les images
                        if (this.files[0].type.match('image.*')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                            }
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            preview.style.display = 'none';
                        }
                    } else {
                        fileName.textContent = '{{ __("packs.absences.buttons.no_file_chosen") }}';
                        preview.style.display = 'none';
                    }
                });
            }
            
            // Gestion de la suppression du justificatif existant
            const removeBtn = document.getElementById('remove_justificatif');
            const removeInput = document.getElementById('remove_justificatif_input');
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (confirm('{{ __("packs.absences.confirm.remove_justificatif") }}')) {
                        removeInput.value = '1';
                        this.closest('.mb-2').style.display = 'none';
                        this.nextElementSibling.style.textDecoration = 'line-through';
                        this.nextElementSibling.style.color = '#6c757d';
                    }
                });
            }
            
            // Filtrer les cours en fonction de la classe de l'élève sélectionné
            const eleveSelect = document.getElementById('eleve_id');
            const coursSelect = document.getElementById('cours_id');
            
            if (eleveSelect && coursSelect) {
                eleveSelect.addEventListener('change', function() {
                    const classeId = this.options[this.selectedIndex].getAttribute('data-classe');
                    
                    // Filtrer les options de cours
                    Array.from(coursSelect.options).forEach(option => {
                        if (option.value === '') return;
                        option.style.display = option.getAttribute('data-classe') === classeId ? '' : 'none';
                    });
                    
                    // Réinitialiser la sélection
                    coursSelect.value = '';
                    $(coursSelect).trigger('change');
                });
                
                // Déclencher l'événement change au chargement si un élève est déjà sélectionné
                if (eleveSelect.value) {
                    eleveSelect.dispatchEvent(new Event('change'));
                }
            }
            
            // Valider la date d'absence par rapport au jour du cours
            const dateInput = document.getElementById('date_absence');
            
            if (dateInput && coursSelect) {
                coursSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (!selectedOption.value) return;
                    
                    const jourSemaine = selectedOption.getAttribute('data-jour');
                    if (!jourSemaine) return;
                    
                    // Mapper les jours de la semaine (0=Dimanche, 1=Lundi, etc.)
                    const jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                    const jourIndex = jours.indexOf(jourSemaine.toLowerCase());
                    
                    if (jourIndex === -1) return;
                    
                    // Définir le jour de la semaine pour la date sélectionnée
                    dateInput.addEventListener('change', function() {
                        const selectedDate = new Date(this.value);
                        const selectedDay = selectedDate.getDay(); // 0=Dimanche, 1=Lundi, etc.
                        
                        if (selectedDay !== jourIndex) {
                            alert('{{ __("packs.absences.messages.wrong_day") }} ' + jourSemaine);
                            this.value = '';
                        }
                    });
                });
            }
        });
    </script>
@endpush
