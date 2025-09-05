// @ts-check
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ command }) => ({

    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
    },
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        manifestPath: 'public/build/manifest.json',
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]',
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            '~': '/resources',
        },
    },
    optimizeDeps: {
        include: ['alpinejs', 'axios'],
    },
}));
