<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des absences</h1>
            <p class="mt-2 text-gray-600">Suivi et gestion des présences des élèves</p>
          </div>
          <div class="flex items-center space-x-4">
            <button
              v-if="canCreate"
              @click="openCreateModal"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Nouvelle absence
            </button>
            <button
              @click="exportAbsences"
              class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Exporter
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total absences</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.total_absences }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Justifiées</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.absences_justifiees }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Non justifiées</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.absences_non_justifiees }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Retards</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.retards }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtres -->
      <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Filtres</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Recherche -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Nom ou email de l'élève..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @input="applyFilters"
              />
            </div>

            <!-- Matière -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Matière</label>
              <select
                v-model="filters.matiere_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              >
                <option value="">Toutes les matières</option>
                <option
                  v-for="matiere in matieres"
                  :key="matiere.id"
                  :value="matiere.id"
                >
                  {{ matiere.nom }}
                </option>
              </select>
            </div>

            <!-- Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
              <select
                v-model="filters.type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              >
                <option value="">Tous les types</option>
                <option value="absence">Absence</option>
                <option value="retard">Retard</option>
                <option value="sortie_anticipée">Sortie anticipée</option>
              </select>
            </div>

            <!-- Justifiée -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
              <select
                v-model="filters.justifiee"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              >
                <option value="">Tous les statuts</option>
                <option value="1">Justifiée</option>
                <option value="0">Non justifiée</option>
              </select>
            </div>

            <!-- Date début -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
              <input
                v-model="filters.date_debut"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              />
            </div>

            <!-- Date fin -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
              <input
                v-model="filters.date_fin"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              />
            </div>

            <!-- Actions -->
            <div class="md:col-span-2 flex items-end space-x-4">
              <button
                @click="clearFilters"
                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
              >
                Effacer les filtres
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tableau des absences -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Élève
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Matière
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Statut
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="absence in absences.data" :key="absence.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">
                          {{ absence.etudiant.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ absence.etudiant.name }}</div>
                      <div class="text-sm text-gray-500">{{ absence.etudiant.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ absence.matiere.nom }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getTypeClass(absence.type)">
                    {{ getTypeLabel(absence.type) }}
                  </span>
                  <div v-if="absence.type === 'retard' && absence.duree_retard" class="text-xs text-gray-500 mt-1">
                    {{ absence.duree_retard }} min
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(absence.date_absence) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(absence.justifiee)">
                    {{ absence.justifiee ? 'Justifiée' : 'Non justifiée' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <button
                      @click="viewAbsence(absence)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Voir
                    </button>
                    <button
                      v-if="canJustify && !absence.justifiee"
                      @click="justifyAbsence(absence)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Justifier
                    </button>
                    <button
                      v-if="canJustify && absence.justifiee"
                      @click="unjustifyAbsence(absence)"
                      class="text-orange-600 hover:text-orange-900"
                    >
                      Non justifier
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="absences.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                v-if="absences.prev_page_url"
                @click="changePage(absences.current_page - 1)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Précédent
              </button>
              <button
                v-if="absences.next_page_url"
                @click="changePage(absences.current_page + 1)"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Suivant
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Affichage de <span class="font-medium">{{ absences.from }}</span> à <span class="font-medium">{{ absences.to }}</span> sur <span class="font-medium">{{ absences.total }}</span> résultats
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    v-for="page in getPageNumbers()"
                    :key="page"
                    @click="changePage(page)"
                    :class="[
                      page === absences.current_page
                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                  >
                    {{ page }}
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  absences: Object,
  stats: Object,
  filters: Object,
  matieres: Array,
  canCreate: Boolean,
  canJustify: Boolean,
});

const filters = reactive({
  search: props.filters.search || '',
  matiere_id: props.filters.matiere_id || '',
  type: props.filters.type || '',
  justifiee: props.filters.justifiee || '',
  date_debut: props.filters.date_debut || '',
  date_fin: props.filters.date_fin || '',
});

const applyFilters = () => {
  router.get('/absences', filters, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = '';
  });
  applyFilters();
};

const changePage = (page) => {
  router.get('/absences', { ...filters, page }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getPageNumbers = () => {
  const pages = [];
  const current = props.absences.current_page;
  const last = props.absences.last_page;
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i);
  }
  
  return pages;
};

const openCreateModal = () => {
  router.visit('/absences/create');
};

const viewAbsence = (absence) => {
  router.visit(`/absences/${absence.id}`);
};

const justifyAbsence = (absence) => {
  const justification = prompt('Motif de justification:');
  if (justification) {
    router.post(`/absences/${absence.id}/justifier`, {
      justification: justification
    });
  }
};

const unjustifyAbsence = (absence) => {
  if (confirm('Êtes-vous sûr de vouloir marquer cette absence comme non justifiée ?')) {
    router.post(`/absences/${absence.id}/non-justifier`);
  }
};

const exportAbsences = () => {
  const params = new URLSearchParams(filters);
  window.open(`/absences/rapport?${params.toString()}`, '_blank');
};

const getTypeLabel = (type) => {
  const labels = {
    'absence': 'Absence',
    'retard': 'Retard',
    'sortie_anticipée': 'Sortie anticipée',
  };
  return labels[type] || type;
};

const getTypeClass = (type) => {
  const classes = {
    'absence': 'bg-red-100 text-red-800',
    'retard': 'bg-orange-100 text-orange-800',
    'sortie_anticipée': 'bg-yellow-100 text-yellow-800',
  };
  return classes[type] || 'bg-gray-100 text-gray-800';
};

const getStatusClass = (justifiee) => {
  return justifiee 
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>
