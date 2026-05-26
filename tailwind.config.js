/**
 * tailwind.config.js
 *
 * NOTE: Proyek ini menggunakan Tailwind CSS v4.
 * Di v4, konfigurasi `darkMode` tidak dibaca dari sini.
 * Class-based dark mode dikonfigurasi via `@custom-variant dark`
 * di dalam resources/css/app.css.
 *
 * File ini dipertahankan untuk kompatibilitas editor / IDE tools.
 */
/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.jsx',
        './resources/**/*.ts',
        './resources/**/*.tsx',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
