@csrf

<div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
    <!-- Étudiant -->
    <div class="sm:col-span-3">
        <label for="eleve_id" class="block text-sm font-medium text-gray-700">Étudiant <span class="text-red-500">*</span></label>
        <select id="eleve_id" name="eleve_id" required
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
            <option value="">Sélectionner un étudiant</option>
            @foreach($eleves as $eleve)
                <option value="{{ $eleve->id }}" {{ (old('eleve_id', $absence->eleve_id ?? '') == $eleve->id) ? 'selected' : '' }}>
                    {{ $eleve->nom }} {{ $eleve->prenom }}
                </option>
            @endforeach
        </select>
        @error('eleve_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Cours -->
    <div class="sm:col-span-3">
        <label for="cours_id" class="block text-sm font-medium text-gray-700">Cours <span class="text-red-500">*</span></label>
        <select id="cours_id" name="cours_id" required
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
            <option value="">Sélectionner un cours</option>
            @foreach($cours as $coursItem)
                <option value="{{ $coursItem->id }}" {{ (old('cours_id', $absence->cours_id ?? '') == $coursItem->id) ? 'selected' : '' }}>
                    {{ $coursItem->nom }} ({{ $coursItem->professeur->nom }})
                </option>
            @endforeach
        </select>
        @error('cours_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Date d'absence -->
    <div class="sm:col-span-3">
        <label for="date_absence" class="block text-sm font-medium text-gray-700">Date d'absence <span class="text-red-500">*</span></label>
        <input type="date" name="date_absence" id="date_absence" required
            value="{{ old('date_absence', $absence->date_absence ?? '') }}"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
        @error('date_absence')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Heure de début -->
    <div class="sm:col-span-3">
        <label for="heure_debut" class="block text-sm font-medium text-gray-700">Heure de début <span class="text-red-500">*</span></label>
        <input type="time" name="heure_debut" id="heure_debut" required
            value="{{ old('heure_debut', $absence->heure_debut ?? '') }}"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
        @error('heure_debut')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Heure de fin -->
    <div class="sm:col-span-3">
        <label for="heure_fin" class="block text-sm font-medium text-gray-700">Heure de fin <span class="text-red-500">*</span></label>
        <input type="time" name="heure_fin" id="heure_fin" required
            value="{{ old('heure_fin', $absence->heure_fin ?? '') }}"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
        @error('heure_fin')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Statut -->
    <div class="sm:col-span-3">
        <label for="statut" class="block text-sm font-medium text-gray-700">Statut <span class="text-red-500">*</span></label>
        <select id="statut" name="statut" required
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
            <option value="non_justifiee" {{ (old('statut', $absence->statut ?? '') == 'non_justifiee') ? 'selected' : '' }}>Non justifiée</option>
            <option value="en_attente" {{ (old('statut', $absence->statut ?? '') == 'en_attente') ? 'selected' : '' }}>En attente de validation</option>
            <option value="justifiee" {{ (old('statut', $absence->statut ?? '') == 'justifiee') ? 'selected' : '' }}>Justifiée</option>
        </select>
        @error('statut')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Justification -->
    <div class="sm:col-span-6">
        <div class="flex items-center">
            <input id="justifiee" name="justifiee" type="checkbox" value="1"
                {{ old('justifiee', $absence->justifiee ?? false) ? 'checked' : '' }}
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
            <label for="justifiee" class="ml-2 block text-sm text-gray-700">
                Cette absence est justifiée
            </label>
        </div>
        @error('justifiee')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Commentaire -->
    <div class="sm:col-span-6">
        <label for="commentaire" class="block text-sm font-medium text-gray-700">Commentaire</label>
        <div class="mt-1">
            <textarea id="commentaire" name="commentaire" rows="3"
                class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('commentaire', $absence->commentaire ?? '') }}</textarea>
        </div>
        <p class="mt-2 text-sm text-gray-500">Ajoutez des détails sur cette absence si nécessaire.</p>
        @error('commentaire')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
