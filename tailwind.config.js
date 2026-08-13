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
                display: ['Poppins', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
                heading: ['Fraunces', ...defaultTheme.fontFamily.serif],
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

                // Paleta da vitrine publica (home) - preto/dourado, igual ao site legado
                // (worldcred.com.br). Escopada com nomes proprios pra nao colidir com
                // os tokens branco/azul usados no painel admin.
                night: {
                    DEFAULT: '#0A0A0A',
                    soft: '#141414',
                    card: '#1F1F1F',
                    border: '#2A2A2A',
                    'border-light': '#353535',
                },
                gold: {
                    DEFAULT: '#E0A526',
                    dark: '#C67C1E',
                },

                // Paleta de referencia trazida pelo usuario (projeto Dra Jaqueline) -
                // aplicada na home publica. Nomes proprios (nao "blue-*") pra nao
                // sobrescrever a escala azul padrao do Tailwind usada em outros lugares.
                brand: {
                    900: '#0B3A5D',
                    700: '#1268A3',
                    500: '#4FA8E8',
                    300: '#7FD3F0',
                    100: '#E8F4FB',
                },
                sand: '#F7F5F1',
                ink: '#1C2A33',
                muted: '#5B6B78',
            },
            borderRadius: {
                card: '10px',
                control: '8px',
            },
            boxShadow: {
                soft: '0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06)',
                'soft-md': '0 4px 8px -2px rgba(15, 23, 42, 0.06), 0 2px 4px -2px rgba(15, 23, 42, 0.05)',
                'soft-lg': '0 12px 24px -8px rgba(15, 23, 42, 0.10), 0 4px 8px -4px rgba(15, 23, 42, 0.06)',
                brand: '0 24px 60px -24px rgba(18, 104, 163, 0.35)',
            },
        },
    },

    plugins: [forms],
};
