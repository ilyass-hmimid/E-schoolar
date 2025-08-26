<template>
  <AppLayout :title="`Détails du professeur - ${professeur.name}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          <Link :href="route('admin.professeurs.index')" class="text-blue-600 hover:text-blue-900">
            Professeurs
          </Link>
          <span class="text-gray-500"> / </span>
          {{ professeur.name }}
        </h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('admin.professeurs.edit', professeur.id)" 
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <i class="fas fa-edit mr-2"></i> Modifier
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <!-- Carte de profil -->
              <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                  <div class="p-6 text-center">
                    <div class="flex justify-center">
                      <img 
                        :src="professeur.avatar || '/images/default-avatar.png'" 
                        :alt="professeur.name"
                        class="h-32 w-32 rounded-full object-cover border-4 border-white shadow"
                      >
                    </div>
                    <h3 class="mt-4 text-xl font-medium text-gray-900">{{ professeur.name }}</h3>
                    <p class="text-sm text-gray-500">{{ professeur.email }}</p>
                    
                    <div class="mt-4">
                      <span 
                        :class="{
                          'bg-green-100 text-green-800': professeur.is_active,
                          'bg-red-100 text-red-800': !professeur.is_active
                        }" 
                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ professeur.is_active ? 'Actif' : 'Inactif' }}
                      </span>
                    </div>
                    
                    <div class="mt-6 border-t border-gray-200 pt-4">
                      <dl class="space-y-2">
                        <div v-if="professeur.phone">
                          <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                          <dd class="text-sm text-gray-900">{{ professeur.phone }}</dd>
                        </div>
                        <div v-if="professeur.date_embauche">
                          <dt class="text-sm font-medium text-gray-500">Date d'embauche</dt>
                          <dd class="text-sm text-gray-900">{{ formatDate(professeur.date_embauche) }}</dd>
                        </div>
                        <div v-if="professeur.salaire">
                          <dt class="text-sm font-medium text-gray-500">Salaire mensuel</dt>
                          <dd class="text-sm text-gray-900">{{ formatCurrency(professeur.salaire) }}</dd>
                        </div>
                      </dl>
                    </div>
                  </div>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                  <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h4>
                    <div class="space-y-4">
                      <div>
                            <div class="flex items-center justify-between">
                              <span class="text-sm font-medium text-gray-500">Matières enseignées</span>
                              <span class="text-sm font-medium text-gray-900">{{ professeur.matieres.length }}</span>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                              <div 
                                class="bg-blue-600 h-2 rounded-full" 
                                :style="{ width: Math.min(100, professeur.matieres.length * 20) + '%' }"
                              ></div>
                            </div>
                          </div>
                          
                          <div>
                            <div class="flex items-center justify-between">
                              <span class="text-sm font-medium text-gray-500">Cours ce mois</span>
                              <span class="text-sm font-medium text-gray-900">{{ stats.cours_ce_mois || 0 }}</span>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                              <div 
                                class="bg-green-600 h-2 rounded-full" 
                                :style="{ width: Math.min(100, (stats.cours_ce_mois || 0) * 10) + '%' }"
                              ></div>
                            </div>
                          </div>
                          
                          <div>
                            <div class="flex items-center justify-between">
                              <span class="text-sm font-medium text-gray-500">Taux de présence</span>
                              <span class="text-sm font-medium text-gray-900">{{ stats.taux_presence || 0 }}%</span>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                              <div 
                                class="bg-yellow-500 h-2 rounded-full" 
                                :style="{ width: (stats.taux_presence || 0) + '%' }"
                              ></div>
                            </div>
                          </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Détails et activités -->
              <div class="md:col-span-2 space-y-6">
                <!-- Onglets -->
                <div class="border-b border-gray-200">
                  <nav class="-mb-px flex space-x-8">
                    <button 
                      v-for="tab in tabs" 
                      :key="tab.name"
                      @click="currentTab = tab.name"
                      :class="[
                        currentTab === tab.name 
                          ? 'border-indigo-500 text-indigo-600' 
                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                      ]"
                    >
                      {{ tab.label }}
                      <span 
                        v-if="tab.count !== null"
                        :class="[
                          'ml-2 py-0.5 px-2 rounded-full text-xs font-medium',
                          currentTab === tab.name ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600'
                        ]"
                      >
                        {{ tab.count }}
                      </span>
                    </button>
                  </nav>
                </div>
                
                <!-- Contenu des onglets -->
                <div>
                  <!-- Onglet Matières -->
                  <div v-if="currentTab === 'matieres'" class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                      <span 
                        v-for="matiere in professeur.matieres" 
                        :key="matiere.id"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
                      >
                        {{ matiere.nom }}
                      </span>
                      <p v-if="professeur.matieres.length === 0" class="text-sm text-gray-500">
                        Aucune matière assignée
                      </p>
                    </div>
                  </div>
                  
                  <!-- Onglet Cours -->
                  <div v-else-if="currentTab === 'cours'" class="space-y-4">
                    <div v-if="cours.length > 0">
                      <div v-for="cour in cours" :key="cour.id" class="border-b border-gray-200 py-4">
                        <div class="flex justify-between items-start">
                          <div>
                            <h4 class="font-medium text-gray-900">{{ cour.matiere.nom }}</h4>
                            <p class="text-sm text-gray-500">
                              {{ formatDateTime(cour.date_debut) }} - {{ formatTime(cour.date_fin) }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                              {{ cour.description || 'Aucune description' }}
                            </p>
                          </div>
                          <span 
                            :class="{
                              'bg-green-100 text-green-800': cour.statut === 'termine',
                              'bg-yellow-100 text-yellow-800': cour.statut === 'en_cours',
                              'bg-blue-100 text-blue-800': cour.statut === 'planifie'
                            }"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          >
                            {{ getStatusLabel(cour.statut) }}
                          </span>
                        </div>
                      </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">
                      Aucun cours prévu pour le moment
                    </p>
                  </div>
                  
                  <!-- Onglet Paiements -->
                  <div v-else-if="currentTab === 'paiements'" class="space-y-4">
                    <div v-if="paiements.length > 0" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                      <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Date</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Mois</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Montant</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                              <span class="sr-only">Actions</span>
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                          <tr v-for="paiement in paiements" :key="paiement.id">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                              {{ formatDate(paiement.date_paiement) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                              {{ getMonthName(paiement.mois) }} {{ paiement.annee }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                              {{ formatCurrency(paiement.montant) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                              <span 
                                :class="{
                                  'bg-green-100 text-green-800': paiement.statut === 'paye',
                                  'bg-yellow-100 text-yellow-800': paiement.statut === 'en_attente',
                                  'bg-red-100 text-red-800': paiement.statut === 'en_retard'
                                }"
                                class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                              >
                                {{ getPaiementStatusLabel(paiement.statut) }}
                              </span>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                              <a href="#" class="text-indigo-600 hover:text-indigo-900">Voir<span class="sr-only">, {{ paiement.id }}</span></a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <p v-else class="text-sm text-gray-500">
                      Aucun paiement enregistré
                    </p>
                  </div>
                  
                  <!-- Onglet Documents -->
                  <div v-else-if="currentTab === 'documents'" class="space-y-4">
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                      <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="document in documents" :key="document.id">
                          <div class="px-4 py-4 flex items-center sm:px-6">
                            <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                              <div class="truncate">
                                <div class="flex text-sm">
                                  <p class="font-medium text-indigo-600 truncate">{{ document.nom }}</p>
                                  <p class="ml-1 flex-shrink-0 font-normal text-gray-500">
                                    ({{ formatFileSize(document.taille) }})
                                  </p>
                                </div>
                                <div class="mt-2 flex">
                                  <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1.5 h-5 w-5 text-gray-400"></i>
                                    <p>
                                      Ajouté le {{ formatDate(document.date_creation) }}
                                    </p>
                                  </div>
                                </div>
                              </div>
                              <div class="mt-4 flex-shrink-0 sm:mt-0 sm:ml-5">
                                <div class="flex -space-x-1 overflow-hidden">
                                  <a 
                                    :href="route('admin.documents.download', document.id)"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                  >
                                    <i class="fas fa-download mr-1"></i> Télécharger
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <div class="mt-4">
                      <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      >
                        <i class="fas fa-plus mr-2"></i> Ajouter un document
                      </button>
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
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  professeur: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({
      cours_ce_mois: 0,
      taux_presence: 0
    })
  },
  cours: {
    type: Array,
    default: () => []
  },
  paiements: {
    type: Array,
    default: () => []
  },
  documents: {
    type: Array,
    default: () => []
  }
});

const currentTab = ref('matieres');

const tabs = [
  { name: 'matieres', label: 'Matières', count: props.professeur.matieres.length },
  { name: 'cours', label: 'Cours', count: props.cours.length },
  { name: 'paiements', label: 'Paiements', count: props.paiements.length },
  { name: 'documents', label: 'Documents', count: props.documents.length },
];

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
}

function formatDateTime(dateTimeString) {
  if (!dateTimeString) return 'N/A';
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return new Date(dateTimeString).toLocaleDateString('fr-FR', options);
}

function formatTime(timeString) {
  if (!timeString) return '';
  return new Date(timeString).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

function formatCurrency(amount) {
  if (amount === null || amount === undefined) return 'N/A';
  return new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(amount);
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
    'planifie': 'Planifié',
    'en_cours': 'En cours',
    'termine': 'Terminé',
    'annule': 'Annulé'
  };
  return statuses[status] || status;
}

function getPaiementStatusLabel(status) {
  const statuses = {
    'paye': 'Payé',
    'en_attente': 'En attente',
    'en_retard': 'En retard'
  };
  return statuses[status] || status;
}
</script>
