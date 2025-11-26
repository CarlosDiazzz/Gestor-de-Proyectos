import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Light Mode
                light: {
                    background: '#ffffff',
                    surface: '#f5f5f5',
                    text: '#010d23',
                    primary: '#038bbb',
                    secondary: '#e19f41',
                    accent: '#fccb6f',
                },

                // Dark Mode
                dark: {
                    background: '#010d23',
                    surface: '#03223f',
                    text: '#ffffff',
                    primary: '#038bbb',
                    secondary: '#fccb6f',
                    accent: '#e19f41',
                },

                // Colores individuales
                midnight: {
                    900: '#010d23',
                    700: '#03223f',
                },
                ocean: {
                    500: '#038bbb',
                },
                sunset: {
                    300: '#fccb6f',
                    500: '#e19f41',
                },
            },
        },
    },

    plugins: [forms],
}
