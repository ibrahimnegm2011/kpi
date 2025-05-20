import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.css',
        './app/**/*.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#e6faf5',
                    100: '#c0f2e5',
                    200: '#8ae5cf',
                    300: '#4dd6b7',
                    400: '#19cfa1',
                    500: '#00a886',
                    600: '#008c6f',
                    700: '#00725a',
                    800: '#005845',
                    900: '#003d2f',
                    950: '#00241b',
                },
                secondary: {
                    50: '#f4f4fb',
                    100: '#e6e6f4',
                    200: '#c6c5e5',
                    300: '#a6a5d7',
                    400: '#8a89c8',
                    500: '#7473b6', // your base color
                    600: '#615fa0',
                    700: '#4e4c84',
                    800: '#423f6d',
                    900: '#363356',
                    950: '#201f39',
                }
            },
        },
    },

    plugins: [forms],
};
