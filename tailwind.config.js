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
                display: ['Inter', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#0F766E',
                    hover: '#115E59',
                    soft: '#14B8A6',
                    light: '#99F6E4',
                    dark: '#2DD4BF',
                },
                accent: {
                    DEFAULT: '#2563EB',
                    hover: '#1D4ED8',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    hover: '#F8FAFC',
                    raised: '#FFFFFF',
                },
                night: {
                    DEFAULT: '#F8FAFC',
                    deep: '#FFFFFF',
                    card: '#FFFFFF',
                },
                danger: '#DC2626',
                success: '#15803D',
                warning: '#D97706',
                info: '#2563EB',
            },
            boxShadow: {
                glass: '0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06)',
                'glass-lg': '0 4px 6px -2px rgba(15, 23, 42, 0.05), 0 12px 24px -6px rgba(15, 23, 42, 0.10)',
                card: '0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px -12px rgba(15, 23, 42, 0.10)',
                'card-hover': '0 12px 32px -16px rgba(15, 23, 42, 0.16)',
                glow: '0 0 0 1px rgba(15, 118, 110, 0.16), 0 0 10px rgba(15, 118, 110, 0.08)',
                'glow-cyan': '0 0 0 1px rgba(37, 99, 235, 0.16)',
                'glow-rose': '0 0 0 1px rgba(220, 38, 38, 0.16)',
                'inset-hairline': 'inset 0 1px 0 rgba(15, 23, 42, 0.04)',
            },
            borderRadius: {
                glass: '0.75rem',
                'glass-sm': '0.5rem',
                'glass-lg': '0.875rem',
                'glass-xl': '1rem',
                'glass-full': '9999px',
            },
            animation: {
                'fade-up': 'fadeUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) both',
                'fade-in': 'fadeIn 0.6s ease-out both',
                shimmer: 'shimmer 2.5s linear infinite',
                'pulse-soft': 'pulseSoft 2.5s ease-in-out infinite',
            },
            keyframes: {
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
