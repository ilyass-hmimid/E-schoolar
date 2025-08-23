<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des Inscriptions
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Liste des Inscriptions</h3>
              <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Nouvelle Inscription
              </button>
            </div>

            <!-- Tableau des inscriptions -->
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
                      Niveau
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date d'inscription
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
                  <tr v-for="inscription in inscriptions" :key="inscription.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                              {{ inscription.eleve.nom.charAt(0) }}
                            </span>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ inscription.eleve.nom }} {{ inscription.eleve.prenom }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ inscription.eleve.email }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ inscription.matiere.nom }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ inscription.niveau.nom }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(inscription.date_inscription) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getStatusClass(inscription.statut)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ inscription.statut }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</button>
                      <button class="text-red-600 hover:text-red-900">Supprimer</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Données simulées
const inscriptions = ref([
  {
    id: 1,
    eleve: { nom: 'Dupont', prenom: 'Jean', email: 'jean.dupont@email.com' },
    matiere: { nom: 'Mathématiques' },
    niveau: { nom: 'Terminale' },
    date_inscription: '2024-01-15',
    statut: 'Actif'
  },
  {
    id: 2,
    eleve: { nom: 'Martin', prenom: 'Marie', email: 'marie.martin@email.com' },
    matiere: { nom: 'Physique' },
    niveau: { nom: '1ère' },
    date_inscription: '2024-01-10',
    statut: 'En attente'
  }
]);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR');
};

const getStatusClass = (status) => {
  switch (status) {
    case 'Actif':
      return 'bg-green-100 text-green-800';
    case 'En attente':
      return 'bg-yellow-100 text-yellow-800';
    case 'Inactif':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>
