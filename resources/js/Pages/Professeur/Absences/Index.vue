<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des absences</h1>
            <p class="mt-2 text-gray-600">Liste des absences des élèves</p>
          </div>
          <Link
            :href="route('professeur.absences.create')"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Enregistrer une absence
          </Link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filtres -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Filtres</h2>
          <form @submit.prevent="filterAbsences" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Recherche par nom d'étudiant -->
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un étudiant</label>
                <input
                  type="text"
                  id="search"
                  v-model="filters.search"
                  placeholder="Nom ou prénom de l'étudiant"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
              
              <!-- Filtre par matière -->
              <div>
                <label for="matiere" class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
                <select
                  id="matiere"
                  v-model="filters.matiere"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option :value="null">Toutes les matières</option>
                  <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                    {{ matiere.nom }}
                  </option>
                </select>
              </div>
              
              <!-- Filtre par statut de justification -->
              <div>
                <label for="justifiee" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select
                  id="justifiee"
                  v-model="filters.justifiee"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option :value="null">Tous les statuts</option>
                  <option value="1">Justifiées</option>
                  <option value="0">Non justifiées</option>
                </select>
              </div>
              
              <!-- Bouton de réinitialisation -->
              <div class="flex items-end">
                <button
                  type="button"
                  @click="resetFilters"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Réinitialiser
                </button>
              </div>
            </div>
            
            <!-- Période -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                <input
                  type="date"
                  id="date_debut"
                  v-model="filters.date_debut"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
              <div>
                <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                <input
                  type="date"
                  id="date_fin"
                  v-model="filters.date_fin"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total des absences</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ absences.total }}</dd>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Absences justifiées</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-600">
              {{ absences.data.filter(a => a.justifiee).length }}
              <span class="text-sm text-gray-500">({{ Math.round((absences.data.filter(a => a.justifiee).length / (absences.total || 1)) * 100) }}%)</span>
            </dd>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Retards</dt>
            <dd class="mt-1 text-3xl font-semibold text-yellow-600">
              {{ absences.data.filter(a => a.type === 'retard').length }}
              <span class="text-sm text-gray-500">({{ Math.round((absences.data.filter(a => a.type === 'retard').length / (absences.total || 1)) * 100) }}%)</span>
            </dd>
          </div>
        </div>
      </div>

      <!-- Liste des absences -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
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
                  Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Période
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Statut
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="absences.data.length === 0">
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                  Aucune absence enregistrée.
                </td>
              </tr>
              <tr v-for="absence in absences.data" :key="absence.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ absence.etudiant.prenom }} {{ absence.etudiant.nom }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ absence.matiere.nom }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ formatDate(absence.date_absence) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ formatTime(absence.heure_debut) }} - {{ formatTime(absence.heure_fin) }}
                    <span v-if="absence.type === 'retard'" class="text-yellow-600 text-xs ml-2">
                      (Retard: {{ absence.duree_retard }} min)
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="{
                      'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                      'bg-red-100 text-red-800': absence.type === 'absence',
                      'bg-yellow-100 text-yellow-800': absence.type === 'retard'
                    }"
                  >
                    {{ absence.type === 'absence' ? 'Absence' : 'Retard' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="{
                      'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                      'bg-green-100 text-green-800': absence.justifiee,
                      'bg-red-100 text-red-800': !absence.justifiee
                    }"
                  >
                    {{ absence.justifiee ? 'Justifiée' : 'Non justifiée' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link 
                    :href="route('professeur.absences.edit', absence.id)" 
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    Modifier
                  </Link>
                  <button 
                    @click="confirmDelete(absence)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <Link
              :href="absences.prev_page_url"
              :class="{
                'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50': true,
                'opacity-50 cursor-not-allowed': !absences.prev_page_url
              }"
              :disabled="!absences.prev_page_url"
            >
              Précédent
            </Link>
            <Link
              :href="absences.next_page_url"
              :class="{
                'ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50': true,
                'opacity-50 cursor-not-allowed': !absences.next_page_url
              }"
              :disabled="!absences.next_page_url"
            >
              Suivant
            </Link>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Affichage de
                <span class="font-medium">{{ absences.from || 0 }}</span>
                à
                <span class="font-medium">{{ absences.to || 0 }}</span>
                sur
                <span class="font-medium">{{ absences.total }}</span>
                résultats
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <Link
                  v-for="(link, index) in absences.links"
                  :key="index"
                  :href="link.url || '#'"
                  :class="{
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium': true,
                    'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
                    'z-10 bg-blue-50 border-blue-500 text-blue-600': link.active,
                    'text-gray-300 cursor-not-allowed': !link.url,
                    'rounded-l-md': index === 0,
                    'rounded-r-md': index === absences.links.length - 1
                  }"
                  v-html="link.label"
                  :aria-current="link.active ? 'page' : undefined"
                ></Link>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Confirmer la suppression
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer cette absence ? Cette action est irréversible.
      </template>
      <template #footer>
        <button
          type="button"
          class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          @click="showDeleteModal = false"
        >
          Annuler
        </button>
        <button
          type="button"
          class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          @click="deleteAbsence"
        >
          Supprimer
        </button>
      </template>
    </ConfirmationModal>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { format, parseISO } from 'date-fns';
import { fr } from 'date-fns/locale';

const props = defineProps({
  absences: Object,
  filters: Object,
  matieres: Array,
});

const filters = useForm({
  search: props.filters.search || '',
  matiere: props.filters.matiere || null,
  date_debut: props.filters.date_debut || '',
  date_fin: props.filters.date_fin || '',
  justifiee: props.filters.justifiee !== undefined ? props.filters.justifiee : null,
});

const showDeleteModal = ref(false);
const selectedAbsence = ref(null);

// Surveiller les changements de filtres
watch(() => filters, (newValue) => {
  const params = {};
  
  if (newValue.search) params.search = newValue.search;
  if (newValue.matiere) params.matiere = newValue.matiere;
  if (newValue.date_debut) params.date_debut = newValue.date_debut;
  if (newValue.date_fin) params.date_fin = newValue.date_fin;
  if (newValue.justifiee !== null) params.justifiee = newValue.justifiee;
  
  router.get(route('professeur.absences.index'), params, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
}, { deep: true });

// Réinitialiser les filtres
const resetFilters = () => {
  filters.reset();
};

// Formater la date
const formatDate = (dateString) => {
  return format(parseISO(dateString), 'PPP', { locale: fr });
};

// Formater l'heure
const formatTime = (timeString) => {
  return format(new Date(`1970-01-01T${timeString}`), 'HH:mm');
};

// Confirmer la suppression
const confirmDelete = (absence) => {
  selectedAbsence.value = absence;
  showDeleteModal.value = true;
};

// Supprimer l'absence
const deleteAbsence = () => {
  if (selectedAbsence.value) {
    router.delete(route('professeur.absences.destroy', selectedAbsence.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        selectedAbsence.value = null;
      },
    });
  }
};
</script>
