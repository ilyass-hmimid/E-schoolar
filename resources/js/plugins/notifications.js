import { createApp } from 'vue';
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import NotificationPreferencesModal from '@/Components/Modals/NotificationPreferencesModal.vue';
import NotificationService from '@/Services/NotificationService';
import RealtimeNotificationService from '@/Services/RealtimeNotificationService';
import notificationConfig from '@/config/notifications';

const NotificationsPlugin = {
  install(app, options = {}) {
    // Configuration
    const config = {
      ...notificationConfig,
      ...options.config,
    };
    
    // Enregistrement des composants globaux
    app.component('NotificationBell', NotificationBell);
    app.component('NotificationPreferencesModal', NotificationPreferencesModal);
    
    // Fournir les services et la configuration aux composants
    app.provide('notificationService', NotificationService);
    app.provide('realtimeNotificationService', RealtimeNotificationService);
    app.provide('notificationConfig', config);
    
    // Méthodes d'aide globales
    const notifications = {
      /**
       * Affiche une notification toast
       * @param {Object} options - Options de la notification
       */
      show(options) {
        // Vérifier si le type de notification est activé
        const type = options.type || 'info';
        if (!config.methods.isTypeEnabled(type)) {
          return;
        }
        
        // Créer un élément de notification
        const notification = {
          id: `notif-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
          title: options.title || 'Notification',
          message: options.message,
          type,
          icon: options.icon || config.methods.getIcon(type),
          color: options.color || config.methods.getColor(type),
          timeout: options.timeout || config.general.toastDuration,
          action: options.action,
          actionText: options.actionText,
          dismissible: options.dismissible !== false,
          onClick: options.onClick,
          onClose: options.onClose,
        };
        
        // Émettre un événement pour afficher la notification
        app.config.globalProperties.$emitter.emit('notification:show', notification);
        
        // Retourner un objet avec une méthode pour fermer la notification
        return {
          close: () => {
            app.config.globalProperties.$emitter.emit('notification:close', notification.id);
          }
        };
      },
      
      /**
       * Affiche une notification de succès
       * @param {string} message - Message de la notification
       * @param {Object} options - Options supplémentaires
       */
      success(message, options = {}) {
        return this.show({
          ...options,
          type: 'success',
          message,
          title: options.title || 'Succès',
        });
      },
      
      /**
       * Affiche une notification d'erreur
       * @param {string} message - Message de la notification
       * @param {Object} options - Options supplémentaires
       */
      error(message, options = {}) {
        return this.show({
          ...options,
          type: 'error',
          message,
          title: options.title || 'Erreur',
        });
      },
      
      /**
       * Affiche une notification d'avertissement
       * @param {string} message - Message de la notification
       * @param {Object} options - Options supplémentaires
       */
      warning(message, options = {}) {
        return this.show({
          ...options,
          type: 'warning',
          message,
          title: options.title || 'Avertissement',
        });
      },
      
      /**
       * Affiche une notification d'information
       * @param {string} message - Message de la notification
       * @param {Object} options - Options supplémentaires
       */
      info(message, options = {}) {
        return this.show({
          ...options,
          type: 'info',
          message,
          title: options.title || 'Information',
        });
      },
      
      /**
       * Affiche la modale des préférences de notification
       */
      showPreferences() {
        app.config.globalProperties.$emitter.emit('notification:show-preferences');
      },
      
      /**
       * Met à jour le compteur de notifications non lues
       */
      async updateUnreadCount() {
        try {
          const count = await NotificationService.getUnreadCount();
          app.config.globalProperties.$emitter.emit('notification:unread-count-updated', count);
          return count;
        } catch (error) {
          console.error('Erreur lors de la mise à jour du compteur de notifications non lues:', error);
          return 0;
        }
      },
      
      /**
       * Initialise le service de notifications en temps réel
       * @param {Object} user - Utilisateur connecté
       * @param {string} token - Token d'authentification
       */
      initializeRealtime(user, token) {
        RealtimeNotificationService.initialize(user, token);
        
        // Mettre à jour le compteur de notifications non lues lors de la réception d'une nouvelle notification
        RealtimeNotificationService.on('new-notification', () => {
          this.updateUnreadCount();
        });
      },
    };
    
    // Ajouter les méthodes globales
    app.config.globalProperties.$notify = notifications;
    
    // Fournir l'instance de notification
    app.provide('notify', notifications);
    
    // Enregistrer le plugin comme installé
    app.config.globalProperties.$notificationsInstalled = true;
  }
};

// Installation automatique lorsque Vue est chargé (si window.Vue est disponible)
if (typeof window !== 'undefined' && window.Vue) {
  window.Vue.use(NotificationsPlugin);
}

export default NotificationsPlugin;
