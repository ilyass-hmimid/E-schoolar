import { defineConfig } from 'vite';

export default defineConfig({
    server: {
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: {
                app: './resources/js/app.js',
                css: './resources/css/app.css'
            }
        }
    },
    publicDir: 'public'
});
