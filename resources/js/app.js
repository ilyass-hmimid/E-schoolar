// Import des styles
import '../css/app.css';

// Import des dépendances JavaScript
import 'bootstrap';
import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy';

// Configuration globale
window.bootstrap = bootstrap;

// Initialisation d'Alpine.js
window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

// Création de l'application Inertia
createInertiaApp({
    // Résolution des composants de page
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`] || pages[`./Pages/${name}/Index.vue`];
        
        if (!page) {
            throw new Error(`Page not found: ${name}.vue or ${name}/Index.vue`);
        }
        
        return page;
    },
    // Configuration de la page de base
    setup({ el, App, props, plugin }) {
        // Création de l'application Vue
        const app = createApp({ render: () => h(App, props) });
        
        // Utilisation des plugins
        app.use(plugin)
           .use(ZiggyVue, Ziggy)
           .component('Link', Link)
           .component('Head', Head);
        
        // Make route() function available in all components
        app.config.globalProperties.$route = window.route;
        
        // Montage de l'application
        app.mount(el);
        
        // Initialisation des composants Bootstrap après le montage
        const initBootstrap = () => {
            // Initialisation des tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(tooltipTriggerEl => {
                try {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                } catch (error) {
                    console.error('Erreur lors de l\'initialisation du tooltip:', error);
                }
            });
            
            // Initialisation des popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.forEach(popoverTriggerEl => {
                try {
                    new bootstrap.Popover(popoverTriggerEl);
                } catch (error) {
                    console.error('Erreur lors de l\'initialisation du popover:', error);
                }
            });
        };
        
        // Initialisation de Bootstrap après le rendu du composant
        app.mixin({
            mounted() {
                initBootstrap();
            },
            updated() {
                initBootstrap();
            }
        });
    },
});
