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
                heading: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
                body: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#0D9488',
                    50: '#F0FDFA',
                    100: '#CCFBF1',
                    500: '#0D9488',
                    600: '#0B7A6E',
                    700: '#095F56',
                },
                surface: '#FFFFFF',
                muted: '#6B7280',
                coral: '#FF6B6B',
                lemon: '#FDE047',
                border: '#111827',
            },
            boxShadow: {
                'neo': '6px 6px 0px #111827',
                'neo-sm': '4px 4px 0px #111827',
                'neo-hover': '10px 10px 0px #111827',
                'neo-press': '2px 2px 0px #111827',
            },
            borderWidth: {
                '3': '3px',
            },
            borderRadius: {
                'none': '0px',
            },
        },
    },

    plugins: [forms],
};
