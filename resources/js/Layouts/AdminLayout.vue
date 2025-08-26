<template>
  <div class="admin-layout">
    <!-- Sidebar -->
    <AdminSidebar :is-open="sidebarOpen" @close="closeSidebar" />
    
    <!-- Main Content -->
    <div class="main-content">
      <!-- Navigation -->
      <nav class="navbar">
        <div class="navbar-container">
          <div class="navbar-header">
            <!-- Bouton menu pour mobile -->
            <button @click="toggleSidebar" class="menu-button">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            
            <!-- Titre de la page -->
            <h2 class="page-title">
              <slot name="title">Tableau de bord</slot>
            </h2>
          </div>
          
          <!-- Menu utilisateur -->
          <div class="user-menu">
            <button @click="userMenuOpen = !userMenuOpen" class="user-button">
              <div class="user-avatar">
                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
              </div>
              <span class="user-name">{{ $page.props.auth.user.name }}</span>
              <svg class="user-dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <!-- Dropdown menu -->
            <div v-show="userMenuOpen" @click.away="userMenuOpen = false" class="dropdown-menu">
              <div class="dropdown-menu-content">
                <Link :href="route('profile.edit')" class="dropdown-item">
                  Mon profil
                </Link>
                <Link :href="route('logout')" method="post" as="button" class="dropdown-item">
                  Déconnexion
                </Link>
              </div>
            </div>
          </div>
        </div>
      </nav>
      
      <!-- Contenu principal -->
      <main class="main-content">
        <div class="main-container">
          <slot></slot>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/Sidebar/AdminSidebar.vue';
import '@/../css/components/admin-layout.css';

export default {
  components: {
    Link,
    AdminSidebar
  },
  setup() {
    const sidebarOpen = ref(false);
    const userMenuOpen = ref(false);
    const page = usePage();

    const currentRoute = () => {
      return route().current();
    };

    const toggleSidebar = () => {
      sidebarOpen.value = !sidebarOpen.value;
    };

    const closeSidebar = () => {
      if (window.innerWidth < 1024) {
        sidebarOpen.value = false;
      }
    };

    // Gestion du redimensionnement de la fenêtre
    const handleResize = () => {
      if (window.innerWidth >= 1024) {
        sidebarOpen.value = true;
      } else {
        sidebarOpen.value = false;
      }
    };

    onMounted(() => {
      // Initialiser l'état du sidebar en fonction de la largeur de l'écran
      if (window.innerWidth >= 1024) {
        sidebarOpen.value = true;
      }
      
      // Ajouter l'écouteur d'événement de redimensionnement
      window.addEventListener('resize', handleResize);
    });

    onUnmounted(() => {
      // Nettoyer l'écouteur d'événement
      window.removeEventListener('resize', handleResize);
    });

    return {
      sidebarOpen,
      userMenuOpen,
      currentRoute,
      toggleSidebar,
      closeSidebar
    };
  }
};
</script>

<style>
/* Styles spécifiques au layout admin */
.main-content {
  transition: margin-left 0.3s ease;
  width: 100%;
}

.sidebar-open .main-content {
  margin-left: 16rem;
}

@media (max-width: 1023px) {
  .main-content {
    margin-left: 0;
  }
}
</style>
