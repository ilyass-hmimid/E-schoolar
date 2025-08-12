<template>
  <transition-group 
    name="toast"
    tag="div"
    class="fixed z-50 w-full max-w-sm p-4 space-y-4"
    :class="positionClasses"
  >
    <div
      v-for="notification in activeNotifications"
      :key="notification.id"
      class="rounded-lg shadow-lg overflow-hidden"
      :class="[notificationClass(notification), { 'cursor-pointer': notification.onClick }]"
      @click="handleClick(notification)"
    >
      <div class="p-4">
        <div class="flex items-start">
          <!-- Icône de notification -->
          <div class="flex-shrink-0">
            <component
              :is="notificationIcon(notification)"
              class="h-6 w-6"
              :class="iconClass(notification)"
              aria-hidden="true"
            />
          </div>
          
          <!-- Contenu de la notification -->
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p v-if="notification.title" class="text-sm font-medium text-gray-900">
              {{ notification.title }}
            </p>
            <p class="mt-1 text-sm text-gray-500">
              {{ notification.message }}
            </p>
            
            <!-- Actions -->
            <div v-if="notification.action || notification.dismissible" class="mt-3 flex space-x-3">
              <button
                v-if="notification.action"
                type="button"
                class="inline-flex items-center text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2"
                :class="[actionButtonClass(notification), actionFocusRingClass(notification)]"
                @click.stop="handleAction(notification)"
              >
                {{ notification.actionText || 'Voir' }}
              </button>
              
              <button
                v-if="notification.dismissible"
                type="button"
                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click.stop="close(notification)"
              >
                <span class="sr-only">Fermer</span>
                <XMarkIcon class="h-5 w-5" aria-hidden="true" />
              </button>
            </div>
          </div>
          
          <!-- Bouton de fermeture -->
          <div v-if="notification.dismissible" class="ml-4 flex-shrink-0 flex">
            <button
              class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              @click.stop="close(notification)"
            >
              <span class="sr-only">Fermer</span>
              <XMarkIcon class="h-5 w-5" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>
      
      <!-- Barre de progression -->
      <div v-if="notification.timeout > 0" class="h-1 bg-gray-200">
        <div 
          class="h-full transition-all duration-300 ease-linear"
          :class="progressBarClass(notification)"
          :style="{ width: `${notification.progress}%` }"
        ></div>
      </div>
    </div>
  </transition-group>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { XMarkIcon, CheckCircleIcon, ExclamationTriangleIcon, ExclamationCircleIcon, InformationCircleIcon, BellIcon } from '@heroicons/vue/24/outline';

