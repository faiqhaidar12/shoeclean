<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ShoeClean') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
            }
            
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            .glass-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
            }
            
            .float-animation {
                animation: float 6s ease-in-out infinite;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
            
            .input-focus:focus {
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen gradient-bg flex items-center justify-center p-4">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-300/20 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-pink-300/10 rounded-full blur-3xl"></div>
            </div>
            
            <div class="w-full max-w-md relative">
                <!-- Logo & Branding -->
                <div class="text-center mb-8 float-animation">
                    <a href="/" wire:navigate class="inline-block">
                        <div class="w-20 h-20 mx-auto bg-white rounded-2xl shadow-2xl flex items-center justify-center mb-4">
                            <span class="text-4xl">👟</span>
                        </div>
                    </a>
                    <h1 class="text-3xl font-bold text-white drop-shadow-lg">ShoeClean</h1>
                    <p class="text-white/80 mt-2">Laundry Management System</p>
                </div>
                
                <!-- Login Card -->
                <div class="glass-card rounded-3xl shadow-2xl p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Welcome Back! 👋</h2>
                        <p class="text-gray-500 mt-2">Sign in to continue to your dashboard</p>
                    </div>
                    
                    {{ $slot }}
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-8">
                    <p class="text-white/60 text-sm">
                        &copy; {{ date('Y') }} ShoeClean. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
