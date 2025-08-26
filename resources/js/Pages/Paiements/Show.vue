<template>
  <AppLayout :title="`Paiement #${paiement.id}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          <Link :href="route('paiements.index')" class="text-blue-600 hover:text-blue-900">
            Paiements
          </Link>
          <span class="text-gray-500"> / </span>
          Paiement #{{ paiement.id }}
        </h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('paiements.edit', paiement.id)" 
            v-if="$page.props.auth.user.isAdmin"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <i class="fas fa-edit mr-2"></i> Modifier
          </Link>
          <button 
            @click="printPaiement"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <i class="fas fa-print mr-2"></i> Imprimer
          </button>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <!-- Carte récapitulative -->
              <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                  <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-medium text-gray-900">Récapitulatif</h3>
                      <span 
                        :class="{
                          'bg-green-100 text-green-800': paiement.statut === 'paye',
                          'bg-yellow-100 text-yellow-800': paiement.statut === 'en_attente',
                          'bg-red-100 text-red-800': paiement.statut === 'en_retard',
                          'bg-gray-100 text-gray-800': paiement.statut === 'annule'
                        }" 
                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ getStatusLabel(paiement.statut) }}
                      </span>
                    </div>
                    
                    <div class="mt-6 space-y-4">
                      <div>
                        <dt class="text-sm font-medium text-gray-500">Montant</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                          {{ formatCurrency(paiement.montant) }}
                        </dd>
                      </div>
                      
                      <div>
                        <dt class="text-sm font-medium text-gray-500">Période</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                          {{ getMonthName(paiement.mois) }} {{ paiement.annee }}
                        </dd>
                      </div>
                      
                      <div>
                        <dt class="text-sm font-medium text-gray-500">Date de paiement</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                          {{ formatDate(paiement.date_paiement) }}
                        </dd>
                      </div>
                      
                      <div>
                        <dt class="text-sm font-medium text-gray-500">Mode de paiement</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                          {{ getModePaiementLabel(paiement.mode_paiement) }}
                        </dd>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Détails et documents -->
              <div class="md:col-span-2 space-y-6">
                <!-- Informations de l'élève -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                  <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Informations de l'élève
                    </h3>
                  </div>
                  <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                      <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ paiement.eleve.name }}
                        </dd>
                      </div>
                      <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Classe</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ paiement.eleve.classe?.nom || 'Non affecté' }}
                        </dd>
                      </div>
                      <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ paiement.eleve.phone || 'Non renseigné' }}
                        </dd>
                      </div>
                    </dl>
                  </div>
                </div>
                
                <!-- Documents associés -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Documents associés
                    </h3>
                  </div>
                  <div class="px-4 py-5 sm:p-6">
                    <div v-if="paiement.documents && paiement.documents.length > 0" class="space-y-3">
                      <div 
                        v-for="document in paiement.documents" 
                        :key="document.id"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-md hover:bg-gray-100"
                      >
                        <div class="flex items-center">
                          <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                          <span class="text-sm text-gray-700">{{ document.nom }}</span>
                        </div>
                        <a 
                          :href="route('documents.download', document.id)" 
                          class="text-indigo-600 hover:text-indigo-900"
                          title="Télécharger"
                        >
                          <i class="fas fa-download"></i>
                        </a>
                      </div>
                    </div>
                    <div v-else class="text-center py-8 text-sm text-gray-500">
                      Aucun document associé à ce paiement
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  paiement: {
    type: Object,
    required: true
  }
});

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
}

function formatCurrency(amount) {
  if (amount === null || amount === undefined) return 'N/A';
  return new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(amount);
}

function getMonthName(monthNumber) {
  const months = [
    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
  ];
  return months[monthNumber - 1] || '';
}

function getStatusLabel(status) {
  const statuses = {
    'paye': 'Payé',
    'en_attente': 'En attente',
    'en_retard': 'En retard',
    'annule': 'Annulé'
  };
  return statuses[status] || status;
}

function getModePaiementLabel(mode) {
  const modes = {
    'especes': 'Espèces',
    'cheque': 'Chèque',
    'virement': 'Virement bancaire',
    'carte': 'Carte bancaire',
    'autre': 'Autre'
  };
  return modes[mode] || mode;
}

function printPaiement() {
  // Implémentation de l'impression
  window.print();
}
</script>
