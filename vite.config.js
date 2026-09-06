import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Public site (Blade + Tailwind + Alpine)
                'resources/css/app.css',
                'resources/js/app.js',
                // Admin panel (Inertia + React + Ant Design)
                'resources/css/admin.css',
                'resources/js/admin.tsx',
            ],
            refresh: [
                'app/**',
                'routes/**',
                'resources/views/**',
                'resources/js/**',
                'resources/css/**',
            ],
        }),
        tailwindcss(),
        react(),
    ],
    server: {
        host: '0.0.0.0',
        port: Number(process.env.VITE_PORT || 5173),
        strictPort: false,
        watch: {
            usePolling: true,
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
