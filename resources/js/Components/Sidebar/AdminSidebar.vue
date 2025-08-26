<template>
  <aside class="sidebar" :class="{ 'open': isOpen }">
    <!-- En-tête -->
    <div class="sidebar-header">
      <h1 class="sidebar-logo">Allo Tawjih</h1>
      <button @click="$emit('close')" class="sidebar-close-btn lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    
    <!-- Navigation -->
    <nav class="sidebar-nav">
      <!-- Tableau de bord -->
      <div class="nav-section">
        <Link 
          :href="route('admin.dashboard')" 
          class="nav-link"
          :class="{ 'active': currentRoute() === 'admin.dashboard' }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Tableau de bord
        </Link>
      </div>
      
      <!-- Gestion des utilisateurs -->
      <div class="nav-section">
        <h3 class="nav-title">Gestion des utilisateurs</h3>
        <ul>
          <li>
            <Link 
              :href="route('admin.eleves.index')" 
              class="nav-link"
              :class="{ 'active': currentRoute().startsWith('admin.eleves') }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Élèves
            </Link>
          </li>
          <li>
            <Link 
              :href="route('admin.professeurs.index')" 
              class="nav-link"
              :class="{ 'active': currentRoute().startsWith('admin.professeurs') }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Professeurs
            </Link>
          </li>
          <li>
            <Link 
              :href="route('admin.users.index')" 
              class="nav-link"
              :class="{ 'active': currentRoute().startsWith('admin.users') }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Utilisateurs
            </Link>
          </li>
        </ul>
      </div>
      
      <!-- Gestion pédagogique -->
      <div class="nav-section">
        <h3 class="nav-title">Gestion pédagogique</h3>
        <ul>
          <li>
            <Link 
              :href="route('admin.niveaux.index')" 
              class="nav-link"
              :class="{ 'active': currentRoute().startsWith('admin.niveaux') }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Niveaux
            </Link>
          </li>
          <li>
            <Link 
              :href="route('admin.filieres.index')" 
              class="nav-link"
              :class="{ 'active': currentRoute().startsWith('admin.filieres') }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Filières
            </Link>
          </li>
          <li>
            <Link 
              :href="route('admin.matieres.index')" 
              class="nav-link"
              :class="{ 'active': currentRoute().startsWith('admin.matieres') }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Matières
            </Link>
          </li>
        </ul>
      </div>
    </nav>
    
    <!-- Pied de page -->
    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar">
          {{ $page.props.auth.user ? $page.props.auth.user.name.charAt(0).toUpperCase() : 'U' }}
        </div>
        <div class="user-info">
          <p class="user-name">
            {{ $page.props.auth.user ? $page.props.auth.user.name : 'Utilisateur' }}
          </p>
          <p class="user-role">
            {{ $page.props.auth.user ? $page.props.auth.user.role : 'Rôle' }}
          </p>
        </div>
        <Link 
          :href="route('logout')" 
          method="post" 
          as="button" 
          class="logout-btn"
          title="Déconnexion"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </Link>
      </div>
    </div>
  </aside>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  name: 'AdminSidebar',
  
  components: {
    Link
  },
  
  props: {
    isOpen: {
      type: Boolean,
      default: true
    }
  },
  
  setup() {
    const currentRoute = () => {
      return route().current();
    };
    
    return {
      currentRoute
    };
  }
};
</script>
