import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    // Charger les variables d'environnement
    const env = loadEnv(mode, process.cwd(), '');
    
    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    }
                }
            }),
        ],
        // Définir les variables d'environnement accessibles côté client
        define: {
            'process.env': {
                VITE_APP_NAME: JSON.stringify(env.VITE_APP_NAME || 'Allo Tawjih'),
                VITE_PUSHER_APP_KEY: JSON.stringify(env.VITE_PUSHER_APP_KEY || ''),
                VITE_PUSHER_HOST: JSON.stringify(env.VITE_PUSHER_HOST || ''),
                VITE_PUSHER_PORT: JSON.stringify(env.VITE_PUSHER_PORT || '443'),
                VITE_PUSHER_SCHEME: JSON.stringify(env.VITE_PUSHER_SCHEME || 'https'),
                VITE_PUSHER_APP_CLUSTER: JSON.stringify(env.VITE_PUSHER_APP_CLUSTER || 'eu'),
            }
        },
        // Configuration du serveur de développement
        server: {
            hmr: {
                host: 'localhost',
            },
        },
    };
});
