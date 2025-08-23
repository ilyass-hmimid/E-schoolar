<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="dashboard-header">
        <div class="header-content">
          <h2 class="dashboard-title">
            Tableau de bord Administrateur
          </h2>
          <p class="welcome-message">Bienvenue, {{ $page.props.auth.user.name }} ! Voici un aperçu de votre centre.</p>
        </div>
        <div class="header-actions">
          <div class="date-card">
            <p class="date-label">Aujourd'hui</p>
            <p class="date-value">{{ new Date().toLocaleDateString('fr-FR') }}</p>
          </div>
        </div>
      </div>
    </template>

    <div class="dashboard-content">
      <!-- Statistiques principales avec animations -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-inner">
            <div class="stat-info">
              <p class="stat-label">Total Utilisateurs</p>
              <p class="stat-value">{{ stats.totalUsers }}</p>
              <div class="stat-details">
                <span class="stat-detail">{{ stats.professeurs }} profs</span>
                <span class="stat-detail">{{ stats.assistants }} assistants</span>
              </div>
            </div>
            <div class="stat-icon blue">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card-inner">
            <div class="stat-info">
              <p class="stat-label">Total Élèves</p>
              <p class="stat-value">{{ stats.eleves }}</p>
              <div class="stat-details">
                <span class="stat-detail">{{ stats.parents }} parents</span>
                <span class="stat-detail">{{ stats.absencesMois }} absences</span>
              </div>
            </div>
            <div class="stat-icon green">
              <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card-inner">
            <div class="stat-info">
              <p class="stat-label">Cours Actifs</p>
              <p class="stat-value">{{ stats.coursActifs }}</p>
              <div class="stat-details">
                <span class="stat-detail">{{ stats.professeurs }} profs</span>
                <span class="stat-detail">{{ stats.eleves }} élèves</span>
              </div>
            </div>
            <div class="stat-icon yellow">
              <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card-inner">
            <div class="stat-info">
              <p class="stat-label">Revenus du Mois</p>
              <p class="stat-value">{{ formatCurrency(stats.revenusMois) }}</p>
              <div class="stat-details">
                <span class="stat-detail">{{ derniersPaiements.length }} transactions</span>
                <span class="stat-detail">Moyenne: {{ formatCurrency(stats.revenusMois / (derniersPaiements.length || 1)) }}</span>
              </div>
            </div>
            <div class="stat-icon purple">
              <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Graphiques avec design moderne -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Graphique des revenus -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Évolution des Revenus</h3>
            <div class="flex items-center space-x-2">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span class="text-sm text-gray-600">Revenus mensuels</span>
            </div>
          </div>
          <div class="h-80 flex items-center justify-center">
            <template v-if="revenusChartData.labels.length === 0">
              <div class="text-center text-gray-500">
                <p>Aucune donnée de revenus disponible</p>
                <p class="text-sm mt-2">Les données s'afficheront automatiquement</p>
              </div>
            </template>
            <template v-else>
              <LineChart 
                :data="revenusChartData" 
                :options="chartOptions"
                class="w-full h-full"
              />
            </template>
          </div>
        </div>

        <!-- Graphique des utilisateurs par rôle -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Répartition des Utilisateurs</h3>
            <div class="text-sm text-gray-600">{{ stats.totalUsers }} utilisateurs</div>
          </div>
          <DoughnutChart 
            :data="repartitionUtilisateursData" 
            :options="doughnutOptions"
            class="h-80"
          />
        </div>
      </div>

      <!-- Tableaux récents avec design moderne -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Derniers paiements -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
          <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-gray-900">Derniers Paiements</h3>
              <Link href="/paiements" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Voir tout →
              </Link>
            </div>
          </div>
          <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <tr v-for="paiement in derniersPaiements" :key="paiement.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                        <span class="text-xs font-bold text-white">
                          {{ paiement.etudiant?.name?.charAt(0).toUpperCase() || 'N' }}
                        </span>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ paiement.etudiant?.name || 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ paiement.etudiant?.email || 'N/A' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    {{ formatCurrency(paiement.montant) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(paiement.date_paiement) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(paiement.statut)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ paiement.statut }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Dernières absences -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
          <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-gray-900">Dernières Absences</h3>
              <Link href="/absences" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Voir tout →
              </Link>
            </div>
          </div>
          <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <tr v-for="absence in dernieresAbsences" :key="absence.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mr-3">
                        <span class="text-xs font-bold text-white">
                          {{ absence.etudiant?.name?.charAt(0).toUpperCase() || 'N' }}
                        </span>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ absence.etudiant?.name || 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ absence.etudiant?.email || 'N/A' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ absence.matiere?.nom || 'N/A' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(absence.date_absence) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getAbsenceTypeClass(absence.type)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ absence.type }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import LineChart from '@/Components/Charts/LineChart.vue'
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue'

