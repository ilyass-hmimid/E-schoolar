<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier une note</h1>
            <p class="mt-2 text-gray-600">Modifiez les informations de la note</p>
          </div>
          <Link
            :href="route('professeur.notes.index')"
            class="text-gray-700 hover:text-gray-900 flex items-center"
          >
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à la liste
          </Link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white shadow overflow-hidden rounded-lg">
        <form @submit.prevent="submit">
          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6">
              <!-- Matière (lecture seule) -->
              <div>
                <label class="block text-sm font-medium text-gray-700">Matière</label>
                <div class="mt-1 text-gray-900">
                  {{ note.matiere.nom }}
                </div>
              </div>

              <!-- Étudiant (lecture seule) -->
              <div>
                <label class="block text-sm font-medium text-gray-700">Étudiant</label>
                <div class="mt-1 text-gray-900">
                  {{ note.etudiant.nom }} {{ note.etudiant.prenom }}
                </div>
              </div>

              <!-- Type de note -->
              <div>
                <label for="type_note" class="block text-sm font-medium text-gray-700">Type de note *</label>
                <select
                  id="type_note"
                  v-model="form.type_note"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.type_note }"
                >
                  <option value="devoir">Devoir</option>
                  <option value="composition">Composition</option>
                  <option value="examen">Examen</option>
                  <option value="participation">Participation</option>
                </select>
                <p v-if="form.errors.type_note" class="mt-1 text-sm text-red-600">
                  {{ form.errors.type_note }}
                </p>
              </div>

              <!-- Note et coefficient -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="valeur" class="block text-sm font-medium text-gray-700">Note /20 *</label>
                  <input
                    type="number"
                    id="valeur"
                    v-model="form.valeur"
                    step="0.01"
                    min="0"
                    max="20"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': form.errors.valeur }"
                  />
                  <p v-if="form.errors.valeur" class="mt-1 text-sm text-red-600">
                    {{ form.errors.valeur }}
                  </p>
                </div>

                <div>
                  <label for="coefficient" class="block text-sm font-medium text-gray-700">Coefficient *</label>
                  <input
                    type="number"
                    id="coefficient"
                    v-model="form.coefficient"
                    step="0.1"
                    min="0.1"
                    max="5"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': form.errors.coefficient }"
                  />
                  <p v-if="form.errors.coefficient" class="mt-1 text-sm text-red-600">
                    {{ form.errors.coefficient }}
                  </p>
                </div>
              </div>

              <!-- Date d'évaluation -->
              <div>
                <label for="date_evaluation" class="block text-sm font-medium text-gray-700">Date d'évaluation *</label>
                <input
                  type="date"
                  id="date_evaluation"
                  v-model="form.date_evaluation"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.date_evaluation }"
                />
                <p v-if="form.errors.date_evaluation" class="mt-1 text-sm text-red-600">
                  {{ form.errors.date_evaluation }}
                </p>
              </div>

              <!-- Commentaire -->
              <div>
                <label for="commentaire" class="block text-sm font-medium text-gray-700">Commentaire</label>
                <textarea
                  id="commentaire"
                  v-model="form.commentaire"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.commentaire }"
                ></textarea>
                <p v-if="form.errors.commentaire" class="mt-1 text-sm text-red-600">
                  {{ form.errors.commentaire }}
                </p>
              </div>
            </div>
          </div>

          <!-- Pied de page du formulaire -->
          <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <Link
              :href="route('professeur.notes.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3"
            >
              Annuler
            </Link>
            <button
              type="submit"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Mise à jour...' : 'Mettre à jour la note' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  note: {
    type: Object,
    required: true
  },
  matieres: {
    type: Array,
    default: () => []
  }
});

// Initialisation du formulaire avec les données de la note
const form = useForm({
  type_note: props.note.type_note,
  valeur: props.note.valeur,
  coefficient: props.note.coefficient,
  date_evaluation: props.note.date_evaluation.split('T')[0], // Format YYYY-MM-DD
  commentaire: props.note.commentaire || ''
});

// Soumission du formulaire
const submit = () => {
  form.put(route('professeur.notes.update', props.note.id));
};
</script>
