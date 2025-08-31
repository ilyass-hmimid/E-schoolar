@extends('layouts.admin')

@section('title', 'Ajouter une absence')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ajouter une absence</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                        <i class="fas fa-home mr-2"></i>
                        Tableau de bord
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('admin.absences.index') }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                            Absences
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            Nouvelle absence
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Informations sur l'absence
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Remplissez les champs ci-dessous pour enregistrer une nouvelle absence.
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.absences.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Élève -->
                    <div class="sm:col-span-3">
                        <label for="eleve_id" class="block text-sm font-medium text-gray-700">
                            Élève <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select id="eleve_id" name="eleve_id" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="">Sélectionnez un élève</option>
                                @foreach($eleves as $eleve)
                                    <option value="{{ $eleve->id }}" {{ old('eleve_id') == $eleve->id ? 'selected' : '' }}>
                                        {{ $eleve->nom_complet }} ({{ $eleve->classe->nom ?? 'Sans classe' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('eleve_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cours -->
                    <div class="sm:col-span-3">
                        <label for="cours_id" class="block text-sm font-medium text-gray-700">
                            Cours <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select id="cours_id" name="cours_id" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="">Sélectionnez un cours</option>
                                @foreach($cours as $cour)
                                    <option value="{{ $cour->id }}" {{ old('cours_id') == $cour->id ? 'selected' : '' }}>
                                        {{ $cour->matiere->nom }} - {{ $cour->professeur->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('cours_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date d'absence -->
                    <div class="sm:col-span-3">
                        <label for="date_absence" class="block text-sm font-medium text-gray-700">
                            Date d'absence <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="date" name="date_absence" id="date_absence" required
                                value="{{ old('date_absence', now()->format('Y-m-d')) }}"
                                class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('date_absence')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Justificatif -->
                    <div class="sm:col-span-3">
                        <label for="justificatif" class="block text-sm font-medium text-gray-700">
                            Justificatif
                        </label>
                        <div class="mt-1">
                            <textarea id="justificatif" name="justificatif" rows="3"
                                class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('justificatif') }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Détails sur le justificatif (facultatif).
                        </p>
                        @error('justificatif')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut de justification -->
                    <div class="sm:col-span-6">
                        <div class="flex items-center">
                            <input id="justifiee" name="justifiee" type="checkbox" value="1"
                                {{ old('justifiee') ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="justifiee" class="ml-2 block text-sm text-gray-700">
                                Marquer comme justifiée
                            </label>
                        </div>
                        @error('justifiee')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-5">
                    <div class="flex justify-end">
                        <a href="{{ route('admin.absences.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Annuler
                        </a>
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Enregistrer l'absence
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script pour gérer les interactions dynamiques
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des sélecteurs avec Select2 si disponible
        if ($ && $.fn.select2) {
            $('#eleve_id, #cours_id').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }
    });
</script>
@endpush
@endsection
