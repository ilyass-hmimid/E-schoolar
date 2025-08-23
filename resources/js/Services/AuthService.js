import axios from 'axios';
import router from '../routes';

class AuthService {
    constructor() {
        this.user = null;
        this.isAuthenticated = false;
        this.init();
    }

    // Initialisation du service
    init() {
        this.loadUserFromStorage();
        this.setupAxiosInterceptors();
    }

    // Charger l'utilisateur depuis le stockage local
    loadUserFromStorage() {
        const user = localStorage.getItem('user');
        const token = localStorage.getItem('token');
        
        if (user && token) {
            this.user = JSON.parse(user);
            this.isAuthenticated = true;
            this.setAuthToken(token);
        }
    }

    // Configurer les intercepteurs Axios
    setupAxiosInterceptors() {
        // Intercepteur pour les requêtes sortantes
        axios.interceptors.request.use(
            config => {
                const token = localStorage.getItem('token');
                if (token) {
                    config.headers['Authorization'] = `Bearer ${token}`;
                }
                return config;
            },
            error => {
                return Promise.reject(error);
            }
        );

        // Intercepteur pour les réponses entrantes
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response) {
                    // Si le token a expiré ou est invalide
                    if (error.response.status === 401) {
                        this.logout();
                        router.push({ name: 'login', query: { sessionExpired: 'true' } });
                    }
                }
                return Promise.reject(error);
            }
        );
    }

    // Définir le token d'authentification
    setAuthToken(token) {
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    // Se connecter
    async login(credentials) {
        try {
            const response = await axios.post('/api/login', credentials);
            const { user, token } = response.data;
            
            // Stocker les informations de l'utilisateur
            this.user = user;
            this.isAuthenticated = true;
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token', token);
            this.setAuthToken(token);
            
            return { success: true, user };
        } catch (error) {
            console.error('Erreur de connexion:', error);
            return { 
                success: false, 
                error: error.response?.data?.message || 'Échec de la connexion. Veuillez réessayer.' 
            };
        }
    }

    // S'inscrire
    async register(userData) {
        try {
            const response = await axios.post('/api/register', userData);
            const { user, token } = response.data;
            
            // Connecter automatiquement l'utilisateur après l'inscription
            this.user = user;
            this.isAuthenticated = true;
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token', token);
            this.setAuthToken(token);
            
            return { success: true, user };
        } catch (error) {
            console.error("Erreur d'inscription:", error);
            return { 
                success: false, 
                error: error.response?.data?.message || "Échec de l'inscription. Veuillez réessayer." 
            };
        }
    }

    // Se déconnecter
    async logout() {
        try {
            await axios.post('/api/logout');
        } catch (error) {
            console.error('Erreur lors de la déconnexion:', error);
        } finally {
            // Nettoyer les données d'authentification
            this.user = null;
            this.isAuthenticated = false;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
            
            // Rediriger vers la page de connexion
            router.push({ name: 'login' });
        }
    }

    // Vérifier si l'utilisateur est authentifié
    checkAuth() {
        return this.isAuthenticated;
    }

    // Vérifier si l'utilisateur a un rôle spécifique
    hasRole(roles) {
        if (!this.user || !this.user.roles) return false;
        if (typeof roles === 'string') roles = [roles];
        return roles.some(role => this.user.roles.includes(role));
    }

    // Rafraîchir les informations de l'utilisateur
    async refreshUser() {
        try {
            const response = await axios.get('/api/user');
            this.user = response.data;
            localStorage.setItem('user', JSON.stringify(this.user));
            return this.user;
        } catch (error) {
            console.error('Erreur lors du rafraîchissement des informations utilisateur:', error);
            this.logout();
            return null;
        }
    }
}

// Exporter une instance unique du service
export default new AuthService();
