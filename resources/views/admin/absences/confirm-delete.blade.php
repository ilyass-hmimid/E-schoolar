@extends('layouts.admin')

@section('title', 'Confirmer la suppression - Absence #' . $absence->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-red-50 border-b border-red-200">
            <h3 class="text-lg leading-6 font-medium text-red-800">
                <i class="fas fa-exclamation-triangle mr-2"></i> Confirmer la suppression
            </h3>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    Vous êtes sur le point de supprimer définitivement l'absence suivante :
                </p>
                
                <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Étudiant</p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $absence->eleve->nom_complet }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Cours</p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $absence->cours->matiere->nom }} - {{ $absence->cours->professeur->nom_complet }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date</p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $absence->date_absence->format('d/m/Y') }} 
                                ({{ $absence->heure_debut->format('H:i') }} - {{ $absence->heure_fin->format('H:i') }})
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Statut</p>
                            @php
                                $statusClasses = [
                                    'non_justifiee' => 'bg-red-100 text-red-800',
                                    'en_attente' => 'bg-yellow-100 text-yellow-800',
                                    'justifiee' => 'bg-green-100 text-green-800',
                                ][$absence->statut];
                                
                                $statusLabels = [
                                    'non_justifiee' => 'Non justifiée',
                                    'en_attente' => 'En attente',
                                    'justifiee' => 'Justifiée',
                                ];
                            @endphp
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                {{ $statusLabels[$absence->statut] }}
                            </span>
                        </div>
                    </div>
                    
                    @if($absence->commentaire)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-500">Commentaire</p>
                            <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $absence->commentaire }}</p>
                        </div>
                    @endif
                </div>
                
                <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Attention : Cette action est irréversible
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>
                                    La suppression de cette absence est définitive. Toutes les données associées seront perdues.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 bg-gray-50 p-4 rounded-md">
                    <p class="text-sm text-gray-700 mb-3">
                        <strong>Considérez plutôt :</strong>
                    </p>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-gray-600">
                        <li>Marquer cette absence comme justifiée au lieu de la supprimer</li>
                        <li>Ajouter un commentaire pour expliquer le contexte</li>
                        <li>Modifier les détails de l'absence si nécessaire</li>
                    </ul>
                </div>
                
                <div class="mt-6 flex items-center">
                    <input id="confirm-delete" name="confirm-delete" type="checkbox" 
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                           required>
                    <label for="confirm-delete" class="ml-2 block text-sm text-gray-900">
                        Je confirme vouloir supprimer définitivement cette absence
                    </label>
                </div>
            </div>
            
            <div class="mt-8 pt-5 border-t border-gray-200">
                <div class="flex justify-between">
                    <a href="{{ route('admin.absences.show', $absence) }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Annuler et revenir aux détails
                    </a>
                    <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" class="inline-block" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i> Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('delete-form');
        const checkbox = document.getElementById('confirm-delete');
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Désactiver le bouton de soumission initialement
        submitButton.disabled = true;
        
        // Activer/désactiver le bouton en fonction de la case à cocher
        checkbox.addEventListener('change', function() {
            submitButton.disabled = !this.checked;
        });
        
        // Empêcher la soumission du formulaire si la case n'est pas cochée
        form.addEventListener('submit', function(e) {
            if (!checkbox.checked) {
                e.preventDefault();
                alert('Veuillez cocher la case de confirmation pour supprimer cette absence.');
            }
        });
    });
</script>
@endpush
@endsection
