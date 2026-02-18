import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/home-style.css',
                'resources/css/login-style.css',
                'resources/css/admin-register.css',
                'resources/css/admin-users.css',
                'resources/css/admin.css',
                'resources/css/audit-index.css',
                'resources/css/edit-style.css',
                'resources/css/index-style.css',
                'resources/css/search-style.css',
                'resources/css/show-style.css',
                'resources/js/app.js',
                'resources/js/alert.js',
                'resources/js/date-day-loader.js',
                'resources/js/patient-loader.js',
                'resources/js/init-searchable-dropdown.js',
                'resources/js/patient-search.js',
                'resources/js/close-cdss-alert.js',

                'resources/js/searchable-dropdown.js',
                'resources/js/date-day-sync.js',
                'resources/js/vital-signs-charts.js',
            ],

            refresh: true,
        }),
        tailwindcss(),
    ],
});
