<template>
  <AppLayout title="Gestion des salaires">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Gestion des salaires
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-6 overflow-hidden bg-white shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex flex-col space-y-4 md:flex-row md:items-center md:justify-between md:space-y-0">
              <div class="flex items-center space-x-4">
                <h3 class="text-lg font-medium">Liste des salaires</h3>
                
                <Link 
                  :href="route('admin.salaires.calculer')" 
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <PlusIcon class="w-5 h-5 mr-2 -ml-1" />
                  Calculer les salaires
                </Link>
              </div>

              <div class="flex items-center space-x-2">
                <div class="relative">
                  <select 
                    v-model="filters.mois_periode" 
                    @change="updateFilters"
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                  >
                    <option value="">Tous les mois</option>
                    <option v-for="month in months" :key="month.value" :value="month.value">
                      {{ month.label }}
                    </option>
                  </select>
                </div>

                <div class="relative">
                  <select 
                    v-model="filters.statut" 
                    @change="updateFilters"
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                  >
                    <option value="">Tous les statuts</option>
                    <option v-for="statut in statuts" :key="statut.value" :value="statut.value">
                      {{ statut.label }}
                    </option>
                  </select>
                </div>

                <div class="relative">
                  <select 
                    v-model="filters.professeur_id" 
                    @change="updateFilters"
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                  >
                    <option value="">Tous les professeurs</option>
                    <option v-for="professeur in professeurs" :key="professeur.id" :value="professeur.id">
                      {{ professeur.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Période
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Professeur
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Matière
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                    Élèves
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                    Montant Brut
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                    Montant Net
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                    Statut
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="salaires.data.length === 0">
                  <td colspan="8" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                    Aucun salaire trouvé.
                  </td>
                </tr>
                <tr v-for="salaire in salaires.data" :key="salaire.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                    {{ formatMonth(salaire.mois_periode) }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                    {{ salaire.professeur.name }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                    {{ salaire.matiere.nom }}
                  </td>
                  <td class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                    {{ salaire.nombre_eleves }}
                  </td>
                  <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">
                    {{ formatCurrency(salaire.montant_brut) }}
                  </td>
                  <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                    {{ formatCurrency(salaire.montant_net) }}
                  </td>
                  <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                    <span :class="getStatusBadgeClass(salaire.statut)">
                      {{ getStatusLabel(salaire.statut) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                    <div class="flex items-center justify-end space-x-2">
                      <Link 
                        :href="route('admin.salaires.show', salaire.id)" 
                        class="text-blue-600 hover:text-blue-900"
                        title="Voir les détails"
                      >
                        <EyeIcon class="w-5 h-5" />
                      </Link>
                      <Link 
                        :href="route('admin.salaires.edit', salaire.id)" 
                        class="text-indigo-600 hover:text-indigo-900"
                        title="Modifier"
                      >
                        <PencilAltIcon class="w-5 h-5" />
                      </Link>
                      <button 
                        v-if="salaire.statut === 'en_attente'"
                        @click="validerPaiement(salaire)"
                        class="text-green-600 hover:text-green-900"
                        title="Valider le paiement"
                      >
                        <CheckCircleIcon class="w-5 h-5" />
                      </button>
                      <button 
                        v-if="salaire.statut === 'en_attente'"
                        @click="annulerSalaire(salaire)"
                        class="text-red-600 hover:text-red-900"
                        title="Annuler"
                      >
                        <XCircleIcon class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
              <tfoot v-if="salaires.data.length > 0" class="bg-gray-50">
                <tr>
                  <td colspan="4" class="px-6 py-3 text-sm font-medium text-right text-gray-500">
                    Total:
                  </td>
                  <td class="px-6 py-3 text-sm font-medium text-right text-gray-900">
                    {{ formatCurrency(totals.montant_brut) }}
                  </td>
                  <td class="px-6 py-3 text-sm font-medium text-right text-gray-900">
                    {{ formatCurrency(totals.montant_net) }}
                  </td>
                  <td colspan="2"></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="px-6 py-4 bg-white border-t border-gray-200">
            <Pagination :links="salaires.links" />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de validation de paiement -->
    <ConfirmationModal :show="showValidationModal" @close="showValidationModal = false">
      <template #title>
        Valider le paiement
      </template>
      <template #content>
        <p>Êtes-vous sûr de vouloir valider le paiement du salaire de <span class="font-semibold">{{ selectedSalaire?.professeur?.name }}</span> pour <span class="font-semibold">{{ selectedSalaire?.matiere?.nom }}</span> ?</p>
        <div class="mt-4">
          <label for="date_paiement" class="block text-sm font-medium text-gray-700">Date de paiement</label>
          <input 
            type="date" 
            id="date_paiement" 
            v-model="datePaiement" 
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          >
        </div>
      </template>
      <template #footer>
        <SecondaryButton @click="showValidationModal = false">
          Annuler
        </SecondaryButton>
        <PrimaryButton @click="confirmValidation" class="ml-3">
          Confirmer
        </PrimaryButton>
      </template>
    </ConfirmationModal>

    <!-- Modal de confirmation d'annulation -->
    <ConfirmationModal :show="showAnnulationModal" @close="showAnnulationModal = false">
      <template #title>
        Annuler le salaire
      </template>
      <template #content>
        <p>Êtes-vous sûr de vouloir annuler le salaire de <span class="font-semibold">{{ selectedSalaire?.professeur?.name }}</span> pour <span class="font-semibold">{{ selectedSalaire?.matiere?.nom }}</span> ?</p>
        <div class="mt-4">
          <label for="raison_annulation" class="block text-sm font-medium text-gray-700">Raison de l'annulation</label>
          <textarea 
            id="raison_annulation" 
            v-model="raisonAnnulation" 
            rows="3"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Veuillez indiquer la raison de l'annulation..."
          ></textarea>
        </div>
      </template>
      <template #footer>
        <SecondaryButton @click="showAnnulationModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton @click="confirmAnnulation" class="ml-3">
          Confirmer l'annulation
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { 
  EyeIcon, 
  PencilAltIcon, 
  CheckCircleIcon, 
  XCircleIcon,
  PlusIcon
} from '@heroicons/vue/outline';

const props = defineProps({
  salaires: Object,
  filters: {
    type: Object,
    default: () => ({
      mois_periode: '',
      statut: '',
      professeur_id: ''
    })
  },
  professeurs: {
    type: Array,
    default: () => []
  },
  statuts: {
    type: Array,
    default: () => []
  }
});

const showValidationModal = ref(false);
const showAnnulationModal = ref(false);
const selectedSalaire = ref(null);
const datePaiement = ref(new Date().toISOString().split('T')[0]);
const raisonAnnulation = ref('');

// Générer la liste des 12 derniers mois
const months = computed(() => {
  const result = [];
  const date = new Date();
  
  for (let i = 0; i < 12; i++) {
    const month = date.getMonth() + 1;
    const year = date.getFullYear();
    const monthStr = month < 10 ? `0${month}` : month;
    
    result.unshift({
      value: `${year}-${monthStr}`,
      label: new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })
    });
    
    date.setMonth(date.getMonth() - 1);
  }
  
  return result;
});

// Calculer les totaux
const totals = computed(() => {
  return {
    montant_brut: props.salaires.data.reduce((sum, salaire) => sum + parseFloat(salaire.montant_brut), 0),
    montant_net: props.salaires.data.reduce((sum, salaire) => sum + parseFloat(salaire.montant_net), 0)
  };
});

// Mise à jour des filtres
const updateFilters = () => {
  router.get(route('admin.salaires.index'), props.filters, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
};

// Formater la monnaie
const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', { 
    style: 'currency', 
    currency: 'MAD',
    minimumFractionDigits: 2
  }).format(value);
};

