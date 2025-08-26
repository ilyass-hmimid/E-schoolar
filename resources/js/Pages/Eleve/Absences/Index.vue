<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Mes absences</h1>
        <p class="mt-1 text-sm text-gray-500">Historique de toutes vos absences</p>
      </div>
      <div class="mt-4 md:mt-0">
        <Link 
          :href="route('eleve.absences.justifier')" 
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Justifier une absence
        </Link>
      </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label for="matiere" class="block text-sm font-medium text-gray-700">Matière</label>
          <select 
            id="matiere" 
            v-model="filters.matiere" 
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Toutes les matières</option>
            <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
              {{ matiere.nom }}
            </option>
          </select>
        </div>
        <div>
          <label for="statut" class="block text-sm font-medium text-gray-700">Statut</label>
          <select 
            id="statut" 
            v-model="filters.statut" 
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Tous les statuts</option>
            <option value="justifiee">Justifiées</option>
            <option value="non_justifiee">Non justifiées</option>
          </select>
        </div>
        <div>
          <label for="debut" class="block text-sm font-medium text-gray-700">Du</label>
          <input 
            type="date" 
            id="debut" 
            v-model="filters.debut" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="fin" class="block text-sm font-medium text-gray-700">Au</label>
          <input 
            type="date" 
            id="fin" 
            v-model="filters.fin" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
      </div>
      <div class="mt-4 flex justify-end space-x-3">
        <button 
          type="button" 
          @click="resetFilters"
          class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Réinitialiser
        </button>
        <button 
          type="button" 
          @click="applyFilters"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Appliquer
        </button>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total des absences</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Non justifiées</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.non_justifiees }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Justifiées</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.justifiees }}</p>
          </div>
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
                Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Matière
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Professeur
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
            <tr v-for="absence in absences.data" :key="absence.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(absence.date_absence) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ absence.enseignement.matiere.nom }}</div>
                <div class="text-sm text-gray-500">Séance de {{ formatHeure(absence.enseignement.date_debut) }} à {{ formatHeure(absence.enseignement.date_fin) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ absence.enseignement.professeur.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': absence.est_justifiee,
                    'bg-yellow-100 text-yellow-800': !absence.est_justifiee
                  }"
                >
                  {{ absence.est_justifiee ? 'Justifiée' : 'Non justifiée' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <Link 
                  :href="route('eleve.absences.show', absence.id)" 
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Détails
                </Link>
                <Link 
                  v-if="!absence.est_justifiee"
                  :href="route('eleve.absences.justifier', absence.id)" 
                  class="text-yellow-600 hover:text-yellow-900"
                >
                  Justifier
                </Link>
              </td>
            </tr>
            <tr v-if="absences.data.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                Aucune absence trouvée
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
              <span class="font-medium">{{ absences.from || 0 }}</span>
              à 
              <span class="font-medium">{{ absences.to || 0 }}</span>
              sur 
              <span class="font-medium">{{ absences.total }}</span>
              résultats
            </p>
          </div>
          <div v-if="absences.last_page > 1">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <Link 
                v-for="(link, index) in absences.links" 
                :key="index"
                :href="link.url || '#'"
                :class="{
                  'bg-indigo-50 border-indigo-500 text-indigo-600 z-10': link.active,
                  'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
                  'pointer-events-none bg-gray-100 text-gray-300': !link.url,
                  'rounded-l-md': index === 0,
                  'rounded-r-md': index === absences.links.length - 1
                }"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                v-html="link.label"
              />
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const props = defineProps({
  absences: {
    type: Object,
    required: true
  },
  matieres: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({
      matiere: '',
      statut: '',
      debut: '',
      fin: ''
    })
  },
  stats: {
    type: Object,
    required: true
  }
});

// Référence locale pour les filtres
const localFilters = reactive({
  matiere: props.filters.matiere || '',
  statut: props.filters.statut || '',
  debut: props.filters.debut || '',
  fin: props.filters.fin || ''
});

// Vérifier si les filtres ont changé
const filtersChanged = computed(() => {
  return (
    localFilters.matiere !== props.filters.matiere ||
    localFilters.statut !== props.filters.statut ||
    localFilters.debut !== props.filters.debut ||
    localFilters.fin !== props.filters.fin
  );
});

// Appliquer les filtres
const applyFilters = () => {
  const query = {};
  
  if (localFilters.matiere) query.matiere = localFilters.matiere;
  if (localFilters.statut) query.statut = localFilters.statut;
  if (localFilters.debut) query.debut = localFilters.debut;
  if (localFilters.fin) query.fin = localFilters.fin;
  
  router.get(route('eleve.absences.index'), query, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
};

// Réinitialiser les filtres
const resetFilters = () => {
  localFilters.matiere = '';
  localFilters.statut = '';
  localFilters.debut = '';
  localFilters.fin = '';
  
  router.get(route('eleve.absences.index'), {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
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
