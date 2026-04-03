/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                manrope: ['Manrope', 'sans-serif'],
            },
            colors: {
                primary: {
                    50: '#EEEFFF',
                    600: '#4F46E5',
                },
                artisan: {
                    primary: '#002045',      // Deep navy
                    secondary: '#81F2EB',    // Mint accent
                    tertiary: '#0D2D57',     // Deep accent
                    background: '#F7F4EE',   // Warm ivory background
                    bg: '#F7F4EE',           // Background duplicate for convenience
                    surface: {
                        low: '#F2EEE6',      // Surface container low
                        lowest: '#FFFFFF',   // Surface container lowest
                        variant: '#E4DED2'   // Surface variant
                    },
                    outline: '#D7D2C8',      // Outline variant
                }
            },
            boxShadow: {
                'soft': '0 10px 26px rgba(0, 32, 69, 0.07), 0 2px 8px rgba(0, 32, 69, 0.04)',
                'artisan': '0 18px 46px rgba(0, 32, 69, 0.09)',
                'artisan-lg': '0 28px 72px rgba(0, 32, 69, 0.14)',
                'artisan-sm': '0 6px 16px rgba(0, 32, 69, 0.06)',
            },
            animation: {
                'spin-slow': 'spin 8s linear infinite',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