// Formater le mois
const formatMonth = (dateStr) => {
  if (!dateStr) return '';
  const [year, month] = dateStr.split('-');
  return new Date(year, month - 1).toLocaleDateString('fr-FR', { 
    month: 'long', 
    year: 'numeric' 
  });
};

// Obtenir la classe CSS pour le badge de statut
const getStatusBadgeClass = (status) => {
  const classes = {
    'px-2 py-1 text-xs font-semibold rounded-full': true,
    'bg-yellow-100 text-yellow-800': status === 'en_attente',
    'bg-green-100 text-green-800': status === 'paye',
    'bg-red-100 text-red-800': status === 'annule'
  };
  
  return classes;
};

// Obtenir le libellé du statut
const getStatusLabel = (status) => {
  const statusMap = {
    'en_attente': 'En attente',
    'paye': 'Payé',
    'annule': 'Annulé'
  };
  
  return statusMap[status] || status;
};

// Ouvrir la modale de validation de paiement
const validerPaiement = (salaire) => {
  selectedSalaire.value = salaire;
  datePaiement.value = new Date().toISOString().split('T')[0];
  showValidationModal.value = true;
};

// Confirmer la validation du paiement
const confirmValidation = () => {
  if (!selectedSalaire.value) return;
  
  router.post(route('admin.salaires.valider', selectedSalaire.value.id), {
    date_paiement: datePaiement.value
  }, {
    onSuccess: () => {
      showValidationModal.value = false;
    }
  });
};

// Ouvrir la modale d'annulation
const annulerSalaire = (salaire) => {
  selectedSalaire.value = salaire;
  raisonAnnulation.value = '';
  showAnnulationModal.value = true;
};

// Confirmer l'annulation
const confirmAnnulation = () => {
  if (!selectedSalaire.value || !raisonAnnulation.value.trim()) {
    // Afficher une erreur si la raison est vide
    return;
  }
  
  router.post(route('admin.salaires.annuler', selectedSalaire.value.id), {
    commentaires: raisonAnnulation.value
  }, {
    onSuccess: () => {
      showAnnulationModal.value = false;
    }
  });
};

// Exporter au format Excel
const exportExcel = () => {
  const params = new URLSearchParams();
  
  if (props.filters.mois_periode) {
    params.append('mois_periode', props.filters.mois_periode);
  }
  
  if (props.filters.statut) {
    params.append('statut', props.filters.statut);
  }
  
  if (props.filters.professeur_id) {
    params.append('professeur_id', props.filters.professeur_id);
  }
  
  window.location.href = `${route('admin.salaires.export')}?${params.toString()}&format=excel`;
};

// Exporter au format PDF
const exportPdf = () => {
  const params = new URLSearchParams();
  
  if (props.filters.mois_periode) {
    params.append('mois_periode', props.filters.mois_periode);
  }
  
  if (props.filters.statut) {
    params.append('statut', props.filters.statut);
  }
  
  if (props.filters.professeur_id) {
    params.append('professeur_id', props.filters.professeur_id);
  }
  
  window.location.href = `${route('admin.salaires.export')}?${params.toString()}&format=pdf`;
};
</script>
