import './bootstrap';
import 'admin-lte/plugins/jquery/jquery.min.js';
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
import Alpine from 'alpinejs';

import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router';
import Routes from './routes.js';

const app = createApp({});



    const router = createRouter({
        routes: Routes, // Modifier cette ligne
        history: createWebHistory(),
    });


    app.use(router);

    app.mount('#app');

// window.Alpine = Alpine;

// Alpine.start();
