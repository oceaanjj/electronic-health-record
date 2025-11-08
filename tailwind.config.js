/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                "text-dark-red": "#A00000",
                ehr: "#008080",
                "dark-yellow": "#FFA500",
            },
        },
    },
    plugins: [],
};
