<template>
  <AppLayout :title="`Salaire - ${salaire.professeur.name}`">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Détails du salaire
        </h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('admin.salaires.index')" 
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <ArrowLeftIcon class="w-5 h-5 mr-2 -ml-1" />
            Retour à la liste
          </Link>
          
          <Link 
            v-if="salaire.statut === 'en_attente'"
            :href="route('admin.salaires.edit', salaire.id)"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <PencilIcon class="w-5 h-5 mr-2 -ml-1" />
            Modifier
          </Link>
          
          <button
            v-if="salaire.statut === 'en_attente'"
            @click="validerPaiement"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <CheckIcon class="w-5 h-5 mr-2 -ml-1" />
            Valider le paiement
          </button>
          
          <button
            v-if="salaire.statut === 'en_attente'"
            @click="annulerSalaire"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            <XIcon class="w-5 h-5 mr-2 -ml-1" />
            Annuler
          </button>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <div class="flex flex-col items-start justify-between md:flex-row md:items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Salaire de {{ salaire.professeur.name }}
                </h3>
                <p class="max-w-2xl mt-1 text-sm text-gray-500">
                  Période : {{ formatMonth(salaire.mois_periode) }}
                </p>
              </div>
              <div class="mt-4 md:mt-0">
                <span :class="getStatusBadgeClass(salaire.statut)">
                  {{ getStatusLabel(salaire.statut) }}
                </span>
              </div>
            </div>
          </div>
          
          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
              <!-- Informations générales -->
              <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 uppercase">Informations générales</h4>
                <dl class="mt-2 space-y-2">
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Professeur</dt>
                    <dd class="text-sm text-gray-900">{{ salaire.professeur.name }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Matière</dt>
                    <dd class="text-sm text-gray-900">{{ salaire.matiere.nom }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Période</dt>
                    <dd class="text-sm text-gray-900">{{ formatMonth(salaire.mois_periode) }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                    <dd class="text-sm text-gray-900">{{ formatDateTime(salaire.created_at) }}</dd>
                  </div>
                  <div v-if="salaire.date_paiement" class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Date de paiement</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(salaire.date_paiement) }}</dd>
                  </div>
                </dl>
              </div>
              
              <!-- Détails du calcul -->
              <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 uppercase">Détails du calcul</h4>
                <dl class="mt-2 space-y-2">
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Nombre d'élèves</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ salaire.nombre_eleves }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Prix unitaire</dt>
                    <dd class="text-sm text-gray-900">{{ formatCurrency(salaire.prix_unitaire) }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Commission professeur</dt>
                    <dd class="text-sm text-gray-900">{{ salaire.commission_prof }}%</dd>
                  </div>
                  <div class="pt-2 mt-2 border-t border-gray-200">
                    <div class="flex justify-between">
                      <dt class="text-base font-medium text-gray-900">Montant brut</dt>
                      <dd class="text-base font-medium text-gray-900">{{ formatCurrency(salaire.montant_brut) }}</dd>
                    </div>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">- Commission</dt>
                    <dd class="text-sm text-gray-900">-{{ formatCurrency(salaire.montant_commission) }}</dd>
                  </div>
                  <div class="pt-2 mt-2 border-t border-gray-200">
                    <div class="flex justify-between">
                      <dt class="text-lg font-bold text-gray-900">Montant net</dt>
                      <dd class="text-lg font-bold text-gray-900">{{ formatCurrency(salaire.montant_net) }}</dd>
                    </div>
                  </div>
                </dl>
              </div>
              
              <!-- Informations complémentaires -->
              <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 uppercase">Informations complémentaires</h4>
                <div class="mt-2 space-y-2">
                  <div v-if="salaire.commentaires">
                    <h5 class="text-sm font-medium text-gray-500">Commentaires</h5>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ salaire.commentaires }}</p>
                  </div>
                  <div v-else class="text-sm text-gray-500">
                    Aucun commentaire.
                  </div>
                  
                  <div v-if="salaire.statut === 'annule' && salaire.raison_annulation" class="mt-4">
                    <h5 class="text-sm font-medium text-red-600">Raison de l'annulation</h5>
                    <p class="mt-1 text-sm text-red-700">{{ salaire.raison_annulation }}</p>
                  </div>
                  
                  <div v-if="salaire.statut === 'paye'" class="mt-4">
                    <h5 class="text-sm font-medium text-gray-500">Informations de paiement</h5>
                    <p class="mt-1 text-sm text-gray-900">
                      Paiement effectué le {{ formatDateTime(salaire.date_paiement) }}
                    </p>
                    <p v-if="salaire.paye_par" class="text-sm text-gray-500">
                      Par {{ salaire.paye_par.name }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end mt-8 space-x-3">
              <Link 
                :href="route('admin.salaires.index')" 
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Retour à la liste
              </Link>
              
              <Link 
                v-if="salaire.statut === 'en_attente'"
                :href="route('admin.salaires.edit', salaire.id)"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <PencilIcon class="w-5 h-5 mr-2 -ml-1" />
                Modifier
              </Link>
              
              <button
                v-if="salaire.statut === 'en_attente'"
                @click="validerPaiement"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <CheckIcon class="w-5 h-5 mr-2 -ml-1" />
                Valider le paiement
              </button>
              
              <button
                v-if="salaire.statut === 'en_attente'"
                @click="annulerSalaire"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <XIcon class="w-5 h-5 mr-2 -ml-1" />
                Annuler
              </button>
              
              <button
                @click="imprimer"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PrinterIcon class="w-5 h-5 mr-2 -ml-1" />
                Imprimer
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal de validation de paiement -->
    <ConfirmationModal :show="showValidationModal" @close="showValidationModal = false">
      <template #title>
        Valider le paiement
      </template>
      <template #content>
        <p>Êtes-vous sûr de vouloir valider le paiement du salaire de <span class="font-semibold">{{ salaire.professeur.name }}</span> pour <span class="font-semibold">{{ salaire.matiere.nom }}</span> ?</p>
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
    
    <!-- Modal d'annulation -->
    <ConfirmationModal :show="showAnnulationModal" @close="showAnnulationModal = false">
      <template #title>
        Annuler le salaire
      </template>
      <template #content>
        <p>Êtes-vous sûr de vouloir annuler le salaire de <span class="font-semibold">{{ salaire.professeur.name }}</span> pour <span class="font-semibold">{{ salaire.matiere.nom }}</span> ?</p>
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
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { 
  ArrowLeftIcon, 
  PencilIcon, 
  CheckIcon, 
  XIcon,
  PrinterIcon
} from '@heroicons/vue/outline';

const props = defineProps({
  salaire: {
    type: Object,
    required: true
  }
});

// État des modales
const showValidationModal = ref(false);
const showAnnulationModal = ref(false);

// Données des formulaires
const datePaiement = ref(new Date().toISOString().split('T')[0]);
const raisonAnnulation = ref('');

// Formater une date au format JJ/MM/AAAA
const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('fr-FR');
};

