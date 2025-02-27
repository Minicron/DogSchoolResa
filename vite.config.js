import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel(['resources/css/app.css', 'resources/js/app.js'])
    ],
    server: {
        host: 'dogschoolresa.test',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'dogschoolresa.test',
        },
        watch: {
            // On ignore le dossier où Laravel compile les vues
            ignored: [
                '**/storage/framework/views/**'
            ]
        }
    }
});
