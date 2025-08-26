<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Ajouter une note</h1>
            <p class="mt-2 text-gray-600">Saisissez les informations de la nouvelle note</p>
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
              <!-- Sélection de la matière -->
              <div>
                <label for="matiere_id" class="block text-sm font-medium text-gray-700">Matière *</label>
                <select
                  id="matiere_id"
                  v-model="form.matiere_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.matiere_id }"
                  @change="loadEtudiants"
                >
                  <option value="">Sélectionnez une matière</option>
                  <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                    {{ matiere.nom }}
                  </option>
                </select>
                <p v-if="form.errors.matiere_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.matiere_id }}
                </p>
              </div>

              <!-- Sélection de la classe -->
              <div v-if="matieres.length > 0">
                <label for="classe_id" class="block text-sm font-medium text-gray-700">Classe *</label>
                <select
                  id="classe_id"
                  v-model="form.classe_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.classe_id }"
                  @change="loadEtudiants"
                >
                  <option value="">Sélectionnez une classe</option>
                  <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                    {{ classe.nom }}
                  </option>
                </select>
                <p v-if="form.errors.classe_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.classe_id }}
                </p>
              </div>

              <!-- Sélection de l'étudiant -->
              <div v-if="form.matiere_id && form.classe_id">
                <label for="etudiant_id" class="block text-sm font-medium text-gray-700">Étudiant *</label>
                <select
                  id="etudiant_id"
                  v-model="form.etudiant_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.etudiant_id }"
                >
                  <option value="">Sélectionnez un étudiant</option>
                  <option v-for="etudiant in etudiants" :key="etudiant.id" :value="etudiant.id">
                    {{ etudiant.nom }} {{ etudiant.prenom }}
                  </option>
                </select>
                <p v-if="form.errors.etudiant_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.etudiant_id }}
                </p>
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

              <!-- Note -->
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
              {{ form.processing ? 'Enregistrement...' : 'Enregistrer la note' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
  matieres: {
    type: Array,
    default: () => []
  },
  classes: {
    type: Array,
    default: () => []
  }
});

// État du formulaire
const form = useForm({
  matiere_id: '',
  classe_id: '',
  etudiant_id: '',
  type_note: 'devoir',
  valeur: '',
  coefficient: 1,
  date_evaluation: new Date().toISOString().split('T')[0],
  commentaire: ''
});

// Liste des étudiants chargés dynamiquement
const etudiants = ref([]);

// Charger les étudiants en fonction de la matière et de la classe sélectionnées
const loadEtudiants = async () => {
  if (!form.matiere_id || !form.classe_id) {
    etudiants.value = [];
    form.etudiant_id = '';
    return;
  }

  try {
    const response = await axios.get(route('professeur.api.etudiants.by-classe-matiere', {
      classe: form.classe_id,
      matiere: form.matiere_id
    }));
    
    etudiants.value = response.data;
  } catch (error) {
    console.error('Erreur lors du chargement des étudiants:', error);
    etudiants.value = [];
  }
};

// Soumission du formulaire
const submit = () => {
  form.post(route('professeur.notes.store'));
};
</script>
