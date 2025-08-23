<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des salaires</h1>
            <p class="mt-2 text-gray-600">Calcul et suivi des salaires des professeurs</p>
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
              Nouveau salaire
            </button>
            <button
              v-if="canCreate"
              @click="openCalculModal"
              class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              Calcul automatique
            </button>
            <button
              @click="exportSalaires"
              class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors flex items-center"
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
      <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total salaires</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.total_salaires }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Montant brut</p>
              <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_montant_brut) }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Montant net</p>
              <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_montant_net) }}</p>
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
              <p class="text-sm font-medium text-gray-600">En attente</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.salaires_en_attente }}</p>
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
              <p class="text-sm font-medium text-gray-600">Payés</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.salaires_payes }}</p>
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
                placeholder="Nom ou email du professeur..."
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

            <!-- Statut -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
              <select
                v-model="filters.statut"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              >
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="paye">Payé</option>
              </select>
            </div>

            <!-- Mois -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Mois</label>
              <input
                v-model="filters.mois_periode"
                type="month"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              />
            </div>

            <!-- Actions -->
            <div class="md:col-span-4 flex items-end space-x-4">
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

      <!-- Tableau des salaires -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Professeur
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Matière
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Période
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Élèves
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Montant brut
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Commission
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Montant net
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
              <tr v-for="salaire in salaires.data" :key="salaire.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">
                          {{ salaire.professeur.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ salaire.professeur.name }}</div>
                      <div class="text-sm text-gray-500">{{ salaire.professeur.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ salaire.matiere.nom }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatMonth(salaire.mois_periode) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ salaire.nombre_eleves }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatCurrency(salaire.montant_brut) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ salaire.commission_prof }}%
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatCurrency(salaire.montant_net) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(salaire.statut)">
                    {{ getStatusLabel(salaire.statut) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <button
                      @click="viewSalaire(salaire)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Voir
                    </button>
                    <button
                      @click="downloadSalarySlip(salaire)"
                      class="text-green-600 hover:text-green-900"
                    >
                      PDF
                    </button>
                    <button
                      v-if="canPay && salaire.statut === 'en_attente'"
                      @click="paySalaire(salaire)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Payer
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="salaires.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                v-if="salaires.prev_page_url"
                @click="changePage(salaires.current_page - 1)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Précédent
              </button>
              <button
                v-if="salaires.next_page_url"
                @click="changePage(salaires.current_page + 1)"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Suivant
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Affichage de <span class="font-medium">{{ salaires.from }}</span> à <span class="font-medium">{{ salaires.to }}</span> sur <span class="font-medium">{{ salaires.total }}</span> résultats
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    v-for="page in getPageNumbers()"
                    :key="page"
                    @click="changePage(page)"
                    :class="[
                      page === salaires.current_page
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

    <!-- Modal de calcul automatique -->
    <div v-if="showCalculModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Calcul automatique des salaires</h3>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mois de période <span class="text-red-500">*</span>
            </label>
            <input
              v-model="calculForm.mois_periode"
              type="month"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Professeur (optionnel)
            </label>
            <select
              v-model="calculForm.professeur_id"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Tous les professeurs</option>
              <option
                v-for="professeur in professeurs"
                :key="professeur.id"
                :value="professeur.id"
              >
                {{ professeur.name }}
              </option>
            </select>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="showCalculModal = false"
              class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
            <button
              @click="calculerSalaires"
              :disabled="!calculForm.mois_periode"
              class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Calculer
            </button>
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
  salaires: Object,
  stats: Object,
  filters: Object,
  matieres: Array,
  professeurs: Array,
  canCreate: Boolean,
  canPay: Boolean,
});

const filters = reactive({
  search: props.filters.search || '',
  matiere_id: props.filters.matiere_id || '',
  statut: props.filters.statut || '',
  mois_periode: props.filters.mois_periode || '',
});

const showCalculModal = ref(false);
const calculForm = reactive({
  mois_periode: new Date().toISOString().slice(0, 7),
  professeur_id: '',
});

const applyFilters = () => {
  router.get('/salaires', filters, {
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
  router.get('/salaires', { ...filters, page }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getPageNumbers = () => {
  const pages = [];
  const current = props.salaires.current_page;
  const last = props.salaires.last_page;
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i);
  }
  
  return pages;
};

const openCreateModal = () => {
  router.visit('/salaires/create');
};

const openCalculModal = () => {
  showCalculModal.value = true;
};

const viewSalaire = (salaire) => {
  router.visit(`/salaires/${salaire.id}`);
};

const downloadSalarySlip = (salaire) => {
  window.open(`/rapports/salaires/${salaire.id}/bulletin`, '_blank');
};

const paySalaire = (salaire) => {
  const datePaiement = prompt('Date de paiement (YYYY-MM-DD):', new Date().toISOString().slice(0, 10));
  if (datePaiement) {
    router.post(`/salaires/${salaire.id}/payer`, {
      date_paiement: datePaiement
    });
  }
};

const calculerSalaires = async () => {
  if (!calculForm.mois_periode) return;

  try {
    const response = await fetch('/salaires/calculer-automatiquement', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(calculForm),
    });

    const result = await response.json();
    
    if (result.success) {
      alert(result.message);
      showCalculModal.value = false;
      router.reload();
    } else {
      alert('Erreur: ' + result.message);
    }
  } catch (error) {
    alert('Erreur lors du calcul: ' + error.message);
  }
};

const exportSalaires = () => {
  const params = new URLSearchParams(filters);
  window.open(`/salaires/rapport?${params.toString()}`, '_blank');
};

const getStatusLabel = (statut) => {
  const labels = {
    'en_attente': 'En attente',
    'paye': 'Payé',
  };
  return labels[statut] || statut;
};

const getStatusClass = (statut) => {
  const classes = {
    'en_attente': 'bg-yellow-100 text-yellow-800',
    'paye': 'bg-green-100 text-green-800',
  };
  return classes[statut] || 'bg-gray-100 text-gray-800';
};

const formatCurrency = (amount) => {
  if (!amount) return '0,00 DH';
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'MAD'
  }).format(amount);
};

const formatMonth = (monthString) => {
  if (!monthString) return '';
  const [year, month] = monthString.split('-');
  const date = new Date(year, month - 1);
  return date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long' });
};
</script>
