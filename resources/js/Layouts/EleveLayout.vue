<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Barre de navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('eleve.dashboard')">
                <img class="h-8 w-auto" src="/images/logo.svg" alt="Logo" />
              </Link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <!-- Navigation principale -->
              <Link 
                :href="route('eleve.dashboard')" 
                :class="[route().current('eleve.dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium']"
              >
                Tableau de bord
              </Link>
              <Link 
                :href="route('eleve.emploi-du-temps')" 
                :class="[route().current('eleve.emploi-du-temps') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium']"
              >
                Emploi du temps
              </Link>
              <Link 
                :href="route('eleve.notes.index')" 
                :class="[route().current('eleve.notes.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium']"
              >
                Notes
              </Link>
              <Link 
                :href="route('eleve.absences.index')" 
                :class="[route().current('eleve.absences.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium']"
              >
                Absences
              </Link>
              <Link 
                :href="route('eleve.paiements.index')" 
                :class="[route().current('eleve.paiements.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium']"
              >
                Paiements
              </Link>
            </div>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <!-- Menu déroulant notifications -->
            <div class="ml-3 relative">
              <button 
                @click="showNotifications = !showNotifications"
                class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <span class="sr-only">Voir les notifications</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
              </button>
              <!-- Menu déroulant des notifications -->
              <div 
                v-show="showNotifications" 
                class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="user-menu-button"
                tabindex="-1"
              >
                <div class="py-1" role="none">
                  <div class="px-4 py-2 border-b border-gray-200">
                    <p class="text-sm font-medium text-gray-900">Notifications</p>
                  </div>
                  <div v-if="notifications.length > 0" class="max-h-96 overflow-y-auto">
                    <a 
                      v-for="(notification, index) in notifications" 
                      :key="index"
                      href="#" 
                      class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem"
                      tabindex="-1"
                    >
                      <div class="flex">
                        <div class="flex-shrink-0">
                          <span 
                            class="h-2 w-2 rounded-full"
                            :class="{
                              'bg-green-500': !notification.read_at,
                              'bg-transparent': notification.read_at
                            }"
                            aria-hidden="true"
                          ></span>
                        </div>
                        <div class="ml-3">
                          <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                          <p class="text-sm text-gray-500">{{ notification.message }}</p>
                          <p class="text-xs text-gray-400 mt-1">{{ formatDate(notification.created_at) }}</p>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div v-else class="px-4 py-3 text-sm text-gray-500 text-center">
                    Aucune notification
                  </div>
                  <div class="px-4 py-2 border-t border-gray-200 text-center">
                    <Link 
                      :href="route('eleve.notifications.index')" 
                      class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                      Voir toutes les notifications
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Menu déroulant profil -->
            <div class="ml-3 relative">
              <div>
                <button 
                  @click="showProfileMenu = !showProfileMenu"
                  type="button" 
                  class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                  id="user-menu-button" 
                  aria-expanded="false" 
                  aria-haspopup="true"
                >
                  <span class="sr-only">Ouvrir le menu utilisateur</span>
                  <img 
                    class="h-8 w-8 rounded-full" 
                    :src="$page.props.user.profile_photo_url" 
                    :alt="$page.props.user.name"
                  >
                </button>
              </div>

              <!-- Menu déroulant -->
              <div 
                v-show="showProfileMenu" 
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="user-menu-button"
                tabindex="-1"
              >
                <div class="py-1" role="none">
                  <div class="px-4 py-2">
                    <p class="text-sm text-gray-700">Connecté en tant que</p>
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $page.props.user.name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $page.props.user.email }}</p>
                  </div>
                  <Link 
                    :href="route('eleve.profil.edit')" 
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem"
                    tabindex="-1"
                  >
                    Mon profil
                  </Link>
                  <Link 
                    :href="route('eleve.parametres')" 
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem"
                    tabindex="-1"
                  >
                    Paramètres
                  </Link>
                  <form @submit.prevent="logout">
                    <button 
                      type="submit"
                      class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem"
                      tabindex="-1"
                    >
                      Déconnexion
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Bouton menu mobile -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button 
              @click="mobileMenuOpen = !mobileMenuOpen"
              type="button" 
              class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
              aria-controls="mobile-menu" 
              :aria-expanded="mobileMenuOpen"
            >
              <span class="sr-only">Ouvrir le menu principal</span>
              <svg 
                class="block h-6 w-6" 
                :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen}" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor" 
                aria-hidden="true"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg 
                class="hidden h-6 w-6" 
                :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor" 
                aria-hidden="true"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Menu mobile -->
      <div v-show="mobileMenuOpen" class="sm:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
          <Link 
            :href="route('eleve.dashboard')" 
            :class="[route().current('eleve.dashboard') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800', 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium']"
            @click="mobileMenuOpen = false"
          >
            Tableau de bord
          </Link>
          <Link 
            :href="route('eleve.emploi-du-temps')" 
            :class="[route().current('eleve.emploi-du-temps') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800', 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium']"
            @click="mobileMenuOpen = false"
          >
            Emploi du temps
          </Link>
          <Link 
            :href="route('eleve.notes.index')" 
            :class="[route().current('eleve.notes.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800', 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium']"
            @click="mobileMenuOpen = false"
          >
            Notes
          </Link>
          <Link 
            :href="route('eleve.absences.index')" 
            :class="[route().current('eleve.absences.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800', 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium']"
            @click="mobileMenuOpen = false"
          >
            Absences
          </Link>
          <Link 
            :href="route('eleve.paiements.index')" 
            :class="[route().current('eleve.paiements.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800', 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium']"
            @click="mobileMenuOpen = false"
          >
            Paiements
          </Link>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <img 
                class="h-10 w-10 rounded-full" 
                :src="$page.props.user.profile_photo_url" 
                :alt="$page.props.user.name"
              >
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-gray-800">{{ $page.props.user.name }}</div>
              <div class="text-sm font-medium text-gray-500">{{ $page.props.user.email }}</div>
            </div>
            <button class="ml-auto bg-white flex-shrink-0 p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <span class="sr-only">Voir les notifications</span>
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </button>
          </div>
          <div class="mt-3 space-y-1">
            <Link 
              :href="route('eleve.profil.edit')" 
              class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
              @click="mobileMenuOpen = false"
            >
              Mon profil
            </Link>
            <Link 
              :href="route('eleve.parametres')" 
              class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
              @click="mobileMenuOpen = false"
            >
              Paramètres
            </Link>
            <form @submit.prevent="logout">
              <button 
                type="submit"
                class="w-full text-left block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
                @click="mobileMenuOpen = false"
              >
                Déconnexion
              </button>
            </form>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <div class="py-6">
      <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <!-- Titre de la page -->
          <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
              <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                <slot name="header"></slot>
              </h2>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
              <slot name="actions"></slot>
            </div>
          </div>
          
          <!-- Messages flash -->
          <div v-if="$page.props.flash.success || $page.props.flash.error || $page.props.flash.warning" class="mb-6">
            <div 
              v-if="$page.props.flash.success"
              class="rounded-md bg-green-50 p-4"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800">
                    {{ $page.props.flash.success }}
                  </p>
                </div>
              </div>
            </div>
            
            <div 
              v-else-if="$page.props.flash.error"
              class="rounded-md bg-red-50 p-4"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800">
                    {{ $page.props.flash.error }}
                  </p>
                </div>
              </div>
            </div>
            
            <div 
              v-else-if="$page.props.flash.warning"
              class="rounded-md bg-yellow-50 p-4"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-yellow-800">
                    {{ $page.props.flash.warning }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Contenu de la page -->
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <slot></slot>
            </div>
          </div>
        </div>
      </main>
    </div>
    
    <!-- Pied de page -->
    <footer class="bg-white">
      <div class="max-w-7xl mx-auto py-6 px-4 overflow-hidden sm:px-6 lg:px-8">
        <p class="mt-4 text-center text-sm text-gray-500">
          &copy; {{ new Date().getFullYear() }} Allo Tawjih. Tous droits réservés.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

// État du menu mobile
const mobileMenuOpen = ref(false);
const showProfileMenu = ref(false);
const showNotifications = ref(false);

// Données de test pour les notifications
const notifications = ref([
  {
    id: 1,
    title: 'Nouvelle note',
    message: 'Vous avez reçu une nouvelle note en Mathématiques',
    read_at: null,
    created_at: new Date()
  },
  {
    id: 2,
    title: 'Absence justifiée',
    message: 'Votre absence du 15/05/2023 a été approuvée',
    read_at: new Date(),
    created_at: new Date(Date.now() - 3600000)
  }
]);

// Formater une date
const formatDate = (date) => {
  if (!date) return '';
  return format(new Date(date), 'PPPp', { locale: fr });
};

// Gestion de la déconnexion
const logout = () => {
  router.post(route('logout'));
};

// Fermer les menus au clic en dehors
const handleClickOutside = (event) => {
  const profileMenu = document.getElementById('user-menu-button');
  const notificationsButton = document.querySelector('[aria-label="Voir les notifications"]');
  
  if (profileMenu && !profileMenu.contains(event.target)) {
    showProfileMenu.value = false;
  }
  
  if (notificationsButton && !notificationsButton.contains(event.target)) {
    showNotifications.value = false;
  }
};

// Écouter les clics en dehors des menus
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      showProfileMenu.value = false;
      showNotifications.value = false;
      mobileMenuOpen.value = false;
    }
  });
});

// Nettoyer les écouteurs d'événements
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      showProfileMenu.value = false;
      showNotifications.value = false;
      mobileMenuOpen.value = false;
    }
  });
});
</script>
