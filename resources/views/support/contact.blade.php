@extends('layouts.admin')

@section('title', 'Contactez le support')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Contactez notre équipe
            </h1>
            <p class="mt-4 text-lg text-gray-600">
                Notre équipe est là pour vous aider. Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.
            </p>
        </div>

        <!-- Formulaire de contact -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('support.contact.send') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Informations de contact -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Nom complet -->
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" required
                                    class="py-3 px-4 block w-full shadow-sm focus:ring-primary-500 focus:border-primary-500 border-gray-300 rounded-md">
                            </div>
                        </div>


                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Adresse email <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email" required
                                    class="py-3 px-4 block w-full shadow-sm focus:ring-primary-500 focus:border-primary-500 border-gray-300 rounded-md">
                            </div>
                        </div>

                        <!-- Sujet -->
                        <div class="sm:col-span-2">
                            <label for="subject" class="block text-sm font-medium text-gray-700">
                                Sujet <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select id="subject" name="subject" required
                                    class="py-3 px-4 block w-full shadow-sm focus:ring-primary-500 focus:border-primary-500 border-gray-300 rounded-md">
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="technical">Problème technique</option>
                                    <option value="billing">Facturation et paiements</option>
                                    <option value="account">Mon compte</option>
                                    <option value="features">Fonctionnalités</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="sm:col-span-2">
                            <label for="message" class="block text-sm font-medium text-gray-700">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <textarea id="message" name="message" rows="6" required
                                    class="py-3 px-4 block w-full shadow-sm focus:ring-primary-500 focus:border-primary-500 border-gray-300 rounded-md"></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Décrivez votre demande en détail. Notre équipe vous répondra dans les plus brefs délais.
                            </p>
                        </div>

                        <!-- Pièce jointe -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Pièce jointe (facultatif)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                            <span>Télécharger un fichier</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only">
                                        </label>
                                        <p class="pl-1">ou glissez-déposez</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, PDF jusqu'à 10MB
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('aide') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations de contact alternatives -->
        <div class="mt-12 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Autres façons de nous contacter</h2>
                
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                    <!-- Email -->
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-600">
                            <i class="fas fa-envelope text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Par email</h3>
                            <p class="mt-1 text-sm text-gray-600">support@allotawjih.ma</p>
                            <p class="mt-1 text-sm text-gray-500">Réponse sous 24h</p>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600">
                            <i class="fas fa-phone-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Par téléphone</h3>
                            <p class="mt-1 text-sm text-gray-600">+212 5XX-XXXXXX</p>
                            <p class="mt-1 text-sm text-gray-500">Lun-Ven, 9h-18h</p>
                        </div>
                    </div>

                    <!-- Chat -->
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                            <i class="fas fa-comment-dots text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Chat en direct</h3>
                            <p class="mt-1 text-sm text-gray-600">Discutez avec un agent</p>
                            <p class="mt-1 text-sm text-gray-500">Disponible 24/7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du téléchargement de fichiers
        const fileInput = document.getElementById('file-upload');
        const fileUploadLabel = fileInput?.nextElementSibling;
        
        if (fileInput && fileUploadLabel) {
            fileInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name || 'Aucun fichier sélectionné';
                fileUploadLabel.textContent = fileName;
            });
        }
        
        // Initialisation des sélecteurs avec Select2 si disponible
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#subject').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                width: '100%',
                placeholder: 'Sélectionnez un sujet',
                allowClear: false
            });
        }
    });
</script>
@endpush
@endsection
