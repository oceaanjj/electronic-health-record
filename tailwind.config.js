/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: ['./resources/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue'],
    theme: {
...
            colors: {
                'text-dark-red': '#A00000',
                ehr: '#008080',
                'dark-yellow': '#FFA500',
            },
        },
    },
    plugins: [],
};
