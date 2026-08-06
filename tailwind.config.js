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
                display: ['"Source Serif 4"', 'Georgia', 'Cambria', '"Times New Roman"', 'serif'],
                body: ['Inter', 'UntitledSans', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#3B82F6',
                    hover: '#339FFF',
                    active: '#2563EB',
                    light: '#B8CAF5',
                    50: '#F0F6FF',
                    100: '#DBEAFE',
                },
                text: {
                    DEFAULT: '#030302',
                    black: '#000000',
                    secondary: '#1F2225',
                    tertiary: '#6E6E6A',
                    dark: '#16181A',
                    darkest: '#0F1011',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    light: '#F7F7F7',
                    lighter: '#E5E7EB',
                },
                danger: '#E5484D',
                success: '#4AA25E',
                warning: '#F5A623',
                info: '#3B82F6',
                'apple-blue': '#3B82F6',
            },
            boxShadow: {
                'apple': '0px 1px 3px rgba(3, 3, 2, 0.08), 0px 1px 2px -1px rgba(3, 3, 2, 0.08)',
                'apple-raised': '0px 4px 12px rgba(3, 3, 2, 0.12)',
                'apple-overlay': '0px 10px 15px -3px rgba(3, 3, 2, 0.10), 0px 4px 6px -4px rgba(3, 3, 2, 0.10)',
                'apple-lg': '0px 20px 40px rgba(3, 3, 2, 0.08), 0px 3px 10px rgba(3, 3, 2, 0.06)',
            },
            borderRadius: {
                'apple': '8px',
                'apple-sm': '8px',
                'apple-md': '16px',
                'apple-lg': '24px',
                'apple-xl': '16px',
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
                'display-xl': ['5rem', { lineHeight: '5.25rem', fontWeight: '400' }],
                'display-lg': ['4rem', { lineHeight: '4.25rem', fontWeight: '400' }],
                'display-md': ['3rem', { lineHeight: '3.25rem', fontWeight: '400' }],
                'heading-xl': ['2.5rem', { lineHeight: '2.75rem', fontWeight: '400' }],
                'heading-lg': ['1.75rem', { lineHeight: '2rem', fontWeight: '500' }],
                'heading-md': ['1.5rem', { lineHeight: '1.5rem', fontWeight: '500' }],
                'heading-sm': ['1.1875rem', { lineHeight: '1.4375rem', fontWeight: '500' }],
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
