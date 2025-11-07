import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/home-style.css",
                "resources/css/login-style.css",
                "resources/css/registration-style.css",
                "resources/css/act-of-daily-living.css",
                "resources/css/admin-register.css",
                "resources/css/admin-users.css",
                "resources/css/admin.css",
                "resources/css/audit-index.css",
                "resources/css/discharge-planning.css",
                "resources/css/edit-style.css",
                "resources/css/index-style.css",
                "resources/css/intake-and-output-style.css",
                "resources/css/ivs-and-lines.css",
                "resources/css/lab-values.css",
                "resources/css/medical-history-style.css",
                "resources/css/medication-administration.css",
                "resources/css/medication-reconciliation.css",
                "resources/css/physical-exam-style.css",
                "resources/css/search-style.css",
                "resources/css/show-style.css",
                "resources/css/vital-signs-style.css",
                "resources/js/app.js",
                "resources/js/alert.js",
                "resources/js/date-day-loader.js",
                "resources/js/patient-loader.js",
                "resources/js/search.js",
                "resources/js/searchable-dropdown.js",
                "resources/js/patient-search.js",
            ],

            refresh: true,
        }),
        tailwindcss(),
    ],
});