// Formater une date et heure
const formatDateTime = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Formater un mois (YYYY-MM) en texte (ex: Janvier 2023)
const formatMonth = (dateStr) => {
  if (!dateStr) return '';
  const [year, month] = dateStr.split('-');
  return new Date(year, month - 1).toLocaleDateString('fr-FR', { 
    month: 'long', 
    year: 'numeric' 
  });
};

// Formater une valeur monétaire
const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', { 
    style: 'currency', 
    currency: 'MAD',
    minimumFractionDigits: 2
  }).format(value);
};

// Obtenir la classe CSS pour le badge de statut
const getStatusBadgeClass = (status) => {
  const classes = {
    'inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium': true,
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
const validerPaiement = () => {
  datePaiement.value = new Date().toISOString().split('T')[0];
  showValidationModal.value = true;
};

// Confirmer la validation du paiement
const confirmValidation = () => {
  router.post(route('admin.salaires.valider', props.salaire.id), {
    date_paiement: datePaiement.value
  }, {
    onSuccess: () => {
      showValidationModal.value = false;
    }
  });
};

// Ouvrir la modale d'annulation
const annulerSalaire = () => {
  raisonAnnulation.value = '';
  showAnnulationModal.value = true;
};

// Confirmer l'annulation
const confirmAnnulation = () => {
  if (!raisonAnnulation.value.trim()) {
    // Afficher une erreur si la raison est vide
    return;
  }
  
  router.post(route('admin.salaires.annuler', props.salaire.id), {
    commentaires: raisonAnnulation.value
  }, {
    onSuccess: () => {
      showAnnulationModal.value = false;
    }
  });
};

// Imprimer la page
const imprimer = () => {
  window.print();
};

// Styles pour l'impression
onMounted(() => {
  // Ajouter des styles spécifiques pour l'impression
  const style = document.createElement('style');
  style.innerHTML = `
    @media print {
      body * {
        visibility: hidden;
      }
      #printable-area, #printable-area * {
        visibility: visible;
      }
      #printable-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
      .no-print {
        display: none !important;
      }
    }
  `;
  document.head.appendChild(style);
});
</script>

<style scoped>
/* Styles spécifiques pour l'impression */
@media print {
  .no-print {
    display: none !important;
  }
  
  body {
    font-size: 12px;
    line-height: 1.4;
  }
  
  .print-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
  }
  
  .print-title {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
  }
  
  .print-subtitle {
    font-size: 14px;
    color: #666;
    margin: 5px 0 0 0;
  }
  
  .print-date {
    font-size: 12px;
    color: #666;
    text-align: right;
  }
  
  .print-section {
    margin-bottom: 20px;
    page-break-inside: avoid;
  }
  
  .print-section-title {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #eee;
  }
  
  .print-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
  }
  
  .print-box {
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 10px;
  }
  
  .print-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
  }
  
  .print-label {
    font-weight: 500;
    color: #666;
  }
  
  .print-value {
    font-weight: 500;
  }
  
  .print-total {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
    font-weight: bold;
  }
  
  .print-amount {
    font-size: 16px;
    font-weight: bold;
  }
  
  .print-footer {
    margin-top: 30px;
    padding-top: 10px;
    border-top: 1px solid #eee;
    font-size: 10px;
    color: #999;
    text-align: center;
  }
}
</style>
