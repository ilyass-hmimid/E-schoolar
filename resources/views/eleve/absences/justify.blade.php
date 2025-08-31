@extends('layouts.eleve')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('eleve.absences.show', $absence) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour aux détails de l'absence
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Justifier une absence
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ $absence->matiere->nom ?? 'Matière non spécifiée' }} - 
                {{ $absence->date_absence->format('d/m/Y') }}
                @if($absence->heure_debut)
                    - {{ substr($absence->heure_debut, 0, 5) }}
                    @if($absence->heure_fin)
                        à {{ substr($absence->heure_fin, 0, 5 }}
                    @endif
                @endif
            </p>
        </div>

        <form action="{{ route('eleve.absences.justify.store', $absence) }}" method="POST" enctype="multipart/form-data" class="px-6 py-5">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Message d'information -->
                @if(session('success'))
                    <div class="rounded-md bg-green-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Il y a {{ $errors->count() }} erreur(s) dans le formulaire
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Champ de justification -->
                <div>
                    <label for="justification" class="block text-sm font-medium text-gray-700">
                        Justification <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <textarea id="justification" name="justification" rows="4" 
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                            placeholder="Veuillez fournir une justification pour cette absence..."
                            required>{{ old('justification', $absence->justification) }}</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Décrivez brièvement la raison de votre absence.
                    </p>
                </div>

                <!-- Pièce jointe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Pièce jointe (optionnel)
                    </label>
                    
                    @if($absence->piece_jointe)
                        <div class="mt-2 flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-2 text-sm text-gray-500 truncate">
                                {{ basename($absence->piece_jointe) }}
                            </span>
                            <a href="{{ route('eleve.absences.justification.download', $absence) }}" 
                               class="ml-4 text-sm text-indigo-600 hover:text-indigo-500">
                                Télécharger
                            </a>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Un nouveau fichier remplacera le fichier existant.
                        </p>
                    @endif
                    
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="piece_jointe" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Téléverser un fichier</span>
                                    <input id="piece_jointe" name="piece_jointe" type="file" class="sr-only">
                                </label>
                                <p class="pl-1">ou glissez-déposez</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PDF, JPG, PNG, DOC, DOCX jusqu'à {{ $maxFileSize }}MB
                            </p>
                        </div>
                    </div>
                    
                    @if($errors->has('piece_jointe'))
                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('piece_jointe') }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="flex justify-end">
                    <a href="{{ route('eleve.absences.show', $absence) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Annuler
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Enregistrer la justification
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Afficher le nom du fichier sélectionné
    document.getElementById('piece_jointe').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Aucun fichier sélectionné';
        const fileInfo = document.createElement('div');
        fileInfo.className = 'mt-2 text-sm text-gray-500';
        fileInfo.textContent = `Fichier sélectionné : ${fileName}`;
        
        // Supprimer l'ancien message s'il existe
        const oldFileInfo = document.querySelector('.file-info');
        if (oldFileInfo) {
            oldFileInfo.remove();
        }
        
        fileInfo.classList.add('file-info');
        e.target.parentNode.parentNode.appendChild(fileInfo);
    });
</script>
@endpush
@endsection
