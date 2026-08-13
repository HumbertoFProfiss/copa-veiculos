import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                bg: '#FFFFFF',
                surface: '#F7F9FC',
                border: '#E4E9F0',
                'text-primary': '#0F172A',
                'text-secondary': '#64748B',
                primary: {
                    DEFAULT: '#1D4ED8',
                    light: '#3B82F6',
                    soft: '#EFF6FF',
                },
                success: '#059669',
                warning: '#D97706',
                error: '#DC2626',
            },
            borderRadius: {
                card: '10px',
                control: '8px',
            },
            boxShadow: {
                soft: '0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06)',
                'soft-md': '0 4px 8px -2px rgba(15, 23, 42, 0.06), 0 2px 4px -2px rgba(15, 23, 42, 0.05)',
                'soft-lg': '0 12px 24px -8px rgba(15, 23, 42, 0.10), 0 4px 8px -4px rgba(15, 23, 42, 0.06)',
            },
        },
    },

    plugins: [forms],
};
