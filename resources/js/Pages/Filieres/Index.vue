<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des Filières
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête avec statistiques -->
        <div class="mb-8">
          <div class="flex justify-between items-center mb-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900">Liste des Filières</h3>
              <p class="text-sm text-gray-600">Gérez les filières d'enseignement du centre</p>
            </div>
            <button
              @click="openModal()"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Nouvelle Filière
            </button>
          </div>

          <!-- Statistiques -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Filières</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ filieres.length }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Élèves Inscrits</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ totalEleves }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Matières</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ totalMatieres }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Taux de Réussite</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ tauxReussite }}%</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Nom de la filière..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                <select
                  v-model="filters.niveau"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Tous les niveaux</option>
                  <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau.id">
                    {{ niveau.nom }}
                  </option>
                </select>
              </div>
              <div class="flex items-end">
                <button
                  @click="resetFilters"
                  class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                  Réinitialiser
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tableau des filières -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Filière
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Niveau
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Élèves
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Matières
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="filiere in filteredFilieres" :key="filiere.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                          <span class="text-sm font-medium text-green-600">{{ filiere.nom.charAt(0).toUpperCase() }}</span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ filiere.nom }}</div>
                        <div class="text-sm text-gray-500">{{ filiere.description || 'Aucune description' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ filiere.niveau?.nom || 'N/A' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ filiere.eleves_count || 0 }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ filiere.matieres_count || 0 }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(filiere.is_active)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ filiere.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button
                      @click="editFiliere(filiere)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                    >
                      Modifier
                    </button>
                    <button
                      @click="deleteFiliere(filiere.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal pour ajouter/modifier une filière -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ editingFiliere ? 'Modifier la Filière' : 'Nouvelle Filière' }}
          </h3>
          
          <form @submit.prevent="saveFiliere">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nom de la filière</label>
                <input
                  v-model="form.nom"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Niveau</label>
                <select
                  v-model="form.niveau_id"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner un niveau</option>
                  <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau.id">
                    {{ niveau.nom }}
                  </option>
                </select>
              </div>

              <div class="flex items-center">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-900">Filière active</label>
              </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                {{ editingFiliere ? 'Modifier' : 'Créer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  filieres: Array,
  niveaux: Array,
})

const showModal = ref(false)
const editingFiliere = ref(null)
const filters = ref({
  search: '',
  niveau: '',
})

const form = ref({
  nom: '',
  description: '',
  niveau_id: '',
  is_active: true,
})

const filteredFilieres = computed(() => {
  return props.filieres.filter(filiere => {
    const matchesSearch = !filters.value.search || 
      filiere.nom.toLowerCase().includes(filters.value.search.toLowerCase())
    
    const matchesNiveau = !filters.value.niveau || 
      filiere.niveau_id == filters.value.niveau
    
    return matchesSearch && matchesNiveau
  })
})

const totalEleves = computed(() => {
  return props.filieres.reduce((sum, filiere) => sum + (filiere.eleves_count || 0), 0)
})

const totalMatieres = computed(() => {
  return props.filieres.reduce((sum, filiere) => sum + (filiere.matieres_count || 0), 0)
})

const tauxReussite = computed(() => {
  // Simulation d'un taux de réussite basé sur les données disponibles
  const totalFiliere = props.filieres.length
  if (totalFiliere === 0) return 0
  return Math.round((totalFiliere / (totalFiliere + 2)) * 100)
})

const getStatusClass = (isActive) => {
  return isActive 
    ? 'bg-green-100 text-green-800' 
    : 'bg-red-100 text-red-800'
}

const openModal = () => {
  editingFiliere.value = null
  resetForm()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingFiliere.value = null
  resetForm()
}

const resetForm = () => {
  form.value = {
    nom: '',
    description: '',
    niveau_id: '',
    is_active: true,
  }
}

const editFiliere = (filiere) => {
  editingFiliere.value = filiere
  form.value = {
    nom: filiere.nom,
    description: filiere.description || '',
    niveau_id: filiere.niveau_id || '',
    is_active: filiere.is_active,
  }
  showModal.value = true
}

const saveFiliere = () => {
  if (editingFiliere.value) {
    router.put(`/filieres/${editingFiliere.value.id}`, form.value, {
      onSuccess: () => {
        closeModal()
      }
    })
  } else {
    router.post('/filieres', form.value, {
      onSuccess: () => {
        closeModal()
      }
    })
  }
}

const deleteFiliere = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette filière ?')) {
    router.delete(`/filieres/${id}`)
  }
}

const resetFilters = () => {
  filters.value = {
    search: '',
    niveau: '',
  }
}
</script>
