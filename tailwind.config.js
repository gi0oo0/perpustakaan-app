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
                display: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#6d5cff',
                    hover: '#7f6cff',
                    soft: '#8b7bff',
                    light: '#a78bfa',
                    dark: '#5444e0',
                },
                accent: {
                    DEFAULT: '#22d3ee',
                    hover: '#67e8f9',
                },
                violet: {
                    DEFAULT: '#a855f7',
                    soft: '#c084fc',
                },
                night: {
                    DEFAULT: '#070b16',
                    deep: '#04060d',
                    card: '#0b1220',
                },
                danger: '#fb5e63',
                success: '#34d399',
                warning: '#fbbf24',
                info: '#38bdf8',
            },
            boxShadow: {
                glass: '0 8px 32px rgba(0, 0, 0, 0.35)',
                'glass-lg': '0 24px 70px rgba(0, 0, 0, 0.5)',
                glow: '0 0 28px rgba(109, 92, 255, 0.35)',
                'glow-cyan': '0 0 28px rgba(34, 211, 238, 0.3)',
                'glow-rose': '0 0 28px rgba(244, 114, 182, 0.3)',
                'inset-hairline': 'inset 0 1px 0 rgba(255, 255, 255, 0.06)',
            },
            borderRadius: {
                glass: '1.25rem',
                'glass-sm': '0.875rem',
                'glass-lg': '1.75rem',
                'glass-xl': '2rem',
                'glass-full': '9999px',
            },
            animation: {
                float: 'float 9s ease-in-out infinite',
                'float-slow': 'float 14s ease-in-out infinite',
                'float-fast': 'float 6s ease-in-out infinite',
                'fade-up': 'fadeUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) both',
                'fade-in': 'fadeIn 0.6s ease-out both',
                shimmer: 'shimmer 2.5s linear infinite',
                'pulse-soft': 'pulseSoft 2.5s ease-in-out infinite',
                'spin-slow': 'spin 8s linear infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '50%': { transform: 'translate(20px, -30px) scale(1.08)' },
                },
                fadeUp: {
                    from: { opacity: '0', transform: 'translateY(18px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
                fadeIn: {
                    from: { opacity: '0' },
                    to: { opacity: '1' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '0.55' },
                    '50%': { opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