const props = defineProps({
  stats: {
    type: Object,
    required: true,
    default: () => ({
      totalUsers: 0,
      eleves: 0,
      professeurs: 0,
      assistants: 0,
      parents: 0,
      revenusMois: 0,
      coursActifs: 0,
      absencesMois: 0
    })
  },
  derniersPaiements: {
    type: Array,
    default: () => []
  },
  dernieresAbsences: {
    type: Array,
    default: () => []
  },
  revenusParMois: {
    type: Array,
    default: () => ([])
  }
});

// Format the revenue data for the chart
const revenusChartData = computed(() => {
  // Ensure we have data
  if (!props.revenusParMois || props.revenusParMois.length === 0) {
    return {
      labels: [],
      datasets: [{
        label: 'Aucune donnée disponible',
        data: [],
        borderColor: 'rgba(200, 200, 200, 0.5)',
        backgroundColor: 'rgba(200, 200, 200, 0.1)',
        borderDash: [5, 5],
        tension: 0.4
      }]
    };
  }
  
  return {
    labels: props.revenusParMois.map(item => item.mois),
    datasets: [{
      label: 'Revenus (DH)',
      data: props.revenusParMois.map(item => item.montant || 0),
      borderColor: 'rgb(59, 130, 246)',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      borderWidth: 2,
      tension: 0.4,
      fill: true
    }]
  };
});

// Format the user distribution data for the doughnut chart
const repartitionUtilisateursData = computed(() => ({
  labels: ['Élèves', 'Professeurs', 'Assistants', 'Parents'],
  datasets: [
    {
      data: [
        props.stats?.eleves || 0,
        props.stats?.professeurs || 0,
        props.stats?.assistants || 0,
        props.stats?.parents || 0
      ],
      backgroundColor: [
        'rgba(99, 102, 241, 0.8)',
        'rgba(249, 115, 22, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(139, 92, 246, 0.8)'
      ],
      borderColor: [
        'rgba(99, 102, 241, 1)',
        'rgba(249, 115, 22, 1)',
        'rgba(16, 185, 129, 1)',
        'rgba(139, 92, 246, 1)'
      ],
      borderWidth: 1
    }
  ]
}));

// Chart options configuration
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `${context.dataset.label || ''}: ${formatCurrency(context.raw || 0)}`;
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.05)'
      },
      ticks: {
        callback: function(value) {
          return formatCurrency(value);
        }
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  },
  elements: {
    point: {
      radius: 4,
      hoverRadius: 6,
      hoverBorderWidth: 2,
      hoverBackgroundColor: 'white'
    },
    line: {
      borderWidth: 2
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
};
const formatCurrency = (amount) => {
  if (isNaN(amount)) return '0,00 DH'
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount) + ' DH'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const getStatusClass = (status) => {
  if (!status) return 'bg-gray-100 text-gray-800'
  
  switch (status.toLowerCase()) {
    case 'payé':
    case 'paye':
      return 'bg-green-100 text-green-800'
    case 'en_attente':
    case 'en attente':
      return 'bg-yellow-100 text-yellow-800'
    case 'annulé':
    case 'annule':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getAbsenceTypeClass = (type) => {
  if (!type) return 'bg-gray-100 text-gray-800'
  
  switch (type.toLowerCase()) {
    case 'justifiée':
    case 'justifiee':
      return 'bg-blue-100 text-blue-800'
    case 'non_justifiée':
    case 'non justifiee':
    case 'non_justifiee':
      return 'bg-red-100 text-red-800'
    case 'retard':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}
</script>
