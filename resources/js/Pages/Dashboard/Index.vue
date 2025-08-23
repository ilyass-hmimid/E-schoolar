<template>
  <div class="dashboard">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="dashboard-title">
        <i class="fas fa-tachometer-alt"></i>
        <span>Tableau de bord</span>
      </div>
    </header>

    <!-- Dashboard Content -->
    <main class="dashboard-content">
      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <div class="welcome-content">
          <h2>Bonjour, {{ $page.props.auth.user.name }}</h2>
          <p>Bienvenue sur votre tableau de bord</p>
        </div>
        <div class="user-info">
          <div class="user-avatar" :title="$page.props.auth.user.name">
            {{ getInitials($page.props.auth.user.name) }}
          </div>
          <div class="notification-bell">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid">
        <!-- Students Card -->
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.students || 0 }}</div>
            <div class="stat-label">Étudiants</div>
            <span 
              v-if="stats.studentsChange !== 0" 
              class="stat-change"
              :class="{ 'positive': stats.studentsChange > 0, 'negative': stats.studentsChange < 0 }"
            >
              {{ stats.studentsChange > 0 ? '+' : '' }}{{ stats.studentsChange }}%
              <i :class="stats.studentsChange > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" style="margin-left: 2px;"></i>
            </span>
          </div>
        </div>

        <!-- Teachers Card -->
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.teachers || 0 }}</div>
            <div class="stat-label">Enseignants</div>
            <span 
              v-if="stats.teachersChange !== 0" 
              class="stat-change"
              :class="{ 'positive': stats.teachersChange > 0, 'negative': stats.teachersChange < 0 }"
            >
              {{ stats.teachersChange > 0 ? '+' : '' }}{{ stats.teachersChange }}%
              <i :class="stats.teachersChange > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" style="margin-left: 2px;"></i>
            </span>
          </div>
        </div>

        <!-- Courses Card -->
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
            <i class="fas fa-book-open"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.courses || 0 }}</div>
            <div class="stat-label">Cours</div>
            <p class="stat-meta">{{ stats.activeCourses || 0 }} actifs</p>
          </div>
        </div>

        <!-- Revenue Card -->
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ formatCurrency(stats.revenue) }}</div>
            <div class="stat-label">Revenu mensuel</div>
            <span 
              v-if="stats.revenueChange !== 0" 
              class="stat-change"
              :class="{ 'positive': stats.revenueChange > 0, 'negative': stats.revenueChange < 0 }"
            >
              {{ stats.revenueChange > 0 ? '+' : '' }}{{ stats.revenueChange }}%
              <i :class="stats.revenueChange > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" style="margin-left: 2px;"></i>
            </span>
            <p class="stat-meta">vs {{ stats.previousMonth || 'période précédente' }}</p>
          </div>
        </div>
      </div>

      <!-- Upcoming Courses -->
      <div class="dashboard-section">
        <h2 class="section-title">Prochains cours</h2>
        <div class="courses-grid">
          <div v-for="course in upcomingCourses" :key="course.id" class="course-card">
            <div class="course-time">
              <div class="time">{{ course.start_time ? formatDate(course.start_time).split(' ')[1] : '' }}</div>
              <div class="duration">90 min</div>
            </div>
            <div class="course-details">
              <h3>{{ course.title || 'Cours de Mathématiques' }}</h3>
              <p class="course-teacher">
                <i class="fas fa-chalkboard-teacher"></i> {{ course.teacher_name || 'Professeur Non Défini' }}
              </p>
              <div class="course-meta">
                <span class="course-level">{{ course.level || 'Tous niveaux' }}</span>
                <span class="course-students">
                  <i class="fas fa-users"></i> {{ course.students_count || 0 }}
                </span>
              </div>
            </div>
            <div class="course-actions">
              <button class="btn-icon" title="Rejoindre la classe">
                <i class="fas fa-video"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();

const isLoading = ref(true);
const error = ref(null);

const stats = ref({
  students: 0,
  studentsChange: 0,
  teachers: 0,
  teachersChange: 0,
  courses: 0,
  coursesChange: 0,
  revenue: 0,
  revenueChange: 0
});

const upcomingCourses = ref([]);
const recentActivities = ref([]);

const userRole = computed(() => page.props.auth?.user?.role || '');

const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

const formatTimeAgo = (date) => {
  if (!date) return '';
  const seconds = Math.floor((new Date() - new Date(date)) / 1000);
  const intervals = {
    année: 31536000,
    mois: 2592000,
    semaine: 604800,
    jour: 86400,
    heure: 3600,
    minute: 60,
    seconde: 1
  };
  for (const [unit, secondsInUnit] of Object.entries(intervals)) {
    const interval = Math.floor(seconds / secondsInUnit);
    if (interval >= 1) {
      return interval === 1
        ? `il y a ${interval} ${unit}`
        : `il y a ${interval} ${unit}s`;
    }
  }
  return 'à l\'instant';
};

const formatCurrency = (amount) => {
  if (amount === null || amount === undefined) return '';
  return new Intl.NumberFormat('fr-MA', {
    style: 'currency',
    currency: 'MAD',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
};

const getStatusText = (status) => {
  const statusMap = {
    confirmed: 'Confirmé',
    pending: 'En attente',
    cancelled: 'Annulé'
  };
  return statusMap[status] || status;
};

const getInitials = (name) => {
  if (!name) return '??';
  return name
    .split(' ')
    .map(part => part[0])
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const loadDashboardData = async () => {
  try {
    isLoading.value = true;
    error.value = null;

    const statsResponse = await axios.get('/api/dashboard/stats');
    stats.value = statsResponse.data;

    const coursesResponse = await axios.get('/api/dashboard/upcoming-classes');
    upcomingCourses.value = coursesResponse.data;

    if (['admin', 'assistant'].includes(userRole.value)) {
      const activitiesResponse = await axios.get('/api/dashboard/recent-registrations');
      recentActivities.value = activitiesResponse.data;
    } else {
      recentActivities.value = [];
    }
  } catch (err) {
    console.error('Erreur lors du chargement des données:', err);
    error.value = 'Impossible de charger les données du tableau de bord. Veuillez réessayer.';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadDashboardData();
});
</script>

<style scoped>
/* Styles spécifiques au composant */
.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.btn-icon {
  background: none;
  border: none;
  color: white;
  font-size: 1.25rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.btn-icon:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.user-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
}

/* Animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
