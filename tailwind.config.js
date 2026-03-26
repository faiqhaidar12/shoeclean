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
                    primary: '#16352D',      // Ink / Deep Green
                    secondary: '#2F7A67',    // Action Sage
                    tertiary: '#102521',     // Deep accent
                    background: '#F7F4EE',   // Warm background
                    bg: '#F7F4EE',           // Background duplicate for convenience
                    surface: {
                        low: '#F1ECE4',      // Surface container low
                        lowest: '#FFFFFF',   // Surface container lowest
                        variant: '#E2DDD4'   // Surface variant
                    },
                    outline: '#D6DDD8',      // Outline variant
                }
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(22, 53, 45, 0.08), 0 10px 20px -2px rgba(22, 53, 45, 0.05)',
                'artisan': '0px 12px 32px rgba(22, 53, 45, 0.08)',
                'artisan-lg': '0px 24px 64px rgba(22, 53, 45, 0.12)',
                'artisan-sm': '0px 4px 12px rgba(22, 53, 45, 0.05)',
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
