/**
 * Service pour la gestion des appels API du tableau de bord
 */

import axios from 'axios';

const API_BASE_URL = '/api/dashboard';

/**
 * Récupère les statistiques du tableau de bord
 * @returns {Promise<Object>} Les statistiques du tableau de bord
 */
export const getDashboardStats = async () => {
    try {
        const response = await axios.get(`${API_BASE_URL}/stats`);
        return response.data;
    } catch (error) {
        console.error('Erreur lors de la récupération des statistiques:', error);
        throw error;
    }
};

/**
 * Récupère les prochains cours à venir
 * @returns {Promise<Array>} La liste des prochains cours
 */
export const getUpcomingClasses = async () => {
    try {
        const response = await axios.get(`${API_BASE_URL}/upcoming-classes`);
        return response.data;
    } catch (error) {
        console.error('Erreur lors de la récupération des prochains cours:', error);
        throw error;
    }
};

/**
 * Récupère les dernières inscriptions
 * @returns {Promise<Array>} La liste des dernières inscriptions
 */
export const getRecentRegistrations = async () => {
    try {
        const response = await axios.get(`${API_BASE_URL}/recent-registrations`);
        return response.data;
    } catch (error) {
        console.error('Erreur lors de la récupération des inscriptions récentes:', error);
        throw error;
    }
};

export default {
    getDashboardStats,
    getUpcomingClasses,
    getRecentRegistrations
};
