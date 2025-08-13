import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

// Configuration pour le développement
const devConfig = {
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            port: 5173,
        },
    },
    // Désactiver la minification en développement pour un meilleur débogage
    build: {
        minify: false,
        sourcemap: true,
    },
};

export default defineConfig(({ mode }) => ({
    // Configuration de base
    base: mode === 'production' ? '/build/' : '',
    
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/adminlte-custom.css',
                'resources/js/app.js',
            ],
            refresh: [
                'resources/views/**',
                'app/Http/Controllers/**',
                'routes/**',
            ],
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
        postcss: './postcss.config.cjs',
        devSourcemap: true,
    },
    
    // Configuration du serveur de développement
    ...(mode === 'development' ? devConfig : {}),
    
    // Exclure AdminLTE du traitement Vite
    optimizeDeps: {
        exclude: ['admin-lte']
    },
    
    // Définir les variables d'environnement accessibles côté client
    define: {
        'process.env': {
            VITE_APP_NAME: JSON.stringify(process.env.VITE_APP_NAME || 'Allo Tawjih'),
            VITE_PUSHER_APP_KEY: JSON.stringify(process.env.VITE_PUSHER_APP_KEY || ''),
            VITE_PUSHER_HOST: JSON.stringify(process.env.VITE_PUSHER_HOST || ''),
            VITE_PUSHER_PORT: JSON.stringify(process.env.VITE_PUSHER_PORT || '443'),
            VITE_PUSHER_SCHEME: JSON.stringify(process.env.VITE_PUSHER_SCHEME || 'https'),
            VITE_PUSHER_APP_CLUSTER: JSON.stringify(process.env.VITE_PUSHER_APP_CLUSTER || 'eu'),
            }
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
}));
