/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

import jquery from 'jquery';
window.$ = window.jQuery = jquery;

// Configuration des en-têtes par défaut d'axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Récupération du token CSRF pour les requêtes
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

/**
 * Echo expose une API expressive pour s'abonner aux canaux et écouter
 * les événements diffusés par Laravel. Echo et la diffusion d'événements
 * permettent à votre équipe de créer facilement des applications web robustes en temps réel.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configuration de Pusher
window.Pusher = Pusher;

// Récupération des clés depuis les variables d'environnement Vite
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || '';
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'eu';
const pusherHost = import.meta.env.VITE_PUSHER_HOST || `ws-${pusherCluster}.pusher.com`;
const pusherPort = import.meta.env.VITE_PUSHER_PORT || 443;
const pusherScheme = import.meta.env.VITE_PUSHER_SCHEME || 'https';

// Configuration d'Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    wsHost: pusherHost,
    wsPort: pusherPort,
    wssPort: pusherPort,
    forceTLS: pusherScheme === 'https',
    encrypted: true,
    disableStats: false,
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': token?.content || '',
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
            'X-Socket-ID': window.Echo?.socketId() || ''
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

// Export pour une utilisation dans d'autres fichiers
export { Echo };
