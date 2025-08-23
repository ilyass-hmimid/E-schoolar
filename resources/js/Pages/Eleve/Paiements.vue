<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Mes Paiements
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Résumé des paiements -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Total Payé</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(totalPaye) }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">À Payer</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(aPayer) }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Factures</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ totalFactures }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Prochain Paiement</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(prochainPaiement) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <select class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="paye">Payé</option>
                <option value="en-attente">En attente</option>
                <option value="en-retard">En retard</option>
              </select>
              <select class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Toutes les matières</option>
                <option value="mathematiques">Mathématiques</option>
                <option value="physique">Physique</option>
                <option value="francais">Français</option>
              </select>
              <input 
                type="date" 
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Date de début"
              >
              <input 
                type="date" 
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Date de fin"
              >
            </div>
          </div>
        </div>

        <!-- Liste des paiements -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Historique des Paiements</h3>
          </div>
          <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Matière
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Montant
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Méthode
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="paiement in paiements" :key="paiement.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(paiement.date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                          <span class="text-xs font-medium text-gray-700">
                            {{ paiement.matiere.charAt(0) }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                          {{ paiement.matiere }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ paiement.periode }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                    {{ formatCurrency(paiement.montant) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(paiement.statut)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ paiement.statut }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ paiement.methode || 'Non spécifiée' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button v-if="paiement.statut === 'En attente'" class="text-blue-600 hover:text-blue-900 mr-3">
                      Payer
                    </button>
                    <button class="text-indigo-600 hover:text-indigo-900 mr-3">
                      Facture
                    </button>
                    <button class="text-green-600 hover:text-green-900">
                      Détails
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Graphique des paiements -->
        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Évolution des Paiements</h3>
          </div>
          <div class="p-6">
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
              <p class="text-gray-500">Graphique d'évolution des paiements</p>
            </div>
          </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Paiement Rapide</h3>
              <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p class="font-medium text-gray-900">Mathématiques - Janvier</p>
                    <p class="text-sm text-gray-500">Échéance: 31/01/2024</p>
                  </div>
                  <div class="text-right">
                    <p class="font-semibold text-gray-900">150 €</p>
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                      Payer maintenant
                    </button>
                  </div>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p class="font-medium text-gray-900">Physique - Janvier</p>
                    <p class="text-sm text-gray-500">Échéance: 31/01/2024</p>
                  </div>
                  <div class="text-right">
                    <p class="font-semibold text-gray-900">120 €</p>
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                      Payer maintenant
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
              <div class="space-y-4">
                <div class="p-3 bg-blue-50 rounded-lg">
                  <p class="text-sm text-blue-800">
                    <strong>Rappel:</strong> Les paiements sont dus le dernier jour de chaque mois.
                  </p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                  <p class="text-sm text-green-800">
                    <strong>Bon à savoir:</strong> Paiement en plusieurs fois possible sur demande.
                  </p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                  <p class="text-sm text-yellow-800">
                    <strong>Contact:</strong> Pour toute question, contactez le secrétariat.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Données simulées
const paiements = ref([
  {
    id: 1,
    date: '2024-01-15',
    matiere: 'Mathématiques',
    periode: 'Janvier 2024',
    montant: 150,
    statut: 'Payé',
    methode: 'Carte bancaire'
  },
  {
    id: 2,
    date: '2024-01-10',
    matiere: 'Physique',
    periode: 'Janvier 2024',
    montant: 120,
    statut: 'En attente',
    methode: null
  },
  {
    id: 3,
    date: '2023-12-28',
    matiere: 'Français',
    periode: 'Décembre 2023',
    montant: 100,
    statut: 'Payé',
    methode: 'Espèces'
  },
  {
    id: 4,
    date: '2023-12-15',
    matiere: 'Mathématiques',
    periode: 'Décembre 2023',
    montant: 150,
    statut: 'Payé',
    methode: 'Virement'
  },
  {
    id: 5,
    date: '2023-11-30',
    matiere: 'Physique',
    periode: 'Novembre 2023',
    montant: 120,
    statut: 'En retard',
    methode: null
  }
]);

const totalPaye = computed(() => 
  paiements.value
    .filter(p => p.statut === 'Payé')
    .reduce((sum, p) => sum + p.montant, 0)
);

const aPayer = computed(() => 
  paiements.value
    .filter(p => p.statut === 'En attente' || p.statut === 'En retard')
    .reduce((sum, p) => sum + p.montant, 0)
);

const totalFactures = computed(() => paiements.value.length);

const prochainPaiement = computed(() => {
  const enAttente = paiements.value.filter(p => p.statut === 'En attente');
  return enAttente.length > 0 ? enAttente[0].montant : 0;
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR');
};

const getStatusClass = (status) => {
  switch (status) {
    case 'Payé':
      return 'bg-green-100 text-green-800';
    case 'En attente':
      return 'bg-yellow-100 text-yellow-800';
    case 'En retard':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>
