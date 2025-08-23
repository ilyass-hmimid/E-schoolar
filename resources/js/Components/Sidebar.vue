<template>
  <!-- Sidebar pour Admin -->
  <AdminSidebar v-if="userRole === 'admin'" :isOpen="isOpen" @close="closeSidebar" />
  
  <!-- Sidebar pour Professeur -->
  <ProfesseurSidebar v-else-if="userRole === 'professeur'" :isOpen="isOpen" @close="closeSidebar" />
  
  <!-- Sidebar pour Assistant -->
  <AssistantSidebar v-else-if="userRole === 'assistant'" :isOpen="isOpen" @close="closeSidebar" />
  
  <!-- Sidebar pour Élève -->
  <EleveSidebar v-else-if="userRole === 'eleve'" :isOpen="isOpen" @close="closeSidebar" />
  
  <!-- Sidebar par défaut (si le rôle n'est pas reconnu) -->
  <div v-else class="sidebar-wrapper">
    <div class="sidebar" :class="{ 'sidebar-closed': !isOpen }">
      <div v-if="isOpen" class="sidebar-overlay" @click="closeSidebar"></div>
      <div class="sidebar-header">
        <Link href="/dashboard" class="sidebar-logo">
          <div class="logo-icon">
            <svg fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
          </div>
          <span class="logo-text">Allo Tawjih</span>
        </Link>
        <button @click="closeSidebar" class="close-btn">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="sidebar-content">
        <p>Menu de navigation non disponible pour votre rôle.</p>
      </div>
      
      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-avatar">
            <span>{{ userInitials }}</span>
          </div>
          <div class="user-details">
            <div class="user-name">{{ user.name }}</div>
            <div class="user-role">{{ getRoleLabel(userRole) }}</div>
          </div>
        </div>
        <Link href="/logout" method="post" as="button" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          <span>Déconnexion</span>
        </Link>
      </div>
    </div>
    
    <div v-if="isOpen" @click="closeSidebar" class="sidebar-overlay"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AdminSidebar from './Sidebar/AdminSidebar.vue';
import ProfesseurSidebar from './Sidebar/ProfesseurSidebar.vue';
import AssistantSidebar from './Sidebar/AssistantSidebar.vue';
import EleveSidebar from './Sidebar/EleveSidebar.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  }
});

const emit = defineEmits(['close']);

const page = usePage();
const user = computed(() => page.props.auth.user);
const userRole = computed(() => user.value?.role || 'guest');
const userInitials = computed(() => {
  if (!user.value?.name) return '??';
  return user.value.name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .substring(0, 2);
});

const closeSidebar = () => {
  emit('close');
};

const getRoleLabel = (role) => {
  // Gérer à la fois les rôles numériques et les chaînes de caractères
  const roles = {
    '1': 'Administrateur',
    'admin': 'Administrateur',
    '2': 'Professeur',
    'professeur': 'Professeur',
    '3': 'Assistant',
    'assistant': 'Assistant',
    '4': 'Élève',
    'eleve': 'Élève',
    '5': 'Parent',
    'parent': 'Parent',
  };
  
  // Si le rôle est un nombre, le convertir en chaîne pour la correspondance
  const roleKey = typeof role === 'number' ? role.toString() : role;
  return roles[roleKey] || role || 'Invité';
};
</script>

<style scoped>
.sidebar-wrapper {
  position: relative;
  height: 100%;
}

.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 260px;
  height: 100%;
  background-color: #ffffff;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  z-index: 50;
  transition: transform 0.3s ease;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.sidebar-closed {
  transform: translateX(-100%);
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 40;
  display: none;
}

.sidebar-header {
  padding: 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #1e40af;
  color: white;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  color: white;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.25rem;
}

.logo-icon {
  width: 32px;
  height: 32px;
  margin-right: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-text {
  color: white;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0.25rem;
  display: none;
}

.sidebar-content {
  padding: 1rem;
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: #6b7280;
}

@media (max-width: 1024px) {
  .sidebar-overlay {
    display: block;
  }
  
  .sidebar {
    transform: translateX(-100%);
  }
  
  .sidebar:not(.sidebar-closed) {
    transform: translateX(0);
  }
  
  .close-btn {
    display: block;
  }
}

.user-info {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  padding: 0.5rem;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 0.375rem;
}

.user-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 9999px;
  background-color: #1e40af;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-right: 0.75rem;
  flex-shrink: 0;
}

.user-details {
  flex: 1;
  min-width: 0;
}

.user-name {
  font-weight: 600;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 0.9rem;
}

.user-role {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.8);
}

.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.6rem 1rem;
  background-color: #e53e3e;
  color: white;
  border: none;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  font-size: 0.9rem;
}

.logout-btn:hover {
  background-color: #c53030;
}

.logout-btn i {
  margin-right: 0.5rem;
  font-size: 0.9rem;
}
</style>
