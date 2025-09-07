@extends('admin.layout')

@section('title', 'Ajouter une matière - ' . $etudiant->name)

@section('header')
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
            <a href="{{ route('admin.etudiants.index') }}" class="text-indigo-600 hover:text-indigo-900">Étudiants</a>
            <span class="text-gray-400 mx-2">/</span>
            <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="text-indigo-600 hover:text-indigo-900">{{ $etudiant->name }}</a>
            <span class="text-gray-400 mx-2">/</span>
            <a href="{{ route('admin.etudiants.matieres', $etudiant) }}" class="text-indigo-600 hover:text-indigo-900">Matières</a>
            <span class="text-gray-400 mx-2">/</span>
            Ajouter une matière
        </h1>
    </div>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('admin.etudiants.matieres.ajouter', $etudiant) }}" method="POST">
            @csrf
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div class="mb-6">
                    <p class="text-sm text-gray-500">
                        Sélectionnez une ou plusieurs matières à ajouter à l'étudiant <span class="font-medium">{{ $etudiant->name }}</span>.
                    </p>
                </div>

                @if($matieres->count() > 0)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2">
                            @foreach($matieres as $matiere)
                                <div class="relative flex items-start py-2">
                                    <div class="flex items-center h-5">
                                        <input id="matiere-{{ $matiere->id }}" name="matieres[]" type="checkbox" 
                                               value="{{ $matiere->id }}" 
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded matiere-checkbox">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="matiere-{{ $matiere->id }}" class="font-medium text-gray-700">
                                            {{ $matiere->nom }}
                                        </label>
                                        <div class="text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                  style="background-color: {{ $matiere->couleur }}20; color: {{ $matiere->couleur }}">
                                                {{ number_format($matiere->prix_mensuel, 2, ',', ' ') }} DH/mois
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ajouter les matières sélectionnées
                        </button>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Toutes les matières sont déjà attribuées</h3>
                        <p class="mt-1 text-sm text-gray-500">Aucune matière supplémentaire à ajouter à cet étudiant.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour aux détails de l'étudiant
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Limiter la sélection à 5 matières maximum
        const checkboxes = document.querySelectorAll('.matiere-checkbox');
        const maxSelection = 5;
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.matiere-checkbox:checked').length;
                
                if (checkedCount >= maxSelection) {
                    checkboxes.forEach(cb => {
                        if (!cb.checked) {
                            cb.disabled = true;
                        }
                    });
                } else {
                    checkboxes.forEach(cb => {
                        cb.disabled = false;
                    });
                }
            });
        });
    });
</script>
@endpush
