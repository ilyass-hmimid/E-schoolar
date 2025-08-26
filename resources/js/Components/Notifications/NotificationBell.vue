<template>
  <div class="relative">
    <!-- Bouton de la cloche -->
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19C4.74 3.63 5.5 3.3 6.3 3.3h11.4c.8 0 1.56.33 2.11.89L20.7 6.3c.56.55.89 1.31.89 2.11v11.4c0 .8-.33 1.56-.89 2.11L19.7 22.7c-.55.56-1.31.89-2.11.89H6.3c-.8 0-1.56-.33-2.11-.89L3.3 19.7c-.56-.55-.89-1.31-.89-2.11V8.4c0-.8.33-1.56.89-2.11L4.19 4.19z" />
      </svg>
      
      <!-- Badge de notifications non lues -->
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown des notifications -->
    <div
      v-if="showDropdown"
      class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
    >
      <div class="py-2">
        <!-- Header -->
        <div class="px-4 py-2 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
            <div class="flex items-center space-x-2">
              <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="text-xs text-blue-600 hover:text-blue-900"
              >
                Tout marquer comme lu
              </button>
              <button
                @click="goToNotifications"
                class="text-xs text-gray-600 hover:text-gray-900"
              >
                Voir tout
              </button>
            </div>
          </div>
        </div>

        <!-- Liste des notifications -->
        <div class="max-h-96 overflow-y-auto">
          <div
            v-if="notifications.length === 0"
            class="px-4 py-8 text-center text-gray-500"
          >
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19C4.74 3.63 5.5 3.3 6.3 3.3h11.4c.8 0 1.56.33 2.11.89L20.7 6.3c.56.55.89 1.31.89 2.11v11.4c0 .8-.33 1.56-.89 2.11L19.7 22.7c-.55.56-1.31.89-2.11.89H6.3c-.8 0-1.56-.33-2.11-.89L3.3 19.7c-.56-.55-.89-1.31-.89-2.11V8.4c0-.8.33-1.56.89-2.11L4.19 4.19z" />
            </svg>
            <p class="mt-2 text-sm">Aucune notification</p>
          </div>

          <div
            v-for="notification in notifications"
            :key="notification.id"
            :class="[
              'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0',
              notification.read_at ? 'bg-white' : 'bg-blue-50'
            ]"
            @click="handleNotificationClick(notification)"
          >
            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="getNotificationIconClass(notification.data.type)">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="notification.data.type === 'absence'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-else-if="notification.data.type === 'payment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19C4.74 3.63 5.5 3.3 6.3 3.3h11.4c.8 0 1.56.33 2.11.89L20.7 6.3c.56.55.89 1.31.89 2.11v11.4c0 .8-.33 1.56-.89 2.11L19.7 22.7c-.55.56-1.31.89-2.11.89H6.3c-.8 0-1.56-.33-2.11-.89L3.3 19.7c-.56-.55-.89-1.31-.89-2.11V8.4c0-.8.33-1.56.89-2.11L4.19 4.19z" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2">
                  <h4 class="text-sm font-medium text-gray-900 truncate">
                    {{ notification.data.title || 'Notification' }}
                  </h4>
                  <span v-if="!notification.read_at" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Nouveau
                  </span>
                </div>
                <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                  {{ notification.data.message }}
                </p>
                <div class="flex items-center justify-between mt-2">
                  <span class="text-xs text-gray-500">
                    {{ formatTimeAgo(notification.created_at) }}
                  </span>
                  <button
                    v-if="!notification.read_at"
                    @click.stop="markAsRead(notification.id)"
                    class="text-xs text-blue-600 hover:text-blue-900"
                  >
                    Marquer comme lu
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="notifications.length > 0" class="px-4 py-2 border-t border-gray-200">
          <button
            @click="goToNotifications"
            class="w-full text-center text-sm text-blue-600 hover:text-blue-900"
          >
            Voir toutes les notifications
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  initialUnreadCount: {
    type: Number,
    default: 0
  }
});

const showDropdown = ref(false);
const unreadCount = ref(props.initialUnreadCount);
const notifications = ref([]);

// Fermer le dropdown quand on clique à l'extérieur
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showDropdown.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  loadNotifications();
  
  // Polling pour les nouvelles notifications (toutes les 30 secondes)
  const interval = setInterval(loadNotifications, 30000);
  
  onUnmounted(() => {
    clearInterval(interval);
  });
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
  if (showDropdown.value) {
    loadNotifications();
  }
};

const loadNotifications = async () => {
  try {
    const response = await fetch('/notifications?limit=5');
    const data = await response.json();
    
    if (data.notifications) {
      notifications.value = data.notifications.data || [];
      unreadCount.value = data.unreadCount || 0;
    }
  } catch (error) {
    console.error('Erreur lors du chargement des notifications:', error);
  }
};

const markAsRead = async (id) => {
  try {
    await fetch(`/notifications/${id}/mark-read`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    });
    
    // Mettre à jour localement
    const notification = notifications.value.find(n => n.id === id);
    if (notification) {
      notification.read_at = new Date().toISOString();
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
};

const markAllAsRead = async () => {
  try {
    await fetch('/notifications/mark-all-read', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    });
    
    // Mettre à jour localement
    notifications.value.forEach(notification => {
      notification.read_at = new Date().toISOString();
    });
    unreadCount.value = 0;
  } catch (error) {
    console.error('Erreur:', error);
  }
};

const handleNotificationClick = (notification) => {
  // Marquer comme lu si pas encore lu
  if (!notification.read_at) {
    markAsRead(notification.id);
  }
  
  // Navigation selon le type de notification
  if (notification.data.type === 'absence' && notification.data.absence_id) {
    router.visit(`/absences/${notification.data.absence_id}`);
  } else if (notification.data.type === 'payment' && notification.data.payment_id) {
    router.visit(`/paiements/${notification.data.payment_id}`);
  } else {
    // Navigation par défaut vers la page des notifications
    goToNotifications();
  }
  
  showDropdown.value = false;
};

const goToNotifications = () => {
  router.visit('/notifications');
  showDropdown.value = false;
};

const getNotificationIconClass = (type) => {
  const classes = {
    'paiement_retard': 'bg-red-500',
    'paiement_effectue': 'bg-green-500',
    'default': 'bg-blue-500'
  };
  
  return classes[type] || classes.default;
};

const getNotificationTitle = (notification) => {
  switch (notification.type) {
    case 'paiement_retard':
      return `Retard de paiement - ${notification.data.eleve_nom}`;
    case 'paiement_effectue':
      return `Paiement reçu - ${notification.data.eleve_nom}`;
    default:
      return 'Notification';
  }
};

const formatTimeAgo = (dateString) => {
  const now = new Date();
  const date = new Date(dateString);
  const diffInSeconds = Math.floor((now - date) / 1000);
  
  if (diffInSeconds < 60) {
    return 'À l\'instant';
  } else if (diffInSeconds < 3600) {
    const minutes = Math.floor(diffInSeconds / 60);
    return `Il y a ${minutes} min`;
  } else if (diffInSeconds < 86400) {
    const hours = Math.floor(diffInSeconds / 3600);
    return `Il y a ${hours}h`;
  } else {
    const days = Math.floor(diffInSeconds / 86400);
    return `Il y a ${days}j`;
  }
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
