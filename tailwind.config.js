import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
                heading: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                bg: '#0A0A0A',
                surface: '#161616',
                border: '#2A2A2A',
                'text-primary': '#FFFFFF',
                'text-secondary': '#C0C0C0',
                primary: {
                    DEFAULT: '#B8860B',
                    light: '#D4AF37',
                    soft: '#F7F0DC',
                },
                success: '#059669',
                warning: '#D97706',
                error: '#DC2626',

                // Modo escuro do site publico (home/estoque/veiculo/area do cliente) -
                // alternado via classe .dark na tag <html> (ver components/layouts/public.blade.php).
                // Temporario: sobrescrito localmente so pro prototipo Rei Motors
                // (preto/dourado, ver .backup_visual_copa pra restaurar o Copa Veiculos).
                duskbg: '#0A0A0A',
                duskcard: '#161616',
                duskborder: '#2A2A2A',
                duskmuted: '#808080',

                // Paleta Rei Motors (preto/dourado) - sobrescrita temporaria local pro
                // prototipo. Original (azul, projeto Dra Jaqueline) em .backup_visual_copa.
                brand: {
                    900: '#0A0A0A',
                    700: '#B8860B',
                    500: '#D4AF37',
                    300: '#E8C766',
                    100: '#F7F0DC',
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
                brand: '0 24px 60px -24px rgba(184, 134, 11, 0.35)',
            },
        },
    },

    plugins: [forms],
};
