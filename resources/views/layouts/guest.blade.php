<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ShoeClean') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
            .bg-noise { background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
        </style>
    </head>
    <body class="antialiased bg-artisan-background text-artisan-primary selection:bg-artisan-secondary/20 font-sans relative overflow-x-hidden min-h-screen">
        <div class="bg-noise absolute inset-0 opacity-[0.03] pointer-events-none mix-blend-overlay"></div>
        <div class="absolute -top-[500px] -right-[500px] w-[1000px] h-[1000px] bg-artisan-secondary/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 p-6 relative z-10">
            <div class="mb-12 text-center">
                <a href="/" wire:navigate class="inline-flex items-center gap-4 group">
                    <div class="w-14 h-14 bg-artisan-primary rounded-2xl flex items-center justify-center text-white shadow-artisan group-hover:bg-artisan-secondary transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <span class="text-3xl font-manrope font-extrabold tracking-tighter uppercase whitespace-nowrap text-artisan-primary">ShoeClean<span class="text-artisan-secondary italic">.</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-12 bg-white/70 backdrop-blur-3xl shadow-artisan-lg border border-white rounded-[2.5rem] overflow-hidden relative">
                {{ $slot }}
            </div>
            
            <p class="mt-12 text-[10px] font-black uppercase tracking-[0.5em] text-artisan-primary/30">
                &copy; {{ date('Y') }} Sistem Preservasi Artisan.
            </p>
        </div>
    </body>
</html>
