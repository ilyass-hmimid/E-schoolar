import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

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
        // Configuration de la résolution des imports
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                '~': path.resolve(__dirname, './node_modules'),
            },
        },
        // Configuration CSS
        css: {
            postcss: {
                plugins: [
                    // Configuration simplifiée pour éviter les problèmes d'import
                    tailwindcss,
                    autoprefixer,
                ],
            },
            // Activer les source maps en développement
            devSourcemap: true,
        },
        // Désactiver les optimisations qui peuvent causer des problèmes
        optimizeDeps: {
            include: ['@inertiajs/vue3', 'vue', 'vue-router'],
            exclude: ['admin-lte'],
        },
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
                protocol: 'ws',
            },
        },
        // Optimisation des builds de production
        build: {
            chunkSizeWarningLimit: 1000,
            rollupOptions: {
                output: {
                    manualChunks: {
                        vue: ['vue', 'vue-router', 'vuex'],
                        adminlte: ['admin-lte'],
                        fontawesome: ['@fortawesome/fontawesome-free'],
                    },
                },
            },
        },
    };
});
