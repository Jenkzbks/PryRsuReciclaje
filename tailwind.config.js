import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': {
                    DEFAULT: '#002b5a',
                    50: '#e6f1ff',
                    100: '#cce3ff',
                    200: '#99c7ff',
                    300: '#66abff',
                    400: '#338fff',
                    500: '#0086cd',
                    600: '#006ba3',
                    700: '#005080',
                    800: '#003556',
                    900: '#002b5a',
                    950: '#001a3a',
                },
                'secondary': {
                    DEFAULT: '#0086cd',
                    50: '#e6f6ff',
                    100: '#ccedff',
                    200: '#99dbff',
                    300: '#66c9ff',
                    400: '#33b7ff',
                    500: '#0086cd',
                    600: '#006ba3',
                    700: '#005079',
                    800: '#003556',
                    900: '#001a33',
                    950: '#000d1a',
                },
            },
        },
    },

    plugins: [forms, typography],
};
