<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des présences</h1>
            <p class="mt-2 text-gray-600">Consultez et gérez les présences des étudiants</p>
          </div>
          <div class="flex space-x-3">
            <Link
              :href="route('professeur.presences.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Nouvelle présence
            </Link>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filtres -->
      <div class="mb-6 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Filtrer les présences</h3>
          <form @submit.prevent="applyFilters" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Matière -->
              <div>
                <label for="matiere_id" class="block text-sm font-medium text-gray-700">Matière</label>
                <select
                  id="matiere_id"
                  v-model="filters.matiere_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">Toutes les matières</option>
                  <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                    {{ matiere.nom }}
                  </option>
                </select>
              </div>
              
              <!-- Classe -->
              <div>
                <label for="classe_id" class="block text-sm font-medium text-gray-700">Classe</label>
                <select
                  id="classe_id"
                  v-model="filters.classe_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">Toutes les classes</option>
                  <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                    {{ classe.nom }}
                  </option>
                </select>
              </div>
              
              <!-- Statut -->
              <div>
                <label for="statut" class="block text-sm font-medium text-gray-700">Statut</label>
                <select
                  id="statut"
                  v-model="filters.statut"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">Tous les statuts</option>
                  <option v-for="(label, value) in statuts" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
              </div>
              
              <!-- Période -->
              <div class="flex space-x-2">
                <div class="flex-1">
                  <label for="date_debut" class="block text-sm font-medium text-gray-700">Du</label>
                  <input
                    type="date"
                    id="date_debut"
                    v-model="filters.date_debut"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </div>
                <div class="flex-1">
                  <label for="date_fin" class="block text-sm font-medium text-gray-700">Au</label>
                  <input
                    type="date"
                    id="date_fin"
                    v-model="filters.date_fin"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </div>
              </div>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="resetFilters"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Réinitialiser
              </button>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ loading ? 'Recherche...' : 'Appliquer les filtres' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Statistiques rapides -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Présences</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.presents || 0 }}</div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Retards</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.retards || 0 }}</div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Absences</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.absents || 0 }}</div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Taux de présence</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.taux_presence || 0 }}%</div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des présences -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Liste des présences</h3>
          <div class="flex space-x-2">
            <button
              @click="exportToExcel"
              class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Exporter
            </button>
          </div>
        </div>
        
        <div class="border-t border-gray-200">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Étudiant
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Matière
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Classe
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="presences.data.length === 0">
                  <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    Aucune présence trouvée.
                  </td>
                </tr>
                <tr v-for="presence in presences.data" :key="presence.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(presence.date_seance) }}
                    <div class="text-xs text-gray-500">
                      {{ presence.heure_debut }} - {{ presence.heure_fin }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-600 font-medium">
                          {{ getInitials(presence.etudiant.nom, presence.etudiant.prenom) }}
                        </span>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ presence.etudiant.nom }} {{ presence.etudiant.prenom }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ presence.etudiant.numero_etudiant }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ presence.matiere.nom }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ presence.classe.nom }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="getStatusBadgeClass(presence.statut)"
                    >
                      {{ getStatusLabel(presence.statut) }}
                    </span>
                    <div v-if="presence.duree_retard" class="text-xs text-gray-500 mt-1">
                      Retard: {{ presence.duree_retard }} min
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('professeur.presences.show', presence.id)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                    >
                      Voir
                    </Link>
                    <Link
                      :href="route('professeur.presences.edit', presence.id)"
                      class="text-indigo-600 hover:text-indigo-900 mr-3"
                    >
                      Modifier
                    </Link>
                    <button
                      @click="confirmDelete(presence)"
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
                :href="presences.prev_page_url"
                :class="{ 'opacity-50 cursor-not-allowed': !presences.prev_page_url }"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Précédent
              </Link>
              <Link
                :href="presences.next_page_url"
                :class="{ 'opacity-50 cursor-not-allowed': !presences.next_page_url }"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Suivant
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Affichage de
                  <span class="font-medium">{{ presences.from || 0 }}</span>
                  à
                  <span class="font-medium">{{ presences.to || 0 }}</span>
                  sur
                  <span class="font-medium">{{ presences.total }}</span>
                  résultats
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <Link
                    v-for="(link, index) in presences.links"
                    :key="index"
                    :href="link.url || '#'"
                    :class="[
                      link.active 
                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                      !link.url ? 'opacity-50 cursor-not-allowed' : ''
                    ]"
                    v-html="link.label"
                  ></Link>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <ConfirmationModal
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="deletePresence"
    >
      <template #title>
        Supprimer une présence
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer cette entrée de présence ? Cette action est irréversible.
      </template>
    </ConfirmationModal>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  presences: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({
      matiere_id: '',
      classe_id: '',
      statut: '',
      date_debut: '',
      date_fin: ''
    })
  },
  matieres: {
    type: Array,
    default: () => []
  },
  classes: {
    type: Array,
    default: () => []
  },
  statuts: {
    type: Object,
    default: () => ({
      present: 'Présent',
      absent: 'Absent',
      retard: 'Retard',
      justifie: 'Absence justifiée'
    })
  },
  stats: {
    type: Object,
    default: () => ({
      presents: 0,
      retards: 0,
      absents: 0,
      taux_presence: 0
    })
  }
});

