import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import NotificationService from './NotificationService';

class RealtimeNotificationService {
  constructor() {
    this.echo = null;
    this.listeners = new Map();
    this.isInitialized = false;
    this.userId = null;
  }

  /**
   * Initialise le service de notifications en temps réel
   * @param {Object} user - Utilisateur connecté
   * @param {string} token - Token d'authentification
   */
  initialize(user, token) {
    if (this.isInitialized || !user) return;
    
    this.userId = user.id;
    
    // Configuration d'Echo avec Pusher
    this.echo = new Echo({
      broadcaster: 'pusher',
      key: process.env.MIX_PUSHER_APP_KEY,
      cluster: process.env.MIX_PUSHER_APP_CLUSTER,
      forceTLS: true,
      auth: {
        headers: {
          Authorization: `Bearer ${token}`,
          'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '',
        },
      },
      enabledTransports: ['ws', 'wss'],
    });

    // Écouter les notifications privées
    this.initializePrivateChannel();
    
    this.isInitialized = true;
  }

  /**
   * Initialise le canal privé pour les notifications
   */
  initializePrivateChannel() {
    if (!this.userId || !this.echo) return;
    
    // Canal privé pour les notifications utilisateur
    this.echo.private(`App.Models.User.${this.userId}`)
      .notification((notification) => {
        this.handleNewNotification(notification);
      });
      
    console.log('Écoute des notifications en temps réel activée');
  }

  /**
   * Gère une nouvelle notification reçue
   * @param {Object} notification - Notification reçue
   */
  handleNewNotification(notification) {
    console.log('Nouvelle notification reçue:', notification);
    
    // Mettre à jour le compteur de notifications non lues
    this.updateUnreadCount();
    
    // Déclencher les écouteurs d'événements
    this.triggerEvent('new-notification', notification);
    
    // Afficher une notification toast si l'utilisateur est sur la page
    if (this.shouldShowNotification(notification)) {
      this.showToastNotification(notification);
    }
  }

  /**
   * Vérifie si une notification doit être affichée à l'utilisateur
   * @param {Object} notification - Notification à vérifier
   * @returns {boolean} - True si la notification doit être affichée
   */
  shouldShowNotification(notification) {
    // Ne pas afficher si la fenêtre est active et que la notification est en sourdine
    if (document.visibilityState === 'visible' && this.isMuted) {
      return false;
    }
    
    // Vérifier les préférences de l'utilisateur pour ce type de notification
    const notificationType = notification.type || 'system';
    return this.notificationPreferences[notificationType] !== false;
  }

  /**
   * Affiche une notification toast
   * @param {Object} notification - Notification à afficher
   */
  showToastNotification(notification) {
    // Utiliser le système de notification du navigateur si disponible
    if ('Notification' in window && Notification.permission === 'granted') {
      const notificationOptions = {
        body: notification.message || '',
        icon: this.getNotificationIcon(notification),
        tag: `notification-${notification.id}`,
        data: notification,
      };
      
      const notif = new Notification(notification.title || 'Nouvelle notification', notificationOptions);
      
      notif.onclick = () => {
        window.focus();
        this.handleNotificationClick(notification);
        notif.close();
      };
    } 
    // Sinon, utiliser un système de toast personnalisé
    else if (window.toastr) {
      const type = this.getNotificationType(notification);
      window.toastr[type](notification.message, notification.title || 'Notification');
    }
  }

  /**
   * Gère le clic sur une notification
   * @param {Object} notification - Notification cliquée
   */
  handleNotificationClick(notification) {
    // Marquer comme lue
    if (!notification.read_at) {
      NotificationService.markAsRead(notification.id);
    }
    
    // Rediriger vers l'URL de l'action si elle existe
    if (notification.action_url) {
      window.location.href = notification.action_url;
    }
  }

  /**
   * Met à jour le compteur de notifications non lues
   */
  async updateUnreadCount() {
    try {
      const count = await NotificationService.getUnreadCount();
      this.triggerEvent('unread-count-updated', count);
    } catch (error) {
      console.error('Erreur lors de la mise à jour du compteur de notifications non lues:', error);
    }
  }

  /**
   * Ajoute un écouteur d'événements
   * @param {string} event - Nom de l'événement
   * @param {Function} callback - Fonction de rappel
   */
  on(event, callback) {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, new Set());
    }
    this.listeners.get(event).add(callback);
    return () => this.off(event, callback);
  }

  /**
   * Supprime un écouteur d'événements
   * @param {string} event - Nom de l'événement
   * @param {Function} callback - Fonction de rappel à supprimer
   */
  off(event, callback) {
    if (this.listeners.has(event)) {
      this.listeners.get(event).delete(callback);
    }
  }

  /**
   * Déclenche un événement
   * @param {string} event - Nom de l'événement
   * @param {...any} args - Arguments à passer aux écouteurs
   */
  triggerEvent(event, ...args) {
    if (this.listeners.has(event)) {
      this.listeners.get(event).forEach(callback => {
        try {
          callback(...args);
        } catch (error) {
          console.error(`Erreur dans l'écouteur d'événement ${event}:`, error);
        }
      });
    }
  }

  /**
   * Détermine le type de notification pour le style
   * @param {Object} notification - Notification
   * @returns {string} - Type de notification (success, error, warning, info)
   */
  getNotificationType(notification) {
    return notification.type || 'info';
  }

  /**
   * Obtient l'icône de notification appropriée
   * @param {Object} notification - Notification
   * @returns {string} - URL de l'icône
   */
  getNotificationIcon(notification) {
    const type = this.getNotificationType(notification);
    const icons = {
      success: '/images/notifications/success.png',
      error: '/images/notifications/error.png',
      warning: '/images/notifications/warning.png',
      info: '/images/notifications/info.png',
      default: '/images/notifications/default.png',
    };
    
    return icons[type] || icons.default;
  }

  /**
   * Nettoie les ressources utilisées par le service
   */
  destroy() {
    if (this.echo) {
      this.echo.leave(`App.Models.User.${this.userId}`);
      this.echo.disconnect();
      this.echo = null;
    }
    
    this.listeners.clear();
    this.isInitialized = false;
    this.userId = null;
  }
}

// Exporte une instance singleton
export default new RealtimeNotificationService();
