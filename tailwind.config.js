import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
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
                // Paletas personalizadas por rol
                'deep-koamaru': {
                    '50': '#f3f6ff',
                    '100': '#e8ecff',
                    '200': '#d5deff',
                    '300': '#b3c1ff',
                    '400': '#8898fd',
                    '500': '#5866fa',
                    '600': '#3539f2',
                    '700': '#2323de',
                    '800': '#1d1dba',
                    '900': '#1a1a98',
                    '950': '#101478',
                },
                'aqua-island': {
                    '50': '#f1faf9',
                    '100': '#dbf2f0',
                    '200': '#bbe6e3',
                    '300': '#9bdad7',
                    '400': '#55bbb8',
                    '500': '#3aa09e',
                    '600': '#338587',
                    '700': '#2f6b6f',
                    '800': '#2d595d',
                    '900': '#294b50',
                    '950': '#173135',
                },
                'pine-cone': {
                    '50': '#f5f4f1',
                    '100': '#e6e2db',
                    '200': '#cec5ba',
                    '300': '#b1a493',
                    '400': '#9b8774',
                    '500': '#8c7866',
                    '600': '#725e52',
                    '700': '#614e47',
                    '800': '#54443f',
                    '900': '#4a3c39',
                    '950': '#29201f',
                },
            },
            animation: {
                'book-open': 'bookOpen 1.5s ease-out forwards',
                'book-close': 'bookClose 1.2s ease-in forwards',
                'page-turn': 'pageTurn 1s ease-in-out',
                'spin-slow': 'spin 3s linear infinite',
            },
            keyframes: {
                bookOpen: {
                    '0%': { transform: 'rotateY(0) translateX(0)' },
                    '100%': { transform: 'rotateY(-170deg) translateX(-100px)' }
                },
                bookClose: {
                    '0%': { transform: 'rotateY(-170deg) translateX(-100px)' },
                    '100%': { transform: 'rotateY(0) translateX(0)' }
                },
                pageTurn: {
                    '0%': { transform: 'rotateY(0)' },
                    '100%': { transform: 'rotateY(-10deg)' }
                }
            },
            perspective: {
                '1000': '1000px',
            },
            transformStyle: {
                '3d': 'preserve-3d',
            },
            backfaceVisibility: {
                'hidden': 'hidden',
            }
        },
    },

    plugins: [
        forms,
        function ({ addUtilities }) {
            addUtilities({
                '.backface-visible': {
                    'backface-visibility': 'visible',
                },
                '.backface-hidden': {
                    'backface-visibility': 'hidden',
                },
                '.preserve-3d': {
                    'transform-style': 'preserve-3d',
                },
            })
        }
    ],
}
