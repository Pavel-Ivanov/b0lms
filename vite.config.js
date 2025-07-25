import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                `resources/css/filament/intern/theme.css`,
                `resources/css/filament/teacher/theme.css`,
            ],
            // input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
