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
                    primary: '#0D2C24',      // Ink / Deep Dark Green
                    secondary: '#3a6758',    // Action Mint
                    tertiary: '#051515',     // Ultra-dark accent
                    background: '#faf9f7',   // Background
                    bg: '#faf9f7',           // Background duplicate for convenience
                    surface: {
                        low: '#f4f3f1',      // Surface container low
                        lowest: '#ffffff',   // Surface container lowest
                        variant: '#e3e2e0'   // Surface variant
                    },
                    outline: '#c1c8c4',      // Outline variant
                }
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'artisan': '0px 12px 32px rgba(26, 28, 27, 0.06)',
                'artisan-lg': '0px 24px 64px rgba(26, 28, 27, 0.1)',
                'artisan-sm': '0px 4px 12px rgba(26, 28, 27, 0.04)',
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

