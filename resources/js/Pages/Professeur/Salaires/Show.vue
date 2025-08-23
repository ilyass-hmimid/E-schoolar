<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h1 class="text-3xl font-bold text-gray-900">Fiche de paie</h1>
            <p class="mt-2 text-gray-600">Détails de votre fiche de paie pour {{ salaire.periode }}</p>
          </div>
          <div class="mt-4 flex md:mt-0 md:ml-4">
            <button
              type="button"
              @click="imprimerFichePaie"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
              </svg>
              Imprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div id="printable" class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Récapitulatif de paie - {{ salaire.periode }}
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Détails de votre rémunération
              </p>
            </div>
            <div class="no-print">
              <span :class="{
                'bg-green-100 text-green-800': salaire.statut === 'payé',
                'bg-yellow-100 text-yellow-800': salaire.statut === 'en attente',
                'bg-red-100 text-red-800': salaire.statut === 'annulé'
              }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ salaire.statut }}
              </span>
            </div>
          </div>
        </div>
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Date de paiement</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ salaire.date_paiement }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Période de paie</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ salaire.periode }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Référence</dt>
                  <dd class="mt-1 text-sm text-gray-900">#{{ salaire.reference || 'N/A' }}</dd>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <h4 class="text-lg font-medium text-gray-900 mb-4">Détails de la rémunération</h4>
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Libellé</th>
                      <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Montant (DH)</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <!-- Gains -->
                    <tr>
                      <td colspan="2" class="px-3 py-2 bg-gray-50">
                        <span class="text-sm font-medium text-gray-900">Gains</span>
                      </td>
                    </tr>
                    <tr v-for="(gain, index) in salaire.gains" :key="'gain-'+index" class="bg-white">
                      <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500 pl-6">{{ gain.libelle }}</td>
                      <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500 text-right">{{ formatMontant(gain.montant) }}</td>
                    </tr>

                    <!-- Retenues -->
                    <tr v-if="salaire.retenues && salaire.retenues.length > 0">
                      <td colspan="2" class="px-3 py-2 bg-gray-50 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-900">Retenues</span>
                      </td>
                    </tr>
                    <tr v-for="(retenue, index) in salaire.retenues" :key="'retenue-'+index" class="bg-white">
                      <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500 pl-6">{{ retenue.libelle }}</td>
                      <td class="whitespace-nowrap px-3 py-3 text-sm text-red-600 text-right">-{{ formatMontant(retenue.montant) }}</td>
                    </tr>

                    <!-- Total -->
                    <tr class="border-t-2 border-gray-200 font-medium">
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">Salaire net à payer</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 text-right">{{ formatMontant(salaire.montant_net) }} DH</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div v-if="salaire.notes" class="mt-6">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Notes</h4>
              <div class="bg-gray-50 p-4 rounded-md">
                <p class="text-sm text-gray-600">{{ salaire.notes }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="px-4 py-4 sm:px-6 border-t border-gray-200 bg-gray-50 no-print">
          <div class="flex justify-end">
            <Link
              :href="route('professeur.salaires.index')"
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Retour à la liste
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
  salaire: {
    type: Object,
    required: true,
    default: () => ({
      id: null,
      reference: '',
      periode: '',
      date_paiement: '',
      statut: 'en attente',
      montant_brut: 0,
      montant_net: 0,
      gains: [],
      retenues: [],
      notes: ''
    })
  }
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

const formatMontant = (montant) => {
  return new Intl.NumberFormat('fr-MA', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(montant);
};

const imprimerFichePaie = () => {
  window.print();
};

// Formatage des données au chargement du composant
onMounted(() => {
  if (props.salaire.date_paiement && typeof props.salaire.date_paiement === 'string') {
    props.salaire.date_paiement = formatDate(props.salaire.date_paiement);
  }
});
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  #printable, #printable * {
    visibility: visible;
  }
  #printable {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    box-shadow: none;
  }
  .no-print {
    display: none !important;
  }
  @page {
    size: A4;
    margin: 1cm;
  }
}
</style>
