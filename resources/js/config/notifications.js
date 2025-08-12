/**
 * Configuration des notifications côté client
 * Ce fichier contient les paramètres de configuration pour le système de notifications
 */

export default {
  // Paramètres généraux
  general: {
    // Activer/désactiver les notifications
    enabled: true,
    
    // Durée d'affichage des toasts (en millisecondes)
    toastDuration: 5000,
    
    // Nombre maximum de notifications à afficher dans la liste
    maxVisibleNotifications: 15,
    
    // Durée de rafraîchissement du compteur de notifications (en millisecondes)
    refreshInterval: 30000,
  },
  
  // Paramètres des canaux de notification
  channels: {
    // Paramètres pour les notifications par email
    email: {
      enabled: true,
      label: 'Email',
      description: 'Recevoir les notifications par email',
    },
    
    // Paramètres pour les notifications internes
    database: {
      enabled: true,
      label: 'Notification interne',
      description: 'Afficher les notifications dans l\'application',
    },
    
    // Paramètres pour les notifications push
    push: {
      enabled: 'Notification' in window,
      label: 'Notifications push',
      description: 'Recevoir des notifications push sur votre appareil',
      // Clé publique VAPID pour les notifications push (Web Push)
      publicKey: process.env.MIX_VAPID_PUBLIC_KEY || '',
    },
    
    // Paramètres pour les notifications SMS
    sms: {
      enabled: false, // Désactivé par défaut, nécessite une configuration supplémentaire
      label: 'SMS',
      description: 'Recevoir des notifications par SMS',
    },
  },
  
  // Types de notifications disponibles
  types: {
    absence: {
      label: 'Absences et retards',
      description: 'Notifications concernant les absences et retards des élèves',
      defaultChannels: ['database', 'email'],
      enabled: true,
    },
    
    payment: {
      label: 'Paiements',
      description: 'Notifications concernant les paiements et échéances',
      defaultChannels: ['database', 'email'],
      enabled: true,
    },
    
    grades: {
      label: 'Notes et évaluations',
      description: 'Notifications concernant les notes et évaluations',
      defaultChannels: ['database', 'email'],
      enabled: true,
    },
    
    system: {
      label: 'Notifications système',
      description: 'Mises à jour et informations importantes du système',
      defaultChannels: ['database'],
      enabled: true,
      required: true, // Les notifications système ne peuvent pas être désactivées
    },
    
    newsletter: {
      label: 'Newsletter et offres',
      description: 'Recevoir des offres spéciales et des mises à jour',
      defaultChannels: ['email'],
      enabled: false, // Désactivé par défaut (opt-in)
    },
  },
  
  // Paramètres de fréquence des notifications
  frequency: {
    realtime: {
      label: 'En temps réel',
      description: 'Recevoir les notifications immédiatement',
    },
    
    daily: {
      label: 'Quotidien',
      description: 'Recevoir un résumé quotidien',
      defaultTime: '09:00',
    },
    
    weekly: {
      label: 'Hebdomadaire',
      description: 'Recevoir un résumé hebdomadaire',
      defaultDay: 'monday',
      defaultTime: '09:00',
    },
  },
  
  // Paramètres des icônes de notification
  icons: {
    default: 'bell',
    types: {
      success: 'check-circle',
      error: 'x-circle',
      warning: 'exclamation',
      info: 'information-circle',
      system: 'cog',
      payment: 'currency-euro',
      absence: 'clock',
      grades: 'academic-cap',
      newsletter: 'mail',
    },
  },
  
  // Paramètres des couleurs des notifications
  colors: {
    default: 'indigo',
    types: {
      success: 'green',
      error: 'red',
      warning: 'yellow',
      info: 'blue',
      system: 'gray',
      payment: 'purple',
      absence: 'red',
      grades: 'blue',
      newsletter: 'pink',
    },
  },
  
  // Paramètres des sons de notification
  sounds: {
    enabled: true,
    volume: 0.5,
    default: '/sounds/notification.mp3',
    types: {
      success: '/sounds/success.mp3',
      error: '/sounds/error.mp3',
      warning: '/sounds/warning.mp3',
      info: '/sounds/info.mp3',
    },
  },
  
  // Paramètres de personnalisation des notifications
  ui: {
    // Position des toasts de notification
    toastPosition: 'top-right', // 'top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'
    
    // Style des notifications
    style: 'minimal', // 'minimal', 'rich', 'compact'
    
    // Afficher les images dans les notifications
    showImages: true,
    
    // Afficher les actions rapides
    showQuickActions: true,
    
    // Activer les animations
    animations: true,
    
    // Durée des animations (en millisecondes)
    animationDuration: 300,
  },
  
  // Paramètres de débogage
  debug: {
    enabled: process.env.NODE_ENV !== 'production',
    logLevel: 'warn', // 'error', 'warn', 'info', 'debug'
  },
  
  // Méthodes utilitaires
  methods: {
    /**
     * Vérifie si un type de notification est activé
     * @param {string} type - Type de notification
     * @returns {boolean}
     */
    isTypeEnabled(type) {
      return this.types[type]?.enabled !== false;
    },
    
    /**
     * Obtient les canaux par défaut pour un type de notification
     * @param {string} type - Type de notification
     * @returns {string[]}
     */
    getDefaultChannels(type) {
      return this.types[type]?.defaultChannels || ['database'];
    },
    
    /**
     * Obtient l'icône pour un type de notification
     * @param {string} type - Type de notification
     * @returns {string}
     */
    getIcon(type) {
      return this.icons.types[type] || this.icons.default;
    },
    
    /**
     * Obtient la couleur pour un type de notification
     * @param {string} type - Type de notification
     * @returns {string}
     */
    getColor(type) {
      return this.colors.types[type] || this.colors.default;
    },
  },
};

// Exporte également une instance configurée pour une utilisation facile
import { reactive } from 'vue';

export const notificationConfig = reactive({
  ...this,
  // Surcharge des méthodes pour qu'elles soient réactives
  methods: {
    isTypeEnabled: (type) => this.methods.isTypeEnabled(type),
    getDefaultChannels: (type) => this.methods.getDefaultChannels(type),
    getIcon: (type) => this.methods.getIcon(type),
    getColor: (type) => this.methods.getColor(type),
  },
});
