@extends('layouts.professeur')

@section('title', 'Déposer une réclamation')

@push('styles')
<style>
    .file-upload {
        @apply mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md;
    }
    .file-upload-content {
        @apply space-y-1 text-center;
    }
    .file-upload-icon {
        @apply mx-auto h-12 w-12 text-gray-400;
    }
    .file-upload-text {
        @apply text-sm text-gray-600;
    }
    .file-upload-input {
        @apply sr-only;
    }
    .file-preview {
        @apply mt-2 flex items-center text-sm text-gray-600;
    }
    .file-preview-icon {
        @apply flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Déposer une réclamation</h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('professeur.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('professeur.salaires.index') }}" class="text-blue-600 hover:text-blue-800 ml-1 md:ml-2 text-sm font-medium">Mes Salaires</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('professeur.salaires.show', $salaire) }}" class="text-blue-600 hover:text-blue-800 ml-1 md:ml-2 text-sm font-medium">Salaire {{ \Carbon\Carbon::parse($salaire->periode)->format('m/Y') }}</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-500 ml-1 md:ml-2 text-sm font-medium">Réclamation</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Formulaire de réclamation</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Veuillez remplir ce formulaire pour nous signaler un problème concernant votre salaire du 
                    <span class="font-medium">{{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}</span>.
                </p>
            </div>
            
            <form action="{{ route('professeur.salaires.traiter-reclamation', $salaire) }}" method="POST" enctype="multipart/form-data" class="px-4 py-5 sm:p-6">
                @csrf
                
                <div class="space-y-6">
                    <!-- Informations sur le salaire -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-blue-700 font-medium">Période</p>
                                <p class="text-blue-900">{{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}</p>
                            </div>
                            <div>
                                <p class="text-blue-700 font-medium">Référence</p>
                                <p class="text-blue-900">{{ $salaire->reference }}</p>
                            </div>
                            <div>
                                <p class="text-blue-700 font-medium">Salaire net</p>
                                <p class="text-blue-900 font-medium">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</p>
                            </div>
                            <div>
                                <p class="text-blue-700 font-medium">Date de paiement</p>
                                <p class="text-blue-900">{{ $salaire->date_paiement ? \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') : 'Non payé' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sujet de la réclamation -->
                    <div>
                        <label for="sujet" class="block text-sm font-medium text-gray-700">Sujet de la réclamation <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="sujet" id="sujet" required
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Ex.: Erreur sur le calcul des heures supplémentaires"
                                   value="{{ old('sujet') }}">
                        </div>
                        @error('sujet')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Message détaillé -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Description détaillée <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <textarea id="message" name="message" rows="6" required
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Décrivez de manière détaillée le problème rencontré...">{{ old('message') }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Soyez aussi précis que possible pour nous aider à résoudre votre problème rapidement.
                        </p>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Pièce jointe -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pièce jointe (optionnel)</label>
                        <div class="mt-1">
                            <div class="file-upload">
                                <div class="file-upload-content">
                                    <svg class="file-upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <div class="file-upload-text">
                                        <label for="piece_jointe" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Téléverser un fichier</span>
                                            <input id="piece_jointe" name="piece_jointe" type="file" class="file-upload-input">
                                        </label>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, PDF (max. 5 Mo)
                                        </p>
                                    </div>
                                    <div id="file-preview" class="hidden mt-2">
                                        <div class="file-preview">
                                            <svg class="file-preview-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span id="file-name"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Vous pouvez joindre une capture d'écran, un fichier PDF ou tout autre document justificatif.
                        </p>
                        @error('piece_jointe')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Informations supplémentaires -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Notre équipe examinera votre réclamation dans les plus brefs délais. Vous serez informé(e) par email de l'avancement du traitement.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('professeur.salaires.show', $salaire) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Envoyer la réclamation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'affichage du nom du fichier sélectionné
        const fileInput = document.getElementById('piece_jointe');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    fileName.textContent = this.files[0].name;
                    filePreview.classList.remove('hidden');
                } else {
                    filePreview.classList.add('hidden');
                }
            });
        }
        
        // Validation du formulaire
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const sujet = document.getElementById('sujet').value.trim();
                const message = document.getElementById('message').value.trim();
                
                if (!sujet || !message) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs obligatoires.');
                    return false;
                }
                
                // Désactiver le bouton de soumission pour éviter les doubles clics
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Envoi en cours...
                    `;
                }
            });
        }
    });
</script>
@endpush
