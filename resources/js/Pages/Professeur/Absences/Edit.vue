<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier une absence</h1>
            <p class="mt-2 text-gray-600">Modifier les détails de l'absence</p>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="submit">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6 space-y-6">
            <!-- Messages d'erreur -->
            <div v-if="form.errors.any()" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-red-700">
                    Impossible de mettre à jour l'absence. Veuillez corriger les erreurs ci-dessous.
                  </p>
                </div>
              </div>
            </div>

            <!-- Informations sur l'étudiant (lecture seule) -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Étudiant</label>
              <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded border border-gray-200">
                {{ absence.etudiant.prenom }} {{ absence.etudiant.nom }}
              </div>
            </div>

            <!-- Sélection de la matière -->
            <div>
              <label for="matiere_id" class="block text-sm font-medium text-gray-700">Matière <span class="text-red-500">*</span></label>
              <select
                id="matiere_id"
                v-model="form.matiere_id"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                :class="{ 'border-red-300 text-red-900': form.errors.matiere_id }"
                :disabled="form.processing"
              >
                <option value="" disabled>Sélectionnez une matière</option>
                <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                  {{ matiere.nom }}
                </option>
              </select>
              <p v-if="form.errors.matiere_id" class="mt-1 text-sm text-red-600">
                {{ form.errors.matiere_id }}
              </p>
            </div>

            <!-- Date de l'absence -->
            <div>
              <label for="date_absence" class="block text-sm font-medium text-gray-700">Date de l'absence <span class="text-red-500">*</span></label>
              <input
                type="date"
                id="date_absence"
                v-model="form.date_absence"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                :class="{ 'border-red-300': form.errors.date_absence }"
                :disabled="form.processing"
              />
              <p v-if="form.errors.date_absence" class="mt-1 text-sm text-red-600">
                {{ form.errors.date_absence }}
              </p>
            </div>

            <!-- Période -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="heure_debut" class="block text-sm font-medium text-gray-700">Heure de début <span class="text-red-500">*</span></label>
                <input
                  type="time"
                  id="heure_debut"
                  v-model="form.heure_debut"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  :class="{ 'border-red-300': form.errors.heure_debut }"
                  :disabled="form.processing"
                />
                <p v-if="form.errors.heure_debut" class="mt-1 text-sm text-red-600">
                  {{ form.errors.heure_debut }}
                </p>
              </div>
              <div>
                <label for="heure_fin" class="block text-sm font-medium text-gray-700">Heure de fin <span class="text-red-500">*</span></label>
                <input
                  type="time"
                  id="heure_fin"
                  v-model="form.heure_fin"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  :class="{ 'border-red-300': form.errors.heure_fin }"
                  :disabled="form.processing"
                />
                <p v-if="form.errors.heure_fin" class="mt-1 text-sm text-red-600">
                  {{ form.errors.heure_fin }}
                </p>
              </div>
            </div>

            <!-- Type d'absence -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
              <div class="space-y-2">
                <div class="flex items-center">
                  <input
                    id="type_absence"
                    v-model="form.type"
                    type="radio"
                    value="absence"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                    :disabled="form.processing"
                  />
                  <label for="type_absence" class="ml-2 block text-sm text-gray-700">
                    Absence
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    id="type_retard"
                    v-model="form.type"
                    type="radio"
                    value="retard"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                    :disabled="form.processing"
                  />
                  <label for="type_retard" class="ml-2 block text-sm text-gray-700">
                    Retard
                  </label>
                </div>
              </div>
              <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                {{ form.errors.type }}
              </p>
            </div>

            <!-- Durée du retard (conditionnel) -->
            <div v-if="form.type === 'retard'">
              <label for="duree_retard" class="block text-sm font-medium text-gray-700">Durée du retard (en minutes) <span class="text-red-500">*</span></label>
              <input
                type="number"
                id="duree_retard"
                v-model="form.duree_retard"
                min="1"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                :class="{ 'border-red-300': form.errors.duree_retard }"
                :disabled="form.processing"
              />
              <p v-if="form.errors.duree_retard" class="mt-1 text-sm text-red-600">
                {{ form.errors.duree_retard }}
              </p>
            </div>

            <!-- Motif -->
            <div>
              <label for="motif" class="block text-sm font-medium text-gray-700">Motif <span class="text-red-500">*</span></label>
              <textarea
                id="motif"
                v-model="form.motif"
                rows="3"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                :class="{ 'border-red-300': form.errors.motif }"
                placeholder="Raison de l'absence/retard..."
                :disabled="form.processing"
              ></textarea>
              <p v-if="form.errors.motif" class="mt-1 text-sm text-red-600">
                {{ form.errors.motif }}
              </p>
            </div>

            <!-- Justification -->
            <div>
              <div class="flex items-center">
                <input
                  id="justifiee"
                  v-model="form.justifiee"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  :disabled="form.processing"
                />
                <label for="justifiee" class="ml-2 block text-sm text-gray-700">
                  Cette absence est justifiée
                </label>
              </div>
              <p v-if="form.errors.justifiee" class="mt-1 text-sm text-red-600">
                {{ form.errors.justifiee }}
              </p>
            </div>

            <!-- Détails de la justification (conditionnel) -->
            <div v-if="form.justifiee">
              <label for="justification" class="block text-sm font-medium text-gray-700">Détails de la justification <span class="text-red-500">*</span></label>
              <textarea
                id="justification"
                v-model="form.justification"
                rows="2"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                :class="{ 'border-red-300': form.errors.justification }"
                placeholder="Précisez les détails de la justification..."
                :disabled="form.processing"
              ></textarea>
              <p v-if="form.errors.justification" class="mt-1 text-sm text-red-600">
                {{ form.errors.justification }}
              </p>
            </div>
          </div>
          
          <!-- Pied de page avec boutons -->
          <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <Link
              :href="route('professeur.absences.index')"
              class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              :disabled="form.processing"
            >
              Annuler
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
  absence: {
    type: Object,
    required: true,
  },
  matieres: {
    type: Array,
    required: true,
  },
});

// Initialiser le formulaire avec les données de l'absence
const form = useForm({
  matiere_id: props.absence.matiere_id,
  date_absence: props.absence.date_absence.split(' ')[0], // Garder uniquement la date
  heure_debut: props.absence.heure_debut,
  heure_fin: props.absence.heure_fin,
  type: props.absence.type,
  duree_retard: props.absence.duree_retard || 15,
  motif: props.absence.motif,
  justifiee: props.absence.justifiee,
  justification: props.absence.justification || '',
  _method: 'PUT', // Pour la méthode HTTP PUT via POST
});

// Fonction de soumission du formulaire
const submit = () => {
  form.post(route('professeur.absences.update', props.absence.id), {
    onSuccess: () => {
      // Redirection gérée par le contrôleur
    },
    preserveScroll: true,
  });
};

// Réinitialiser la durée du retard si le type change d'absence à retard
watch(() => form.type, (newType) => {
  if (newType === 'retard' && !form.duree_retard) {
    form.duree_retard = 15;
  }
});
</script>
