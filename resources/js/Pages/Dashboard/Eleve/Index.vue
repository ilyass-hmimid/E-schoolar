<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-white">Espace Élève</h1>
            <p class="mt-2 text-indigo-100">Suivi de vos notes et présences</p>
          </div>
          <div class="flex items-center space-x-4">
            <div class="text-right text-white">
              <p class="text-sm opacity-75">{{ currentDate }}</p>
              <p class="font-semibold">{{ currentTime }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Statistiques personnelles -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Montant à payer -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Montant à payer</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.somme_a_payer || '0 DH' }}</p>
            </div>
          </div>
        </div>

        <!-- Paiements du mois -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Paiements du mois</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.paiements_mois || '0 DH' }}</p>
            </div>
          </div>
        </div>

        <!-- Absences du mois -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Absences du mois</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.absences_mois || 0 }}</p>
            </div>
          </div>
        </div>

        <!-- Notes du mois -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Notes du mois</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.notes_mois || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Mes notes récentes -->
      <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Mes notes récentes</h3>
        </div>
        <div class="overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professeur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coefficient</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appréciation</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="note in notesRecentes" :key="note.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ note.matiere }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ note.professeur }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getNoteClass(note.note)" class="text-sm font-semibold">
                    {{ note.note }}/20
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ note.coefficient }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ note.type }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ note.date_evaluation }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getAppreciationClass(note.appreciation)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ note.appreciation }}
                  </span>
                </td>
              </tr>
              <tr v-if="notesRecentes.length === 0">
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                  Aucune note récente
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Mes absences récentes -->
      <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Mes absences récentes</h3>
        </div>
        <div class="overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Justifiée</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="absence in absencesRecentes" :key="absence.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ absence.matiere }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ absence.date_absence }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getAbsenceTypeClass(absence.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ absence.type }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="absence.justifiee ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ absence.justifiee ? 'Oui' : 'Non' }}
                  </span>
                </td>
              </tr>
              <tr v-if="absencesRecentes.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                  Aucune absence récente
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h4 class="text-lg font-medium text-gray-900">Voir mon bulletin</h4>
              <p class="text-sm text-gray-500">Consulter toutes mes notes</p>
            </div>
          </div>
          <div class="mt-4">
            <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
              Voir bulletin
            </button>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h4 class="text-lg font-medium text-gray-900">Mon planning</h4>
              <p class="text-sm text-gray-500">Voir mes horaires de cours</p>
            </div>
          </div>
          <div class="mt-4">
            <button class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
              Voir planning
            </button>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h4 class="text-lg font-medium text-gray-900">Contacter l'administration</h4>
              <p class="text-sm text-gray-500">Poser une question</p>
            </div>
          </div>
          <div class="mt-4">
            <button class="w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
              Contacter
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'EleveDashboard',
  props: {
    stats: {
      type: Object,
      default: () => ({})
    },
    notesRecentes: {
      type: Array,
      default: () => []
    },
    absencesRecentes: {
      type: Array,
      default: () => []
    }
  },
  setup() {
    const currentDate = ref('')
    const currentTime = ref('')
    let timeInterval = null

    const updateDateTime = () => {
      const now = new Date()
      currentDate.value = now.toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
      currentTime.value = now.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const getNoteClass = (note) => {
      if (note >= 16) return 'text-green-600'
      if (note >= 14) return 'text-blue-600'
      if (note >= 12) return 'text-yellow-600'
      if (note >= 10) return 'text-orange-600'
      return 'text-red-600'
    }

    const getAppreciationClass = (appreciation) => {
      switch (appreciation) {
        case 'Très bien':
          return 'bg-green-100 text-green-800'
        case 'Bien':
          return 'bg-blue-100 text-blue-800'
        case 'Assez bien':
          return 'bg-yellow-100 text-yellow-800'
        case 'Passable':
          return 'bg-orange-100 text-orange-800'
        default:
          return 'bg-red-100 text-red-800'
      }
    }

    const getAbsenceTypeClass = (type) => {
      return type === 'absence' 
        ? 'bg-red-100 text-red-800' 
        : 'bg-yellow-100 text-yellow-800'
    }

    onMounted(() => {
      updateDateTime()
      timeInterval = setInterval(updateDateTime, 1000)
    })

    onUnmounted(() => {
      if (timeInterval) {
        clearInterval(timeInterval)
      }
    })

    return {
      currentDate,
      currentTime,
      getNoteClass,
      getAppreciationClass,
      getAbsenceTypeClass
    }
  }
}
</script>
