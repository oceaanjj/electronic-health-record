import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS Files
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

                // JS Files
                'resources/js/app.js',
                'resources/js/adpie-alert.js',
                'resources/js/alert.js',
                'resources/js/audit-search.js',
                'resources/js/close-cdss-alert.js',
                'resources/js/compute-age.js',
                'resources/js/date-day-loader.js',
                'resources/js/date-day-sync.js',
                'resources/js/diagnostics.js',
                'resources/js/discharge-planning-loader.js',
                'resources/js/form-disable-alert.js',
                'resources/js/form-saver.js',
                'resources/js/init-searchable-dropdown.js',
                'resources/js/intake-and-output-date-sync.js',
                'resources/js/intake-output-cdss.js',
                'resources/js/intake-output-data-loader.js',
                'resources/js/intake-output-patient-loader.js',
                'resources/js/medication-administration-loader.js',
                'resources/js/medication-form-validation.js',
                'resources/js/page-initializer.js',
                'resources/js/patient-loader.js',
                'resources/js/patient-report.js',
                'resources/js/patient-search.js',
                'resources/js/search.js',
                'resources/js/searchable-dropdown.js',
                'resources/js/soft-delete.js',
                'resources/js/sweetalert.js',
                'resources/js/vital-signs-chart-updater.js',
                'resources/js/vital-signs-charts.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});