// État local
const loading = ref(false);
const showDeleteModal = ref(false);
const selectedPresence = ref(null);
const localFilters = reactive({ ...props.filters });

// Appliquer les filtres avec debounce
const applyFilters = debounce(() => {
  loading.value = true;
  
  // Construire l'URL avec les paramètres de filtre
  const query = {};
  Object.keys(localFilters).forEach(key => {
    if (localFilters[key]) {
      query[key] = localFilters[key];
    }
  });
  
  // Mettre à jour l'URL avec les nouveaux filtres
  router.get(route('professeur.presences.index'), query, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    onFinish: () => {
      loading.value = false;
    }
  });
}, 300);

// Réinitialiser les filtres
const resetFilters = () => {
  Object.keys(localFilters).forEach(key => {
    localFilters[key] = '';
  });
  applyFilters();
};

// Confirmer la suppression d'une présence
const confirmDelete = (presence) => {
  selectedPresence.value = presence;
  showDeleteModal.value = true;
};

// Supprimer une présence
const deletePresence = () => {
  if (!selectedPresence.value) return;
  
  router.delete(route('professeur.presences.destroy', selectedPresence.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      selectedPresence.value = null;
    }
  });
};

// Exporter les données en Excel
const exportToExcel = () => {
  // Construire l'URL d'export avec les filtres actuels
  const query = { ...localFilters, export: 'excel' };
  const queryString = Object.keys(query)
    .filter(key => query[key] !== '')
    .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(query[key])}`)
    .join('&');
    
  // Ouvrir l'URL d'export dans un nouvel onglet
  window.open(`${route('professeur.presences.index')}?${queryString}`, '_blank');
};

// Formater une date au format JJ/MM/AAAA
const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

// Obtenir les initiales d'un nom et prénom
const getInitials = (nom, prenom) => {
  return `${prenom ? prenom.charAt(0) : ''}${nom ? nom.charAt(0) : ''}`.toUpperCase();
};

// Obtenir le libellé d'un statut
const getStatusLabel = (status) => {
  return props.statuts[status] || status;
};

// Obtenir la classe CSS pour un statut
const getStatusBadgeClass = (status) => {
  const classes = {
    present: 'bg-green-100 text-green-800',
    absent: 'bg-red-100 text-red-800',
    retard: 'bg-yellow-100 text-yellow-800',
    justifie: 'bg-blue-100 text-blue-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

// Mettre à jour les filtres locaux lorsque les props changent
watch(() => props.filters, (newFilters) => {
  Object.assign(localFilters, newFilters);
}, { deep: true, immediate: true });
</script>
