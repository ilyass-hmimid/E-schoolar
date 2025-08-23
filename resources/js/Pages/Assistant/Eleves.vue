<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des Élèves
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Liste des Élèves</h3>
              <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Nouvel Élève
              </button>
            </div>

            <!-- Filtres -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <input 
                type="text" 
                placeholder="Rechercher un élève..." 
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <select class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les niveaux</option>
                <option value="6eme">6ème</option>
                <option value="5eme">5ème</option>
                <option value="4eme">4ème</option>
                <option value="3eme">3ème</option>
                <option value="2nde">2nde</option>
                <option value="1ere">1ère</option>
                <option value="terminale">Terminale</option>
              </select>
              <select class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Toutes les filières</option>
                <option value="scientifique">Scientifique</option>
                <option value="litteraire">Littéraire</option>
                <option value="economique">Économique</option>
              </select>
              <select class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="actif">Actif</option>
                <option value="inactif">Inactif</option>
              </select>
            </div>

            <!-- Tableau des élèves -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Élève
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Niveau
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Filière
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Matières inscrites
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
                  <tr v-for="eleve in eleves" :key="eleve.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                              {{ eleve.nom.charAt(0) }}
                            </span>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ eleve.nom }} {{ eleve.prenom }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ eleve.email }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ eleve.niveau }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ eleve.filiere }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ eleve.matieres.length }} matière(s)
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getStatusClass(eleve.statut)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ eleve.statut }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</button>
                      <button class="text-blue-600 hover:text-blue-900 mr-3">Modifier</button>
                      <button class="text-red-600 hover:text-red-900">Supprimer</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Affichage de 1 à 10 sur 25 résultats
              </div>
              <div class="flex space-x-2">
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                  Précédent
                </button>
                <button class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm">
                  1
                </button>
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                  2
                </button>
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                  3
                </button>
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                  Suivant
                </button>
              </div>
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
const eleves = ref([
  {
    id: 1,
    nom: 'Dupont',
    prenom: 'Jean',
    email: 'jean.dupont@email.com',
    niveau: 'Terminale',
    filiere: 'Scientifique',
    matieres: ['Mathématiques', 'Physique', 'Chimie'],
    statut: 'Actif'
  },
  {
    id: 2,
    nom: 'Martin',
    prenom: 'Marie',
    email: 'marie.martin@email.com',
    niveau: '1ère',
    filiere: 'Littéraire',
    matieres: ['Français', 'Histoire', 'Philosophie'],
    statut: 'Actif'
  },
  {
    id: 3,
    nom: 'Bernard',
    prenom: 'Pierre',
    email: 'pierre.bernard@email.com',
    niveau: '2nde',
    filiere: 'Économique',
    matieres: ['Mathématiques', 'SES'],
    statut: 'Inactif'
  }
]);

const getStatusClass = (status) => {
  switch (status) {
    case 'Actif':
      return 'bg-green-100 text-green-800';
    case 'Inactif':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>
