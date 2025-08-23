<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des Enseignements
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête avec statistiques -->
        <div class="mb-8">
          <div class="flex justify-between items-center mb-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900">Liste des Enseignements</h3>
              <p class="text-sm text-gray-600">Gérez les cours et enseignements du centre</p>
            </div>
            <button
              @click="openModal()"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Nouvel Enseignement
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Cours</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ enseignements.length }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Professeurs</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ totalProfesseurs }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Revenus Mensuels</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(revenusMensuels) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Matière, professeur ou étudiant..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Matière</label>
                <select
                  v-model="filters.matiere"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Toutes les matières</option>
                  <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                    {{ matiere.nom }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select
                  v-model="filters.statut"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Tous les statuts</option>
                  <option value="actif">Actif</option>
                  <option value="termine">Terminé</option>
                  <option value="suspendu">Suspendu</option>
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

        <!-- Tableau des enseignements -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cours
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Professeur
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Étudiant
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Période
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Prix
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
                <tr v-for="enseignement in filteredEnseignements" :key="enseignement.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <span class="text-sm font-medium text-blue-600">{{ enseignement.matiere?.nom?.charAt(0).toUpperCase() || 'C' }}</span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ enseignement.matiere?.nom || 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ enseignement.niveau?.nom || 'N/A' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ enseignement.professeur?.name || 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ enseignement.professeur?.email || '' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ enseignement.etudiant?.name || 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ enseignement.etudiant?.email || '' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(enseignement.date_debut) }}</div>
                    <div class="text-sm text-gray-500">{{ formatDate(enseignement.date_fin) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ formatCurrency(enseignement.prix) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(enseignement.statut)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ enseignement.statut }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button
                      @click="editEnseignement(enseignement)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                    >
                      Modifier
                    </button>
                    <button
                      @click="deleteEnseignement(enseignement.id)"
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

    <!-- Modal pour ajouter/modifier un enseignement -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ editingEnseignement ? 'Modifier l\'Enseignement' : 'Nouvel Enseignement' }}
          </h3>
          
          <form @submit.prevent="saveEnseignement">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Matière</label>
                <select
                  v-model="form.matiere_id"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner une matière</option>
                  <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                    {{ matiere.nom }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Professeur</label>
                <select
                  v-model="form.professeur_id"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner un professeur</option>
                  <option v-for="prof in professeurs" :key="prof.id" :value="prof.id">
                    {{ prof.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Étudiant</label>
                <select
                  v-model="form.etudiant_id"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner un étudiant</option>
                  <option v-for="etudiant in etudiants" :key="etudiant.id" :value="etudiant.id">
                    {{ etudiant.name }}
                  </option>
                </select>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Date de début</label>
                  <input
                    v-model="form.date_debut"
                    type="date"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                  <input
                    v-model="form.date_fin"
                    type="date"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Prix (DH)</label>
                <input
                  v-model="form.prix"
                  type="number"
                  step="0.01"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Statut</label>
                <select
                  v-model="form.statut"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="actif">Actif</option>
                  <option value="termine">Terminé</option>
                  <option value="suspendu">Suspendu</option>
                </select>
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
                {{ editingEnseignement ? 'Modifier' : 'Créer' }}
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
  enseignements: Array,
  matieres: Array,
  professeurs: Array,
  etudiants: Array,
})

const showModal = ref(false)
const editingEnseignement = ref(null)
const filters = ref({
  search: '',
  matiere: '',
  statut: '',
})

const form = ref({
  matiere_id: '',
  professeur_id: '',
  etudiant_id: '',
  date_debut: '',
  date_fin: '',
  prix: '',
  statut: 'actif',
})

const filteredEnseignements = computed(() => {
  return props.enseignements.filter(enseignement => {
    const matchesSearch = !filters.value.search || 
      enseignement.matiere?.nom?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      enseignement.professeur?.name?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      enseignement.etudiant?.name?.toLowerCase().includes(filters.value.search.toLowerCase())
    
    const matchesMatiere = !filters.value.matiere || 
      enseignement.matiere_id == filters.value.matiere
    
    const matchesStatut = !filters.value.statut || 
      enseignement.statut === filters.value.statut
    
    return matchesSearch && matchesMatiere && matchesStatut
  })
})

const totalEleves = computed(() => {
  return props.enseignements.reduce((sum, enseignement) => sum + 1, 0)
})

const totalProfesseurs = computed(() => {
  const profIds = new Set(props.enseignements.map(e => e.professeur_id).filter(id => id))
  return profIds.size
})

const revenusMensuels = computed(() => {
  return props.enseignements.reduce((sum, enseignement) => sum + (enseignement.prix || 0), 0)
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-MA', {
    style: 'currency',
    currency: 'MAD'
  }).format(amount || 0)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}

const getStatusClass = (statut) => {
  switch (statut) {
    case 'actif':
      return 'bg-green-100 text-green-800'
    case 'termine':
      return 'bg-blue-100 text-blue-800'
    case 'suspendu':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const openModal = () => {
  editingEnseignement.value = null
  resetForm()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingEnseignement.value = null
  resetForm()
}

const resetForm = () => {
  form.value = {
    matiere_id: '',
    professeur_id: '',
    etudiant_id: '',
    date_debut: '',
    date_fin: '',
    prix: '',
    statut: 'actif',
  }
}

const editEnseignement = (enseignement) => {
  editingEnseignement.value = enseignement
  form.value = {
    matiere_id: enseignement.matiere_id || '',
    professeur_id: enseignement.professeur_id || '',
    etudiant_id: enseignement.etudiant_id || '',
    date_debut: enseignement.date_debut || '',
    date_fin: enseignement.date_fin || '',
    prix: enseignement.prix || '',
    statut: enseignement.statut || 'actif',
  }
  showModal.value = true
}

const saveEnseignement = () => {
  if (editingEnseignement.value) {
    router.put(`/enseignements/${editingEnseignement.value.id}`, form.value, {
      onSuccess: () => {
        closeModal()
      }
    })
  } else {
    router.post('/enseignements', form.value, {
      onSuccess: () => {
        closeModal()
      }
    })
  }
}

const deleteEnseignement = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet enseignement ?')) {
    router.delete(`/enseignements/${id}`)
  }
}

const resetFilters = () => {
  filters.value = {
    search: '',
    matiere: '',
    statut: '',
  }
}
</script>
