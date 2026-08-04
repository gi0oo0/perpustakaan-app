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
                display: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Helvetica Neue', 'sans-serif'],
                body: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Helvetica Neue', 'sans-serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#0071E3',
                    hover: '#006EDB',
                    active: '#0076DF',
                    light: '#0077ED',
                    50: '#F0F7FF',
                    100: '#D6EAFF',
                },
                text: {
                    DEFAULT: '#1D1D1F',
                    black: '#000000',
                    secondary: '#333336',
                    tertiary: '#6E6E73',
                    dark: '#272729',
                    darkest: '#18181A',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    light: '#FAFAFC',
                    lighter: '#EDEDF2',
                },
                danger: '#FF3B30',
                success: '#34C759',
                warning: '#FF9500',
                info: '#0071E3',
            },
            boxShadow: {
                'apple': '0px 2px 8px rgba(0, 0, 0, 0.08)',
                'apple-raised': '0px 4px 16px rgba(0, 0, 0, 0.12)',
                'apple-overlay': '0px 8px 32px rgba(0, 0, 0, 0.16)',
            },
            borderRadius: {
                'apple': '8px',
                'apple-lg': '28px',
                'apple-xl': '20px',
            },
            spacing: {
                'apple-xs': '8px',
                'apple-sm': '12px',
                'apple-md': '16px',
                'apple-lg': '20px',
                'apple-xl': '24px',
                'apple-2xl': '28px',
                'apple-3xl': '32px',
                'apple-4xl': '36px',
                'apple-5xl': '40px',
                'apple-6xl': '44px',
                'apple-7xl': '84px',
                'apple-8xl': '88px',
            },
            fontSize: {
                'display-xl': ['5rem', { lineHeight: '5.25rem', fontWeight: '600' }],
                'display-lg': ['4rem', { lineHeight: '4.25rem', fontWeight: '600' }],
                'display-md': ['3rem', { lineHeight: '3.25rem', fontWeight: '600' }],
                'heading-xl': ['2.5rem', { lineHeight: '2.75rem', fontWeight: '600' }],
                'heading-lg': ['1.75rem', { lineHeight: '2rem', fontWeight: '600' }],
                'heading-md': ['1.5rem', { lineHeight: '1.5rem', fontWeight: '600' }],
                'heading-sm': ['1.1875rem', { lineHeight: '1.4375rem', fontWeight: '600' }],
                'heading-xs': ['0.875rem', { lineHeight: '1.1875rem', fontWeight: '600' }],
                'body-lg': ['1.75rem', { lineHeight: '2rem', fontWeight: '400' }],
                'body-md': ['1.3125rem', { lineHeight: '1.8125rem', fontWeight: '400' }],
                'body-sm': ['1.0625rem', { lineHeight: '1.5625rem', fontWeight: '400' }],
                'body-xs': ['0.875rem', { lineHeight: '1.125rem', fontWeight: '400' }],
                'caption': ['0.75rem', { lineHeight: '1rem', fontWeight: '400' }],
            },
        },
    },

    plugins: [forms],
};
