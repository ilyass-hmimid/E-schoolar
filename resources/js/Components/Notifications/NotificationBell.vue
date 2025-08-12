<template>
  <div class="relative">
    <!-- Bouton de notification -->
    <button 
      @click="toggleNotifications"
      class="p-2 rounded-full text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 relative"
      aria-label="Notifications"
    >
      <!-- Icône de cloche avec badge de notification -->
      <svg 
        class="h-6 w-6" 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="2" 
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
        />
      </svg>
      
      <!-- Badge de notification non lues -->
      <span 
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"
      ></span>
    </button>
    
    <!-- Panneau des notifications -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div 
        v-if="isOpen" 
        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-200 focus:outline-none z-50"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="options-menu"
      >
        <!-- En-tête du panneau -->
        <div class="px-4 py-3 flex items-center justify-between border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
          <div class="flex items-center space-x-2">
            <button 
              @click="markAllAsRead"
              :disabled="unreadCount === 0"
              :class="{'text-gray-400': unreadCount === 0, 'text-indigo-600 hover:text-indigo-900': unreadCount > 0}"
              class="text-sm font-medium focus:outline-none"
              :title="unreadCount > 0 ? 'Marquer tout comme lu' : 'Tout est lu'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
              </svg>
            </button>
            <button 
              @click="openPreferences"
              class="text-gray-400 hover:text-gray-500 focus:outline-none"
              title="Préférences de notification"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
              </svg>
            </button>
            <button 
              @click="clearAllNotifications"
              :disabled="notifications.length === 0"
              :class="{'text-gray-400': notifications.length === 0, 'text-red-500 hover:text-red-700': notifications.length > 0}"
              class="focus:outline-none"
              title="Effacer toutes les notifications"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Liste des notifications -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="loading" class="p-4 text-center">
            <svg class="animate-spin h-5 w-5 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
          
          <div v-else-if="notifications.length === 0" class="p-4 text-center text-gray-500">
            Aucune notification
          </div>
          
          <div v-else>
            <div 
              v-for="notification in notifications" 
              :key="notification.id"
              @click="handleNotificationClick(notification)"
              :class="{
                'bg-indigo-50': !notification.read_at, 
                'hover:bg-gray-50 cursor-pointer': true
              }"
              class="px-4 py-3 border-b border-gray-200"
            >
              <div class="flex items-start">
                <!-- Icône de la notification -->
                <div 
                  :class="getNotificationIconClass(notification)"
                  class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-white"
                >
                  <component :is="getNotificationIcon(notification)" class="h-4 w-4" />
                </div>
                
                <!-- Contenu de la notification -->
                <div class="ml-3 flex-1">
                  <p class="text-sm font-medium text-gray-900">
                    {{ notification.data.title || 'Nouvelle notification' }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    {{ notification.data.message || 'Vous avez une nouvelle notification' }}
                  </p>
                  <div class="mt-1 text-xs text-gray-400">
                    {{ formatDate(notification.created_at) }}
                  </div>
                </div>
                
                <!-- Indicateur de non lu -->
                <div v-if="!notification.read_at" class="ml-2">
                  <span class="h-2 w-2 rounded-full bg-indigo-600 inline-block"></span>
                </div>
              </div>
            </div>
            
            <!-- Bouton pour charger plus de notifications -->
            <div v-if="hasMorePages" class="p-2 text-center border-t border-gray-200">
              <button 
                @click.stop="loadMore"
                class="text-sm text-indigo-600 hover:text-indigo-900 font-medium focus:outline-none"
                :disabled="loadingMore"
              >
                <span v-if="loadingMore">Chargement...</span>
                <span v-else>Charger plus de notifications</span>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Pied de page avec lien vers toutes les notifications -->
        <div class="px-4 py-2 bg-gray-50 text-right">
          <inertia-link 
            href="#" 
            class="text-sm font-medium text-indigo-600 hover:text-indigo-900 focus:outline-none"
          >
            Voir toutes les notifications
          </inertia-link>
        </div>
      </div>
    </transition>
    
    <!-- Overlay pour fermer en cliquant à l'extérieur -->
    <div 
      v-if="isOpen" 
      @click="closeNotifications"
      class="fixed inset-0 z-40"
    ></div>
    
    <!-- Modale des préférences -->
    <notification-preferences-modal 
      v-if="showPreferences" 
      @close="showPreferences = false" 
    />
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head, Link } from '@inertiajs/inertia-vue3';
import NotificationPreferencesModal from '@/Components/Modals/NotificationPreferencesModal.vue';

// Icônes pour les différents types de notifications
const BellIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
      <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
    </svg>
  `
};

const CheckCircleIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
    </svg>
  `
};

const ExclamationIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
    </svg>
  `
};

export default {
  components: {
    InertiaLink: Link,
    NotificationPreferencesModal
  },
  
  setup() {
    const isOpen = ref(false);
    const showPreferences = ref(false);
    const notifications = ref([]);
    const unreadCount = ref(0);
    const loading = ref(true);
    const loadingMore = ref(false);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const hasMorePages = ref(false);
    
    // Charger les notifications au montage du composant
    onMounted(() => {
      fetchNotifications();
      // Écouter les événements de notification en temps réel (si vous utilisez Echo ou similaire)
      // window.Echo.private(`App.Models.User.${user.id}`)
      //   .notification((notification) => {
      //     notifications.value.unshift(notification);
      //     unreadCount.value++;
      //   });
    });
    
    // Nettoyer les écouteurs d'événements lors du démontage
    onUnmounted(() => {
      // window.Echo.leave(`App.Models.User.${user.id}`);
    });
    
    // Fermer les notifications en cliquant à l'extérieur
    const handleClickOutside = (event) => {
      if (isOpen.value && !event.target.closest('.relative')) {
        closeNotifications();
      }
    };
    
    // Ajouter l'écouteur d'événement pour les clics à l'extérieur
    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
    });
    
    // Supprimer l'écouteur d'événement lors du démontage
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
    });
    
    // Récupérer les notifications depuis l'API
    const fetchNotifications = async (page = 1) => {
      try {
        if (page === 1) {
          loading.value = true;
        } else {
          loadingMore.value = true;
        }
        
        const response = await axios.get('/api/notifications', {
          params: { page }
        });
        
        if (page === 1) {
          notifications.value = response.data.data;
        } else {
          notifications.value = [...notifications.value, ...response.data.data];
        }
        
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        hasMorePages.value = response.data.current_page < response.data.last_page;
        
        // Mettre à jour le nombre de notifications non lues
        updateUnreadCount();
      } catch (error) {
        console.error('Erreur lors du chargement des notifications:', error);
      } finally {
        loading.value = false;
        loadingMore.value = false;
      }
    };
    
    // Mettre à jour le compteur de notifications non lues
    const updateUnreadCount = async () => {
      try {
        const response = await axios.get('/api/notifications/unread/count');
        unreadCount.value = response.data.count;
      } catch (error) {
        console.error('Erreur lors de la récupération du nombre de notifications non lues:', error);
      }
    };
    
    // Basculer l'affichage des notifications
    const toggleNotifications = () => {
      isOpen.value = !isOpen.value;
      if (isOpen.value && notifications.value.length === 0) {
        fetchNotifications();
      }
    };
    
    // Fermer le panneau des notifications
    const closeNotifications = () => {
      isOpen.value = false;
    };
    
    // Ouvrir les préférences de notification
    const openPreferences = () => {
      showPreferences.value = true;
      closeNotifications();
    };
    
    // Marquer une notification comme lue
    const markAsRead = async (notificationId) => {
      try {
        await axios.post(`/api/notifications/${notificationId}/read`);
        
        // Mettre à jour l'état local
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification && !notification.read_at) {
          notification.read_at = new Date().toISOString();
          unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
      } catch (error) {
        console.error('Erreur lors du marquage de la notification comme lue:', error);
      }
    };
    
    // Marquer toutes les notifications comme lues
    const markAllAsRead = async () => {
      try {
        await axios.post('/api/notifications/read-all');
        
        // Mettre à jour l'état local
        notifications.value.forEach(notification => {
          if (!notification.read_at) {
            notification.read_at = new Date().toISOString();
          }
        });
        
        unreadCount.value = 0;
      } catch (error) {
        console.error('Erreur lors du marquage de toutes les notifications comme lues:', error);
      }
    };
    
    // Supprimer toutes les notifications
    const clearAllNotifications = async () => {
      if (confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications ?')) {
        try {
          await axios.delete('/api/notifications');
          notifications.value = [];
          unreadCount.value = 0;
        } catch (error) {
          console.error('Erreur lors de la suppression des notifications:', error);
        }
      }
    };
    
    // Gérer le clic sur une notification
    const handleNotificationClick = (notification) => {
      // Marquer comme lue si ce n'est pas déjà fait
      if (!notification.read_at) {
        markAsRead(notification.id);
      }
      
      // Fermer le panneau des notifications
      closeNotifications();
      
      // Rediriger vers l'URL de la notification si elle existe
      if (notification.data.action_url) {
        Inertia.visit(notification.data.action_url);
      }
    };
    
    // Charger plus de notifications
    const loadMore = () => {
      if (currentPage.value < lastPage.value) {
        fetchNotifications(currentPage.value + 1);
      }
    };
    
    // Obtenir l'icône en fonction du type de notification
    const getNotificationIcon = (notification) => {
      const type = notification.data?.type || 'info';
      
      switch (type) {
        case 'success':
          return CheckCircleIcon;
        case 'warning':
        case 'error':
          return ExclamationIcon;
        default:
          return BellIcon;
      }
    };
    
    // Obtenir la classe CSS pour l'icône de notification
    const getNotificationIconClass = (notification) => {
      const type = notification.data?.type || 'info';
      
      switch (type) {
        case 'success':
          return 'bg-green-500';
        case 'warning':
          return 'bg-yellow-500';
        case 'error':
          return 'bg-red-500';
        case 'info':
        default:
          return 'bg-indigo-500';
      }
    };
    
    // Formater la date de la notification
    const formatDate = (dateString) => {
      const date = new Date(dateString);
      const now = new Date();
      const diffInSeconds = Math.floor((now - date) / 1000);
      
      if (diffInSeconds < 60) {
        return 'À l\'instant';
      }
      
      const diffInMinutes = Math.floor(diffInSeconds / 60);
      if (diffInMinutes < 60) {
        return `Il y a ${diffInMinutes} min`;
      }
      
      const diffInHours = Math.floor(diffInMinutes / 60);
      if (diffInHours < 24) {
        return `Il y a ${diffInHours} h`;
      }
      
      const diffInDays = Math.floor(diffInHours / 24);
      if (diffInDays === 1) {
        return 'Hier';
      }
      
      if (diffInDays < 7) {
        return `Il y a ${diffInDays} j`;
      }
      
      // Pour les dates plus anciennes, afficher la date complète
      return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };
    
    return {
      isOpen,
      showPreferences,
      notifications,
      unreadCount,
      loading,
      loadingMore,
      hasMorePages,
      toggleNotifications,
      closeNotifications,
      openPreferences,
      markAsRead,
      markAllAsRead,
      clearAllNotifications,
      handleNotificationClick,
      loadMore,
      getNotificationIcon,
      getNotificationIconClass,
      formatDate
    };
  }
};
</script>