export default {
  name: 'ToastNotifications',
  
  components: {
    XMarkIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    ExclamationCircleIcon,
    InformationCircleIcon,
    BellIcon,
  },
  
  props: {
    position: {
      type: String,
      default: 'top-right',
      validator: (value) => {
        return [
          'top-left', 'top-center', 'top-right',
          'bottom-left', 'bottom-center', 'bottom-right'
        ].includes(value);
      }
    },
    maxNotifications: {
      type: Number,
      default: 3
    },
    duration: {
      type: Number,
      default: 5000
    },
    animationDuration: {
      type: Number,
      default: 300
    },
    closeOnClick: {
      type: Boolean,
      default: true
    },
    pauseOnHover: {
      type: Boolean,
      default: true
    },
    pauseOnFocusLoss: {
      type: Boolean,
      default: true
    },
  },
  
  emits: ['click', 'close', 'action'],
  
  setup(props, { emit }) {
    const notifications = ref([]);
    const timers = new Map();
    
    // Classes CSS pour la position des notifications
    const positionClasses = computed(() => {
      const classes = [];
      const [vertical, horizontal] = props.position.split('-');
      
      // Position verticale
      if (vertical === 'top') {
        classes.push('top-0');
      } else {
        classes.push('bottom-0');
      }
      
      // Position horizontale
      if (horizontal === 'left') {
        classes.push('left-0');
      } else if (horizontal === 'center') {
        classes.push('left-1/2 transform -translate-x-1/2');
      } else {
        classes.push('right-0');
      }
      
      // Espacement
      classes.push(vertical === 'top' ? 'pt-4' : 'pb-4');
      classes.push(horizontal === 'center' ? 'px-4' : 'pr-4');
      
      return classes.join(' ');
    });
    
    // Notifications actives (limitées par maxNotifications)
    const activeNotifications = computed(() => {
      return notifications.value.slice(0, props.maxNotifications);
    });
    
    // Écouter les événements de notification
    const setupEventListeners = () => {
      // Écouter les événements de notification globale (si le plugin est utilisé)
      if (window.$emitter) {
        window.$emitter.on('notification:show', add);
        window.$emitter.on('notification:close', closeById);
      }
      
      // Mettre en pause les timers lorsque la fenêtre perd le focus
      if (props.pauseOnFocusLoss) {
        window.addEventListener('blur', pauseAllTimers);
        window.addEventListener('focus', resumeAllTimers);
      }
    };
    
    // Nettoyer les écouteurs d'événements
    const cleanupEventListeners = () => {
      if (window.$emitter) {
        window.$emitter.off('notification:show', add);
        window.$emitter.off('notification:close', closeById);
      }
      
      window.removeEventListener('blur', pauseAllTimers);
      window.removeEventListener('focus', resumeAllTimers);
    };
    
    // Ajouter une notification
    const add = (notification) => {
      // Créer un ID unique si non fourni
      const id = notification.id || `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
      
      // Vérifier si une notification avec le même ID existe déjà
      const existingIndex = notifications.value.findIndex(n => n.id === id);
      if (existingIndex !== -1) {
        update(id, notification);
        return;
      }
      
      // Créer la notification
      const newNotification = {
        id,
        title: notification.title,
        message: notification.message,
        type: notification.type || 'info',
        icon: notification.icon,
        color: notification.color,
        timeout: notification.timeout !== undefined ? notification.timeout : props.duration,
        action: notification.action,
        actionText: notification.actionText,
        dismissible: notification.dismissible !== false,
        onClick: notification.onClick,
        onClose: notification.onClose,
        progress: 100,
        createdAt: Date.now(),
        paused: false,
        remaining: notification.timeout !== undefined ? notification.timeout : props.duration,
      };
      
      // Ajouter la notification au début du tableau
      notifications.value.unshift(newNotification);
      
      // Démarrer le minuteur si nécessaire
      if (newNotification.timeout > 0) {
        startTimer(newNotification);
      }
      
      // Retourner un objet avec une méthode pour fermer la notification
      return {
        close: () => close(newNotification)
      };
    };
    
    // Mettre à jour une notification existante
    const update = (id, updates) => {
      const index = notifications.value.findIndex(n => n.id === id);
      if (index === -1) return;
      
      // Mettre à jour la notification
      const notification = { ...notifications.value[index], ...updates };
      
      // Réinitialiser le minuteur si le timeout a changé
      if (updates.timeout !== undefined && !notification.paused) {
        clearTimer(notification);
        if (notification.timeout > 0) {
          startTimer(notification);
        }
      }
      
      // Mettre à jour la notification dans le tableau
      notifications.value.splice(index, 1, notification);
    };
    
    // Fermer une notification
    const close = (notification) => {
      const index = notifications.value.findIndex(n => n.id === notification.id);
      if (index === -1) return;
      
      // Appeler le callback onClose si fourni
      if (typeof notification.onClose === 'function') {
        notification.onClose();
      }
      
      // Émettre l'événement de fermeture
      emit('close', notification);
      
      // Supprimer la notification du tableau après l'animation
      setTimeout(() => {
        const index = notifications.value.findIndex(n => n.id === notification.id);
        if (index !== -1) {
          notifications.value.splice(index, 1);
        }
      }, props.animationDuration);
      
      // Nettoyer le minuteur
      clearTimer(notification);
    };
    
    // Fermer une notification par son ID
    const closeById = (id) => {
      const notification = notifications.value.find(n => n.id === id);
      if (notification) {
        close(notification);
      }
    };
    
    // Fermer toutes les notifications
    const closeAll = () => {
      notifications.value.slice().forEach(notification => {
        close(notification);
      });
    };
    
    // Gérer le clic sur une notification
    const handleClick = (notification) => {
      if (notification.onClick) {
        notification.onClick(notification);
      } else if (props.closeOnClick) {
        close(notification);
      }
      
      // Émettre l'événement de clic
      emit('click', notification);
    };
    
    // Gérer l'action d'une notification
    const handleAction = (notification) => {
      if (typeof notification.action === 'function') {
        notification.action(notification);
      }
      
      // Émettre l'événement d'action
      emit('action', notification);
      
      // Fermer la notification après l'action
      close(notification);
    };
    
    // Démarrer le minuteur pour une notification
    const startTimer = (notification) => {
      // Nettoyer tout minuteur existant
      clearTimer(notification);
      
      // Ne pas démarrer de minuteur si le timeout est désactivé
      if (notification.timeout <= 0) return;
      
      // Calculer l'intervalle pour la barre de progression
      const startTime = Date.now();
      const duration = notification.remaining || notification.timeout;
      const step = 10; // ms
      
      // Mettre à jour la progression de la barre
      const updateProgress = () => {
        if (notification.paused) return;
        
        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, duration - elapsed);
        notification.progress = (remaining / duration) * 100;
        notification.remaining = remaining;
        
        if (remaining <= 0) {
          close(notification);
        } else {
          // Planifier la prochaine mise à jour
          const timerId = setTimeout(updateProgress, step);
          timers.set(notification.id, { id: timerId, type: 'progress' });
        }
      };
      
      // Démarrer la mise à jour de la progression
      updateProgress();
      
      // Planifier la fermeture de la notification
      const timerId = setTimeout(() => {
        if (!notification.paused) {
          close(notification);
        }
      }, duration);
      
      // Enregistrer l'ID du minuteur
      timers.set(notification.id, { id: timerId, type: 'timeout' });
    };
    
    // Mettre en pause tous les minuteurs
    const pauseAllTimers = () => {
      notifications.value.forEach(notification => {
        if (!notification.paused) {
          clearTimer(notification);
          notification.paused = true;
        }
      });
    };
    
    // Reprendre tous les minuteurs
    const resumeAllTimers = () => {
      notifications.value.forEach(notification => {
        if (notification.paused) {
          notification.paused = false;
          if (notification.timeout > 0) {
            startTimer(notification);
          }
        }
      });
    };
    
    // Effacer le minuteur d'une notification
    const clearTimer = (notification) => {
      if (timers.has(notification.id)) {
        const { id, type } = timers.get(notification.id);
        clearTimeout(id);
        timers.delete(notification.id);
      }
    };
    
    // Obtenir l'icône de notification appropriée
    const notificationIcon = (notification) => {
      if (notification.icon) {
        return notification.icon;
      }
      
      switch (notification.type) {
        case 'success':
          return CheckCircleIcon;
        case 'warning':
          return ExclamationTriangleIcon;
        case 'error':
          return ExclamationCircleIcon;
        case 'info':
        default:
          return InformationCircleIcon;
      }
    };
    
    // Classes CSS pour la notification
    const notificationClass = (notification) => {
      const baseClasses = ['bg-white border'];
      
      // Couleur de la bordure en fonction du type
      switch (notification.type) {
        case 'success':
          baseClasses.push('border-green-500');
          break;
        case 'warning':
          baseClasses.push('border-yellow-500');
          break;
        case 'error':
          baseClasses.push('border-red-500');
          break;
        case 'info':
        default:
          baseClasses.push('border-blue-500');
          break;
      }
      
      return baseClasses.join(' ');
    };
    
    // Couleur de l'icône
    const iconClass = (notification) => {
      switch (notification.type) {
        case 'success':
          return 'text-green-500';
        case 'warning':
          return 'text-yellow-500';
        case 'error':
          return 'text-red-500';
        case 'info':
        default:
          return 'text-blue-500';
      }
    };
    
    // Couleur du bouton d'action
    const actionButtonClass = (notification) => {
      const baseClasses = ['px-3 py-1.5 text-sm'];
      
      switch (notification.type) {
        case 'success':
          baseClasses.push('bg-green-600 hover:bg-green-700');
          break;
        case 'warning':
          baseClasses.push('bg-yellow-600 hover:bg-yellow-700');
          break;
        case 'error':
          baseClasses.push('bg-red-600 hover:bg-red-700');
          break;
        case 'info':
        default:
          baseClasses.push('bg-blue-600 hover:bg-blue-700');
          break;
      }
      
      return baseClasses.join(' ');
    };
    
    // Couleur de la bordure de focus pour le bouton d'action
    const actionFocusRingClass = (notification) => {
      switch (notification.type) {
        case 'success':
          return 'focus:ring-green-500';
        case 'warning':
          return 'focus:ring-yellow-500';
        case 'error':
          return 'focus:ring-red-500';
        case 'info':
        default:
          return 'focus:ring-blue-500';
      }
    };
    
    // Couleur de la barre de progression
    const progressBarClass = (notification) => {
      switch (notification.type) {
        case 'success':
          return 'bg-green-500';
        case 'warning':
          return 'bg-yellow-500';
        case 'error':
          return 'bg-red-500';
        case 'info':
        default:
          return 'bg-blue-500';
      }
    };
    
    // Mettre en place les écouteurs d'événements au montage
    onMounted(() => {
      setupEventListeners();
    });
    
    // Nettoyer les écouteurs d'événements avant le démontage
    onBeforeUnmount(() => {
      cleanupEventListeners();
      
      // Nettoyer tous les minuteurs
      timers.forEach(({ id }) => {
        clearTimeout(id);
      });
      timers.clear();
    });
    
    return {
      positionClasses,
      activeNotifications,
      handleClick,
      handleAction,
      close,
      closeAll,
      notificationClass,
      iconClass,
      actionButtonClass,
      actionFocusRingClass,
      progressBarClass,
      notificationIcon,
    };
  },
};
</script>

<style scoped>
/* Animations d'entrée/sortie */
.toast-enter-active,
.toast-leave-active {
  transition: all v-bind('`${animationDuration}ms`') ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

/* Styles spécifiques pour les positions */
.top-left .toast-enter-from,
.top-center .toast-enter-from,
.top-right .toast-enter-from {
  transform: translateY(-20px);
}

.bottom-left .toast-enter-from,
.bottom-center .toast-enter-from,
.bottom-right .toast-enter-from {
  transform: translateY(20px);
}

/* Animation de la barre de progression */
@keyframes progress {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

.progress-bar {
  animation: progress v-bind('`${duration}ms`') linear forwards;
}
</style>
