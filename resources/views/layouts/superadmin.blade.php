<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Super Admin — {{ config('app.name', 'ShoeClean') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --sa-primary: #1e1b4b;
                --sa-secondary: #6366f1;
                --sa-accent: #818cf8;
                --sa-surface: #f1f0fb;
                --sa-surface-low: #e8e7f5;
                --sa-bg: #f8f7ff;
            }
        </style>
    </head>
    <body class="antialiased font-sans" style="background-color: var(--sa-bg); color: var(--sa-primary);" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex relative overflow-hidden">
            <!-- Background Noise Overlay -->
            <div class="bg-noise absolute inset-0 opacity-[0.02] pointer-events-none"></div>

            <!-- Sidebar Overlay (Mobile) -->
            <div 
                x-show="sidebarOpen" 
                x-cloak
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 backdrop-blur-sm z-40 lg:hidden"
                style="background-color: rgba(30, 27, 75, 0.4);"
            ></div>

            <!-- Sidebar -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 w-72 z-50 lg:translate-x-0 lg:static lg:z-auto transition-transform duration-500 ease-in-out flex flex-col shadow-2xl"
                style="background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%); color: white;"
            >
                <!-- Logo -->
                <div class="p-8 mb-4">
                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-4 group">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-white backdrop-blur-md group-hover:bg-indigo-500 transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <span class="text-xl font-manrope font-extrabold tracking-tighter uppercase whitespace-nowrap">ShoeClean<span class="text-indigo-400">.</span></span>
                            <p class="text-[9px] font-black uppercase tracking-[0.3em] text-indigo-300/60">Super Admin</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 space-y-2 overflow-y-auto artisan-scrollbar">
                    <div class="pb-4">
                        <p class="px-5 text-[10px] font-black uppercase tracking-[0.3em] text-white/30 mb-4">Platform</p>
                        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all duration-300 {{ request()->routeIs('superadmin.dashboard') ? 'bg-white/10 text-white' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('superadmin.orders.index') }}" class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all duration-300 {{ request()->routeIs('superadmin.orders.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Order Platform
                        </a>
                        <a href="{{ route('superadmin.subscriptions.index') }}" class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all duration-300 {{ request()->routeIs('superadmin.subscriptions.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-8V7m0 1v8m0 0v1m0-1c-2.21 0-4-.895-4-2m8-8a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Langganan
                        </a>
                    </div>

                    <div class="py-4">
                        <p class="px-5 text-[10px] font-black uppercase tracking-[0.3em] text-white/30 mb-4">Fitur</p>
                        <a href="{{ route('superadmin.surveys.index') }}" class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all duration-300 {{ request()->routeIs('superadmin.surveys.*') ? 'bg-white/10 text-white' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            Survey Platform
                        </a>
                        <a href="{{ route('superadmin.feedbacks.index') }}" class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all duration-300 {{ request()->routeIs('superadmin.feedbacks.*') ? 'bg-white/10 text-white' : 'text-amber-400 hover:bg-amber-400/10 hover:text-amber-300' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            Feedback Terbaru
                        </a>
                    </div>

                    <div class="py-4">
                        <p class="px-5 text-[10px] font-black uppercase tracking-[0.3em] text-white/30 mb-4">Segera Hadir</p>
                        <span class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm text-white/30 cursor-not-allowed">
                            <svg class="w-5 h-5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Manajemen Outlet
                        </span>
                        <span class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm text-white/30 cursor-not-allowed">
                            <svg class="w-5 h-5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan
                        </span>
                    </div>
                </nav>

                <!-- User Section -->
                <div class="p-6 bg-white/5 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center text-white font-manrope font-extrabold shadow-lg border-2 border-white/10">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-manrope font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-indigo-300/60 font-black uppercase tracking-widest">Super Admin</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-white/30 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 relative" style="background-color: var(--sa-bg);">
                <!-- Top Nav -->
                <header class="flex items-center justify-between px-8 h-24 lg:h-32">
                    <div class="lg:hidden">
                        <button @click="sidebarOpen = true" class="p-3 bg-white rounded-xl shadow-lg" style="color: var(--sa-primary);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                    
                    <div class="hidden lg:flex items-center gap-3">
                        <div class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest" style="background-color: var(--sa-surface-low); color: var(--sa-secondary);">
                            <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Platform Control Panel
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('profile') }}" class="w-12 h-12 bg-white rounded-xl shadow-lg flex items-center justify-center transition-all hover:shadow-xl" style="color: var(--sa-secondary);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 px-8 pb-12 overflow-y-auto artisan-scrollbar">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
