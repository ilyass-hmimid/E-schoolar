/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';

// Configuration d'axios
window.axios = axios.create({
    baseURL: '/',
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    },
    timeout: 30000, // 30 secondes de timeout
});

// Configuration des intercepteurs pour la gestion des erreurs
window.axios.interceptors.response.use(
    response => response,
    error => {
        const { status, data } = error.response || {};
        
        // Gestion des erreurs HTTP courantes
        if (status === 401) {
            // Redirection vers la page de connexion si non authentifié
            if (window.location.pathname !== '/login') {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
            }
        } else if (status === 403) {
            // Accès refusé
            console.error('Accès refusé :', data?.message || 'Vous n\'avez pas les permissions nécessaires');
        } else if (status === 404) {
            // Ressource non trouvée
            console.error('Ressource non trouvée :', error.config.url);
        } else if (status === 419) {
            // Token CSRF expiré
            console.error('Session expirée. Veuillez vous reconnecter.');
            window.location.reload();
        } else if (status === 422) {
            // Erreur de validation
            console.error('Erreur de validation :', data?.errors || data?.message);
        } else if (status >= 500) {
            // Erreur serveur
            console.error('Erreur serveur :', data?.message || 'Une erreur est survenue sur le serveur');
        }
        
        return Promise.reject(error);
    }
);

// Récupération du token CSRF pour les requêtes
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Configuration d'Echo pour les notifications en temps réel (mock)
window.Echo = {
    channel: () => ({
        listen: () => ({}),
        notification: () => ({}),
    }),
    private: () => ({
        listen: () => ({}),
        notification: () => ({}),
    }),
    join: () => ({
        here: () => {},
        joining: () => {},
        leaving: () => {},
        listen: () => {},
    }),
};

console.warn('Pusher non configuré - notifications en temps réel désactivées');

// Vérification de l'existence de pusherKey avant utilisation
const pusherKey = window.pusherKey || '';
if (pusherKey) {
    console.log('Configuration Pusher:', {
        key: '***' + pusherKey.slice(-4),
        cluster: pusherCluster,
        host: pusherHost,
        port: pusherPort,
        scheme: pusherScheme
    });

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        wsHost: pusherHost,
        wsPort: pusherPort,
        wssPort: pusherPort,
        forceTLS: pusherScheme === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: false,
        encrypted: true,
        auth: {
            headers: {
                'X-CSRF-TOKEN': token?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
            }
        }
    });

    // Gestion des erreurs de connexion
    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Erreur de connexion Pusher:', err);
    });

    // Gestion des états de connexion
    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log('Changement d\'état de connexion:', states);
    });
} else {
    console.warn('Pusher non configuré - notifications en temps réel désactivées');
    // Créer un Echo factice pour éviter les erreurs
    window.Echo = {
        channel: () => ({
            listen: () => ({}),
            notification: () => ({}),
        }),
        private: () => ({
            listen: () => ({}),
            notification: () => ({}),
        }),
        join: () => ({
            here: () => {},
            joining: () => {},
            leaving: () => {},
            listen: () => {},
        }),
    };
}
