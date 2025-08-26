<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des notes</h1>
            <p class="mt-2 text-gray-600">Liste des notes des élèves</p>
          </div>
          <Link
            :href="route('professeur.notes.create')"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Ajouter une note
          </Link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filtres -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Recherche -->
          <div class="col-span-1 md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
            <input
              type="text"
              id="search"
              v-model="filters.search"
              placeholder="Étudiant, matière..."
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              @keyup.enter="applyFilters"
            />
          </div>
          
          <!-- Filtre par matière -->
          <div>
            <label for="matiere" class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
            <select
              id="matiere"
              v-model="filters.matiere"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              @change="applyFilters"
            >
              <option value="">Toutes les matières</option>
              <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                {{ matiere.nom }}
              </option>
            </select>
          </div>
          
          <!-- Bouton de réinitialisation -->
          <div class="flex items-end">
            <button
              @click="resetFilters"
              class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors"
            >
              Réinitialiser
            </button>
          </div>
        </div>
      </div>

      <!-- Liste des notes -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- En-tête du tableau -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Étudiant
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Matière
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Note
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="notes.data.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  Aucune note trouvée.
                </td>
              </tr>
              <tr v-for="note in notes.data" :key="note.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                      <span class="text-blue-600 font-medium">
                        {{ note.etudiant.nom.charAt(0) }}{{ note.etudiant.prenom.charAt(0) }}
                      </span>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ note.etudiant.nom }} {{ note.etudiant.prenom }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ note.matiere.nom }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                    :class="{
                      'bg-green-100 text-green-800': note.type_note === 'examen',
                      'bg-blue-100 text-blue-800': note.type_note === 'devoir',
                      'bg-yellow-100 text-yellow-800': note.type_note === 'composition',
                      'bg-purple-100 text-purple-800': note.type_note === 'participation'
                    }">
                    {{ getTypeNoteLabel(note.type_note) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ note.valeur }}/20
                    <span class="text-xs text-gray-500">(coeff. {{ note.coefficient }})</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(note.date_evaluation) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link :href="route('professeur.notes.edit', note.id)" class="text-blue-600 hover:text-blue-900 mr-4">
                    Modifier
                  </Link>
                  <button @click="confirmDelete(note)" class="text-red-600 hover:text-red-900">
                    Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Affichage de
                <span class="font-medium">{{ notes.from || 0 }}</span>
                à
                <span class="font-medium">{{ notes.to || 0 }}</span>
                sur
                <span class="font-medium">{{ notes.total }}</span>
                résultats
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <Link
                  v-for="(link, index) in notes.links"
                  :key="index"
                  :href="link.url || '#'"
                  v-html="link.label"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    link.active
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                      : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                    !link.url ? 'opacity-50 cursor-not-allowed' : '',
                    index === 0 ? 'rounded-l-md' : '',
                    index === notes.links.length - 1 ? 'rounded-r-md' : ''
                  ]"
                />
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <ConfirmationModal
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="deleteNote"
    >
      <template #title>
        Supprimer la note
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.
      </template>
    </ConfirmationModal>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  notes: Object,
  filters: {
    type: Object,
    default: () => ({
      search: '',
      matiere: '',
      classe: '',
      date_debut: '',
      date_fin: ''
    })
  },
  matieres: {
    type: Array,
    default: () => []
  }
});

const showDeleteModal = ref(false);
const noteToDelete = ref(null);

const filters = ref({
  search: props.filters.search || '',
  matiere: props.filters.matiere || '',
  classe: props.filters.classe || '',
  date_debut: props.filters.date_debut || '',
  date_fin: props.filters.date_fin || ''
});

// Fonction pour formater la date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

// Fonction pour obtenir le libellé du type de note
const getTypeNoteLabel = (type) => {
  const types = {
    'devoir': 'Devoir',
    'composition': 'Composition',
    'examen': 'Examen',
    'participation': 'Participation'
  };
  return types[type] || type;
};

// Fonction pour appliquer les filtres avec debounce
const applyFilters = debounce(() => {
  router.get(route('professeur.notes.index'), filters.value, {
    preserveState: true,
    replace: true
  });
}, 300);

// Fonction pour réinitialiser les filtres
const resetFilters = () => {
  filters.value = {
    search: '',
    matiere: '',
    classe: '',
    date_debut: '',
    date_fin: ''
  };
  applyFilters();
};

// Confirmer la suppression d'une note
const confirmDelete = (note) => {
  noteToDelete.value = note.id;
  showDeleteModal.value = true;
};

// Supprimer une note
const deleteNote = () => {
  if (noteToDelete.value) {
    router.delete(route('professeur.notes.destroy', noteToDelete.value), {
      onSuccess: () => {
        showDeleteModal.value = false;
        noteToDelete.value = null;
      }
    });
  }
};

// Observer les changements de filtres
watch(filters, () => {
  applyFilters();
}, { deep: true });
</script>
