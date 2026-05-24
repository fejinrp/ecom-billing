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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Redefining standard Tailwind colors to dynamically resolve via CSS custom properties
                slate: {
                    50: 'var(--color-slate-50)',
                    100: 'var(--color-slate-100)',
                    200: 'var(--color-slate-200)',
                    300: 'var(--color-slate-300)',
                    400: 'var(--color-slate-400)',
                    500: 'var(--color-slate-500)',
                    600: 'var(--color-slate-600)',
                    700: 'var(--color-slate-700)',
                    750: 'var(--color-slate-750)',
                    800: 'var(--color-slate-800)',
                    850: 'var(--color-slate-850)',
                    900: 'var(--color-slate-900)',
                    950: 'var(--color-slate-950)',
                },
                // Indigo is the primary Brand Blue (Logo Blue: #003BFF)
                indigo: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#3b82f6', // Ensure readable text-indigo-400
                    500: '#003BFF', // Exact Logo Royal Blue
                    600: '#0033dd', // Hover state
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                // Purple is the Brand Orange (Logo Orange: #ff6b00)
                purple: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#f97316', // Ensure readable text-purple-400 (Vibrant Orange)
                    500: '#ff6b00', // Exact Logo Orange
                    600: '#ea580c', // Hover state
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                    950: '#431407',
                },
                // Status Colors - Dynamically Adaptive
                emerald: {
                    50: 'var(--color-emerald-50)',
                    100: 'var(--color-emerald-100)',
                    200: 'var(--color-emerald-200)',
                    300: 'var(--color-emerald-300)',
                    400: 'var(--color-emerald-400)',
                    500: 'var(--color-emerald-500)',
                    600: 'var(--color-emerald-600)',
                    700: 'var(--color-emerald-700)',
                    800: 'var(--color-emerald-800)',
                    900: 'var(--color-emerald-900)',
                    950: 'var(--color-emerald-950)',
                },
                amber: {
                    50: 'var(--color-amber-50)',
                    100: 'var(--color-amber-100)',
                    200: 'var(--color-amber-200)',
                    300: 'var(--color-amber-300)',
                    400: 'var(--color-amber-400)',
                    500: 'var(--color-amber-500)',
                    600: 'var(--color-amber-600)',
                    700: 'var(--color-amber-700)',
                    800: 'var(--color-amber-800)',
                    900: 'var(--color-amber-900)',
                    950: 'var(--color-amber-950)',
                },
                cyan: {
                    50: 'var(--color-cyan-50)',
                    100: 'var(--color-cyan-100)',
                    200: 'var(--color-cyan-200)',
                    300: 'var(--color-cyan-300)',
                    400: 'var(--color-cyan-400)',
                    500: 'var(--color-cyan-500)',
                    600: 'var(--color-cyan-600)',
                    700: 'var(--color-cyan-700)',
                    800: 'var(--color-cyan-800)',
                    900: 'var(--color-cyan-900)',
                    950: 'var(--color-cyan-950)',
                },
                red: {
                    50: 'var(--color-red-50)',
                    100: 'var(--color-red-100)',
                    200: 'var(--color-red-200)',
                    300: 'var(--color-red-300)',
                    400: 'var(--color-red-400)',
                    500: 'var(--color-red-500)',
                    600: 'var(--color-red-600)',
                    700: 'var(--color-red-700)',
                    800: 'var(--color-red-800)',
                    900: 'var(--color-red-900)',
                    950: 'var(--color-red-950)',
                },
                rose: {
                    50: 'var(--color-rose-50)',
                    100: 'var(--color-rose-100)',
                    200: 'var(--color-rose-200)',
                    300: 'var(--color-rose-300)',
                    400: 'var(--color-rose-400)',
                    500: 'var(--color-rose-500)',
                    600: 'var(--color-rose-600)',
                    700: 'var(--color-rose-700)',
                    800: 'var(--color-rose-800)',
                    900: 'var(--color-rose-900)',
                    950: 'var(--color-rose-950)',
                }
            },
        },
    },

    plugins: [forms],
};
