<template>
  <!-- Sidebar pour Admin -->
  <AdminSidebar 
    v-if="['1', 'admin', 'administrateur'].includes(userRole)" 
    :isOpen="isOpen" 
    @close="closeSidebar" 
  />
  
  <!-- Sidebar pour Professeur -->
  <ProfesseurSidebar 
    v-else-if="['2', 'professeur', 'prof', 'teacher'].includes(userRole)" 
    :isOpen="isOpen" 
    @close="closeSidebar" 
  />
  
  <!-- Sidebar pour Assistant -->
  <AssistantSidebar 
    v-else-if="['3', 'assistant', 'assist'].includes(userRole)" 
    :isOpen="isOpen" 
    @close="closeSidebar" 
  />
  
  <!-- Sidebar pour Élève -->
  <EleveSidebar 
    v-else-if="['4', 'eleve', 'etudiant', 'student'].includes(userRole)" 
    :isOpen="isOpen" 
    @close="closeSidebar" 
  />
  
  <!-- Sidebar par défaut (si le rôle n'est pas reconnu) -->
  <div v-else class="fixed inset-0 flex z-40 lg:hidden" v-show="isOpen">
    <div class="fixed inset-0">
      <div class="absolute inset-0 bg-gray-600 opacity-75" @click="closeSidebar"></div>
    </div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
      <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
        <div class="flex-shrink-0 flex items-center px-4">
          <Link href="/" class="text-xl font-bold text-gray-900">
            Allo Tawjih
          </Link>
          <button 
            @click="closeSidebar" 
            class="ml-auto -mx-1.5 -mr-5 p-1.5 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <span class="sr-only">Fermer le menu</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <nav class="mt-5 px-2 space-y-1">
          <p class="px-3 text-sm font-medium text-gray-500 uppercase tracking-wider">
            Menu non disponible pour votre rôle
          </p>
        </nav>
      </div>
      <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
              {{ userInitials }}
            </div>
          </div>
          <div class="ml-3">
            <p class="text-base font-medium text-gray-700 group-hover:text-gray-900">
              {{ user.name }}
            </p>
            <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">
              {{ getRoleLabel(userRole) }}
            </p>
          </div>
          <div class="ml-4 flex-shrink-0">
            <Link 
              href="/logout" 
              method="post" 
              as="button"
              class="text-sm font-medium text-gray-500 hover:text-gray-700"
            >
              Déconnexion
            </Link>
          </div>
        </div>
      </div>
    </div>
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

// Récupération du rôle utilisateur depuis le contexte d'authentification
const userRole = computed(() => {
  if (!user.value?.role) return 'guest';
  return String(user.value.role).toLowerCase();
});

// Fermeture du sidebar
const closeSidebar = () => {
  emit('close');
};

// Obtention du libellé du rôle
const getRoleLabel = (role) => {
  const roles = {
    // Admin
    '1': 'Administrateur',
    'admin': 'Administrateur',
    'administrateur': 'Administrateur',
    
    // Professeur
    '2': 'Professeur',
    'professeur': 'Professeur',
    'prof': 'Professeur',
    'teacher': 'Professeur',
    
    // Assistant
    '3': 'Assistant',
    'assistant': 'Assistant',
    'assist': 'Assistant',
    
    // Élève
    '4': 'Élève',
    'eleve': 'Élève',
    'etudiant': 'Élève',
    'student': 'Élève'
  };
  
  return roles[role] || 'Utilisateur';
};

// Journalisation pour débogage
console.log('Rôle utilisateur détecté :', {
  role: user.value?.role,
  role_label: user.value?.role_label,
  role_value: user.value?.role_value,
  normalizedRole: userRole.value,
  user: user.value
});

const userInitials = computed(() => {
  if (!user.value?.name) return '??';
  return user.value.name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .substring(0, 2);
});
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
