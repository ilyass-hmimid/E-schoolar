import './bootstrap';
import 'admin-lte/plugins/jquery/jquery.min.js';
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router';
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import ToastNotifications from '@/Components/Notifications/ToastNotifications.vue';
import Routes from './routes';

// Création de l'application
const app = createApp({});

// Configuration du routeur
const router = createRouter({
    routes: Routes,
    history: createWebHistory(),
    linkActiveClass: 'active',
    linkExactActiveClass: 'active',
});

// Configuration de l'application
app.use(router);

// Composants globaux
app.component('NotificationBell', NotificationBell);
app.component('ToastNotifications', ToastNotifications);

// Montage de l'application
app.mount('#app');

// Export pour une utilisation potentielle ailleurs
export { app, router };

export default router;
