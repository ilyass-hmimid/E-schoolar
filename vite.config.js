import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
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
                },
            },
        }),
    ],
    css: {
        postcss: './postcss.config.cjs'
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            '@heroicons/vue/outline': '@heroicons/vue/outline/esm/',
        },
    },
    optimizeDeps: {
        include: ['@heroicons/vue/outline'],
    },
});
