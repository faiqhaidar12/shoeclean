<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Shoe Clean') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="dashboard-surface text-artisan-primary antialiased font-sans"
        x-data="{
            sidebarOpen: false,
            lockedFeature: null,
            openLockedFeature(title, description) {
                this.lockedFeature = { title, description };
            },
            closeLockedFeature() {
                this.lockedFeature = null;
            }
        }"
    >
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
                class="fixed inset-0 bg-artisan-primary/40 backdrop-blur-sm z-40 lg:hidden"
            ></div>

            <!-- Sidebar -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 w-72 overflow-hidden bg-artisan-primary text-white z-50 lg:translate-x-0 lg:static lg:z-auto transition-transform duration-500 ease-in-out flex flex-col shadow-artisan-lg"
            >
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(129,242,235,0.22),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(255,255,255,0.08),transparent_32%)] pointer-events-none"></div>
                <!-- Logo -->
                <div class="relative p-8 mb-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-4 group">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white backdrop-blur-md group-hover:bg-artisan-secondary group-hover:text-artisan-primary transition-colors duration-300 shadow-artisan-sm border border-white/10">
                             <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <span class="text-2xl font-manrope font-extrabold tracking-tighter uppercase whitespace-nowrap">ShoeClean<span class="text-artisan-secondary">.</span></span>
                            <p class="mt-1 text-[9px] font-black uppercase tracking-[0.26em] text-white/35">Dasbor Operasional</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="relative flex-1 px-4 space-y-2 overflow-y-auto artisan-scrollbar">
                    <div class="pb-4">
                        <p class="px-5 text-[10px] font-black uppercase tracking-[0.3em] text-white/30 mb-4">Ringkasan Menu</p>
                        <a href="{{ route('dashboard') }}" class="nav-link-artisan {{ request()->routeIs('dashboard') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </a>

                        @if(auth()->user()->isOwner())
                            <a href="{{ route('outlets.index') }}" class="nav-link-artisan {{ request()->routeIs('outlets.*') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Cabang Outlet
                            </a>
                            <a href="{{ route('subscription') }}" class="nav-link-artisan {{ request()->routeIs('subscription') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Langganan
                            </a>
                        @endif
                    </div>

                    @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                        <div class="py-4">
                            <p class="px-5 text-[10px] font-black uppercase tracking-[0.3em] text-white/30 mb-4">Manajemen</p>
                            
                            <a href="{{ route('services.index') }}" class="nav-link-artisan {{ request()->routeIs('services.*') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Layanan
                            </a>

                            @if(auth()->user()->hasFeature('team_management'))
                                <a href="{{ route('users.index') }}" class="nav-link-artisan {{ request()->routeIs('users.*') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    Staf Outlet
                                </a>
                            @else
                                <button
                                    type="button"
                                    @click="openLockedFeature('Kelola Staf Terkunci', 'Kelola admin dan staf outlet tersedia mulai paket Pro. Upgrade saat Anda siap menambah tim dan membagi operasional lebih rapi.')"
                                    class="nav-link-artisan w-full justify-between text-white/60 hover:text-white"
                                >
                                    <span class="flex items-center gap-3">
                                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <span>Staf Outlet</span>
                                    </span>
                                    <span class="rounded-full bg-amber-400/20 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-amber-200">Pro</span>
                                </button>
                            @endif

                            <a href="{{ route('expenses.index') }}" class="nav-link-artisan {{ request()->routeIs('expenses.*') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Pengeluaran
                            </a>

                            @if(auth()->user()->hasFeature('promos'))
                                <a href="{{ route('promos.index') }}" class="nav-link-artisan {{ request()->routeIs('promos.*') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                    Promo
                                </a>
                            @else
                                <button
                                    type="button"
                                    @click="openLockedFeature('Fitur Promo Terkunci', 'Buat kode promo dan voucher pelanggan tersedia mulai paket Pro. Cocok saat Anda sudah siap menjalankan campaign dan diskon outlet.')"
                                    class="nav-link-artisan w-full justify-between text-white/60 hover:text-white"
                                >
                                    <span class="flex items-center gap-3">
                                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                        <span>Promo</span>
                                    </span>
                                    <span class="rounded-full bg-amber-400/20 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-amber-200">Pro</span>
                                </button>
                            @endif

                            @if(auth()->user()->isOwner())
                                <a href="{{ route('surveys.index') }}" class="nav-link-artisan {{ request()->routeIs('surveys.*') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    Survey Outlet
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="py-4">
                        <p class="px-5 text-[10px] font-black uppercase tracking-[0.3em] text-white/30 mb-4">Operasional</p>
                        
                        <a href="{{ route('customers.index') }}" class="nav-link-artisan {{ request()->routeIs('customers.*') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Pelanggan
                        </a>

                        <a href="{{ route('orders.index') }}" class="nav-link-artisan {{ request()->routeIs('orders.index') || request()->routeIs('orders.view') ? 'active bg-white/10 text-white' : 'text-white/60 hover:text-white' }} group/nav justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Pesanan</span>
                            </div>
                            
                            @if(isset($processingOrdersCount) && $processingOrdersCount > 0)
                                <span class="flex items-center justify-center min-w-[20px] h-5 px-1.5 bg-artisan-secondary text-[10px] font-black text-white rounded-full shadow-lg shadow-artisan-secondary/20 group-hover/nav:scale-110 transition-transform duration-300">
                                    {{ $processingOrdersCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('orders.create') }}" class="flex items-center gap-3 px-5 py-4 bg-artisan-secondary text-white font-bold rounded-2xl hover:bg-white hover:text-artisan-primary transition-all shadow-artisan mt-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Restorasi Baru
                        </a>

                        @if(!auth()->user()->isSuperAdmin())
                            <button @click="$dispatch('open-feedback-modal')" class="w-full flex items-center gap-3 px-5 py-4 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 font-bold rounded-2xl transition-all mt-3 group">
                                <svg class="w-5 h-5 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Kritik & Saran
                            </button>
                        @else
                            <a href="{{ route('superadmin.dashboard') }}#latest-feedbacks" class="w-full flex items-center gap-3 px-5 py-4 bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white font-bold rounded-2xl transition-all mt-3 group shadow-lg shadow-amber-500/5">
                                <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                Feedback Terbaru
                            </a>
                        @endif
                    </div>
                </nav>

                <!-- User Section -->
                <div class="relative p-6 border-t border-white/10 bg-white/5 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-artisan-secondary rounded-full flex items-center justify-center text-white font-manrope font-extrabold shadow-artisan border-2 border-white/10">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-manrope font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-white/40 font-black uppercase tracking-widest">{{ auth()->user()->roles->first()?->slug ?? 'Artisan' }}</p>
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
            <div class="flex-1 flex min-w-0 flex-col bg-transparent relative">
                <!-- Top Nav / Headless Header -->
                <header class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-24 lg:h-32">
                    <div class="lg:hidden">
                        <button @click="sidebarOpen = true" class="p-3 bg-white rounded-2xl shadow-artisan text-artisan-primary border border-artisan-outline/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                    
                    <div class="hidden lg:flex items-center gap-6">
                        @if(auth()->user()->isOwner())
                             <livewire:outlet-switcher />
                        @endif
                    </div>

                    <div class="dashboard-glass flex items-center gap-4 rounded-[1.6rem] px-3 py-3">
                        <a href="{{ route('profile') }}" class="w-12 h-12 bg-white rounded-2xl shadow-artisan-sm flex items-center justify-center text-artisan-secondary hover:text-artisan-primary transition-all border border-artisan-outline/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto artisan-scrollbar">
                    <div class="dashboard-page-shell">
                        {{ $slot }}
                    </div>
                </main>
            </div>
            
            <livewire:feedback-modal />

            <div
                x-show="lockedFeature"
                x-cloak
                x-transition.opacity
                @keydown.escape.window="closeLockedFeature()"
                class="fixed inset-0 z-[70] flex items-center justify-center p-4"
            >
                <div class="absolute inset-0 bg-artisan-primary/55 backdrop-blur-sm" @click="closeLockedFeature()"></div>

                <div class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-white shadow-2xl">
                    <div class="bg-gradient-to-br from-artisan-primary via-artisan-primary to-artisan-secondary px-6 py-6 text-white">
                        <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-manrope font-extrabold" x-text="lockedFeature?.title"></h3>
                        <p class="mt-2 text-sm leading-6 text-white/80" x-text="lockedFeature?.description"></p>
                    </div>

                    <div class="space-y-4 px-6 py-6">
                        <div class="rounded-2xl border border-artisan-primary/10 bg-artisan-surface-soft px-4 py-4 text-sm text-artisan-primary/70">
                            Fitur ini tetap terlihat supaya Anda bisa tahu apa yang akan terbuka saat upgrade. Operasional inti tetap bisa dipakai seperti biasa.
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            @if(auth()->user()->isOwner())
                                <a href="{{ route('subscription') }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-artisan-primary px-5 py-3 text-sm font-bold text-white transition hover:bg-artisan-secondary">
                                    Lihat Paket Upgrade
                                </a>
                            @endif
                            <button type="button" @click="closeLockedFeature()" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-artisan-primary/10 px-5 py-3 text-sm font-bold text-artisan-primary transition hover:bg-artisan-surface-soft">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
