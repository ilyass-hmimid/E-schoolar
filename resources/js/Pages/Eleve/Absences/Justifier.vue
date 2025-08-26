<template>
  <div class="space-y-6">
    <!-- En-tête avec bouton de retour -->
    <div>
      <Link 
        :href="absence ? route('eleve.absences.show', absence.id) : route('eleve.absences.index')" 
        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
      >
        <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        {{ absence ? 'Retour aux détails' : 'Retour à la liste des absences' }}
      </Link>
      <h1 class="mt-2 text-2xl font-bold text-gray-900">
        {{ absence ? 'Justifier une absence' : 'Déclarer une absence' }}
      </h1>
    </div>

    <!-- Formulaire de justification -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <form @submit.prevent="submit">
        <div class="px-4 py-5 sm:p-6">
          <div class="space-y-6">
            <!-- Informations sur l'absence -->
            <div v-if="absence" class="bg-blue-50 p-4 rounded-md">
              <h3 class="text-sm font-medium text-blue-800">Absence à justifier</h3>
              <div class="mt-2 text-sm text-blue-700">
                <p>Matière : {{ absence.enseignement.matiere.nom }}</p>
                <p>Date : {{ formatDate(absence.date_absence) }}</p>
                <p>Séance : {{ formatHeure(absence.enseignement.date_debut) }} - {{ formatHeure(absence.enseignement.date_fin) }}</p>
              </div>
            </div>

            <!-- Sélection de la date pour une nouvelle absence -->
            <div v-else class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
              <div class="sm:col-span-3">
                <label for="date_absence" class="block text-sm font-medium text-gray-700">
                  Date de l'absence <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                  <input
                    type="date"
                    id="date_absence"
                    v-model="form.date_absence"
                    :max="new Date().toISOString().split('T')[0]"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    :class="{ 'border-red-300': form.errors.date_absence }"
                  />
                  <p v-if="form.errors.date_absence" class="mt-1 text-sm text-red-600">
                    {{ form.errors.date_absence }}
                  </p>
                </div>
              </div>

              <div class="sm:col-span-3">
                <label for="enseignement_id" class="block text-sm font-medium text-gray-700">
                  Séance manquée <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                  <select
                    id="enseignement_id"
                    v-model="form.enseignement_id"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    :class="{ 'border-red-300': form.errors.enseignement_id }"
                  >
                    <option value="">Sélectionnez une séance</option>
                    <option 
                      v-for="seance in seancesDisponibles" 
                      :key="seance.id" 
                      :value="seance.id"
                    >
                      {{ formatDate(seance.date_debut) }} - {{ seance.matiere.nom }} ({{ formatHeure(seance.date_debut) }} - {{ formatHeure(seance.date_fin) }})
                    </option>
                  </select>
                  <p v-if="form.errors.enseignement_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.enseignement_id }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Type de justification -->
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
              <div class="sm:col-span-6">
                <label class="block text-sm font-medium text-gray-700">
                  Type de justification <span class="text-red-500">*</span>
                </label>
                <fieldset class="mt-2">
                  <div class="space-y-4">
                    <div v-for="type in typesJustification" :key="type.value" class="flex items-center">
                      <input
                        :id="`type_${type.value}`"
                        v-model="form.type_justificatif"
                        :value="type.value"
                        type="radio"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      />
                      <label :for="`type_${type.value}`" class="ml-3">
                        <span class="block text-sm text-gray-700">{{ type.label }}</span>
                        <span class="block text-xs text-gray-500">{{ type.description }}</span>
                      </label>
                    </div>
                  </div>
                  <p v-if="form.errors.type_justificatif" class="mt-1 text-sm text-red-600">
                    {{ form.errors.type_justificatif }}
                  </p>
                </fieldset>
              </div>
            </div>

            <!-- Détails de la justification -->
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
              <div class="sm:col-span-6">
                <label for="commentaire" class="block text-sm font-medium text-gray-700">
                  Commentaire (optionnel)
                </label>
                <div class="mt-1">
                  <textarea
                    id="commentaire"
                    v-model="form.commentaire"
                    rows="3"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                    placeholder="Décrivez brièvement la raison de votre absence..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Téléchargement du justificatif -->
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
              <div class="sm:col-span-6">
                <label class="block text-sm font-medium text-gray-700">
                  Justificatif (optionnel)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                  <div class="space-y-1 text-center">
                    <svg
                      class="mx-auto h-12 w-12 text-gray-400"
                      stroke="currentColor"
                      fill="none"
                      viewBox="0 0 48 48"
                      aria-hidden="true"
                    >
                      <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                      <label
                        for="justificatif"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                      >
                        <span>Télécharger un fichier</span>
                        <input
                          id="justificatif"
                          type="file"
                          class="sr-only"
                          @change="handleFileChange"
                        />
                      </label>
                      <p class="pl-1">ou glisser-déposer</p>
                    </div>
                    <p class="text-xs text-gray-500">
                      PDF, JPG, PNG jusqu'à 5 Mo
                    </p>
                  </div>
                </div>
                <p v-if="form.errors.justificatif" class="mt-1 text-sm text-red-600">
                  {{ form.errors.justificatif }}
                </p>
                <p v-if="form.justificatif" class="mt-2 text-sm text-gray-500">
                  Fichier sélectionné : {{ form.justificatif.name }}
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pied de page avec boutons -->
        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
          <Link
            :href="absence ? route('eleve.absences.show', absence.id) : route('eleve.absences.index')"
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Annuler
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const props = defineProps({
  absence: {
    type: Object,
    default: null
  },
  seances: {
    type: Array,
    default: () => []
  },
  typesJustification: {
    type: Array,
    default: () => [
      { value: 'maladie', label: 'Maladie', description: 'Avec ou sans certificat médical' },
      { value: 'famille', label: 'Raison familiale', description: 'Événement familial important' },
      { value: 'administratif', label: 'Démarche administrative', description: 'Rendez-vous officiel' },
      { value: 'autre', label: 'Autre raison', description: 'Précisez dans les commentaires' }
    ]
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

// Initialisation du formulaire
const form = useForm({
  _method: props.absence ? 'PUT' : 'POST',
  enseignement_id: props.absence?.enseignement_id || '',
  date_absence: props.absence?.date_absence || '',
  type_justificatif: '',
  commentaire: props.absence?.commentaire || '',
  justificatif: null
});

// Filtrage des séances disponibles pour la date sélectionnée
const seancesDisponibles = computed(() => {
  if (!form.date_absence) return [];
  
  return props.seances.filter(seance => {
    const seanceDate = new Date(seance.date_debut).toISOString().split('T')[0];
    return seanceDate === form.date_absence;
  });
});

// Gestion du téléchargement de fichier
const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) { // 5MB
      alert('Le fichier est trop volumineux. Taille maximale : 5 Mo.');
      return;
    }
    
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
      alert('Type de fichier non pris en charge. Formats acceptés : PDF, JPG, PNG.');
      return;
    }
    
    form.justificatif = file;
  }
};

// Soumission du formulaire
const submit = () => {
  const url = props.absence 
    ? route('eleve.absences.update', props.absence.id)
    : route('eleve.absences.store');
  
  form.post(url, {
    forceFormData: true,
    onSuccess: () => {
      // Redirection après succès
    },
    onError: (errors) => {
      // Gestion des erreurs
    }
  });
};

// Formater une date en français
const formatDate = (dateString) => {
  if (!dateString) return '';
  return format(new Date(dateString), 'PPP', { locale: fr });
};

// Formater une heure
const formatHeure = (dateTimeString) => {
  if (!dateTimeString) return '';
  return format(new Date(dateTimeString), 'HH:mm');
};
</script>
