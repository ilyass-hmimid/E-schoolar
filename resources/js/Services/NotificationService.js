import axios from 'axios';

class NotificationService {
  /**
   * Récupère les notifications de l'utilisateur
   * @param {Object} params - Paramètres de pagination et de filtrage
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async getNotifications(params = {}) {
    try {
      const response = await axios.get('/api/notifications', { params });
      return response.data;
    } catch (error) {
      console.error('Erreur lors de la récupération des notifications:', error);
      throw error;
    }
  }

  /**
   * Récupère une notification spécifique
   * @param {string} id - ID de la notification
   * @returns {Promise<Object>} - Données de la notification
   */
  static async getNotification(id) {
    try {
      const response = await axios.get(`/api/notifications/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Erreur lors de la récupération de la notification ${id}:`, error);
      throw error;
    }
  }

  /**
   * Marque une notification comme lue
   * @param {string} id - ID de la notification
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async markAsRead(id) {
    try {
      const response = await axios.post(`/api/notifications/${id}/read`);
      return response.data;
    } catch (error) {
      console.error(`Erreur lors du marquage de la notification ${id} comme lue:`, error);
      throw error;
    }
  }

  /**
   * Marque toutes les notifications comme lues
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async markAllAsRead() {
    try {
      const response = await axios.post('/api/notifications/read-all');
      return response.data;
    } catch (error) {
      console.error('Erreur lors du marquage de toutes les notifications comme lues:', error);
      throw error;
    }
  }

  /**
   * Supprime une notification
   * @param {string} id - ID de la notification
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async deleteNotification(id) {
    try {
      const response = await axios.delete(`/api/notifications/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Erreur lors de la suppression de la notification ${id}:`, error);
      throw error;
    }
  }

  /**
   * Supprime toutes les notifications
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async clearAll() {
    try {
      const response = await axios.delete('/api/notifications');
      return response.data;
    } catch (error) {
      console.error('Erreur lors de la suppression de toutes les notifications:', error);
      throw error;
    }
  }

  /**
   * Récupère le nombre de notifications non lues
   * @returns {Promise<number>} - Nombre de notifications non lues
   */
  static async getUnreadCount() {
    try {
      const response = await axios.get('/api/notifications/unread/count');
      return response.data.count;
    } catch (error) {
      console.error('Erreur lors de la récupération du nombre de notifications non lues:', error);
      return 0;
    }
  }

  /**
   * Récupère les préférences de notification de l'utilisateur
   * @returns {Promise<Object>} - Préférences de notification
   */
  static async getPreferences() {
    try {
      const response = await axios.get('/api/notifications/preferences');
      return response.data.data || {};
    } catch (error) {
      console.error('Erreur lors de la récupération des préférences de notification:', error);
      throw error;
    }
  }

  /**
   * Met à jour les préférences de notification de l'utilisateur
   * @param {Object} preferences - Nouvelles préférences
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async updatePreferences(preferences) {
    try {
      const response = await axios.put('/api/notifications/preferences', preferences);
      return response.data;
    } catch (error) {
      console.error('Erreur lors de la mise à jour des préférences de notification:', error);
      throw error;
    }
  }

  /**
   * S'abonne aux notifications push
   * @param {Object} subscription - Objet d'abonnement push
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async subscribeToPush(subscription) {
    try {
      const response = await axios.post('/api/push/subscribe', subscription);
      return response.data;
    } catch (error) {
      console.error('Erreur lors de l\'abonnement aux notifications push:', error);
      throw error;
    }
  }

  /**
   * Se désabonne des notifications push
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async unsubscribeFromPush() {
    try {
      const response = await axios.post('/api/push/unsubscribe');
      return response.data;
    } catch (error) {
      console.error('Erreur lors du désabonnement des notifications push:', error);
      throw error;
    }
  }

  /**
   * Envoie une notification de test
   * @param {string} type - Type de notification (info, success, warning, error)
   * @returns {Promise<Object>} - Réponse de l'API
   */
  static async sendTestNotification(type = 'info') {
    try {
      const response = await axios.post('/api/notifications/test', { type });
      return response.data;
    } catch (error) {
      console.error('Erreur lors de l\'envoi d\'une notification de test:', error);
      throw error;
    }
  }
}

export default NotificationService;
