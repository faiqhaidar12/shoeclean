<div
    x-data="{ open: false }"
    class="flex flex-1 items-center justify-end gap-3"
>
    <div class="hidden items-center gap-6 lg:flex">
        <a href="/" class="public-nav-link {{ request()->routeIs('home') ? 'public-nav-link-active' : '' }}">Beranda</a>
        <a href="{{ route('pricing') }}" class="public-nav-link {{ request()->routeIs('pricing') ? 'public-nav-link-active' : '' }}">Harga</a>
        <a href="{{ route('track') }}" class="public-nav-link {{ request()->routeIs('track') ? 'public-nav-link-active' : '' }}">Lacak</a>
        <a href="{{ route('public.order.select') }}" class="public-nav-link {{ request()->routeIs('public.order*') ? 'public-nav-link-active' : '' }}">Pesan</a>
    </div>

    @auth
        <a href="{{ url('/dashboard') }}" class="btn-primary hidden sm:inline-flex">
            Buka Dasbor
        </a>
        <a href="{{ url('/dashboard') }}" class="btn-primary sm:hidden">
            Dasbor
        </a>
    @else
        <div class="hidden items-center gap-3 sm:flex">
            <a href="{{ route('login') }}" class="btn-secondary">
                Masuk
            </a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary">
                    Daftar
                </a>
            @endif
        </div>
    @endauth

    <button
        type="button"
        @click="open = !open"
        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-artisan-outline/15 bg-white text-artisan-primary shadow-artisan-sm lg:hidden"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M4 6h16M4 12h16M4 18h16"/>
            <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M6 6l12 12M18 6L6 18"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        class="absolute inset-x-4 top-full mt-3 rounded-[1.75rem] border border-artisan-outline/10 bg-white/95 p-4 shadow-artisan lg:hidden"
    >
        <div class="grid gap-2">
            <a href="/" class="btn-secondary justify-start {{ request()->routeIs('home') ? '!border-artisan-secondary/30 !bg-artisan-surface-low' : '' }}">Beranda</a>
            <a href="{{ route('pricing') }}" class="btn-secondary justify-start {{ request()->routeIs('pricing') ? '!border-artisan-secondary/30 !bg-artisan-surface-low' : '' }}">Harga</a>
            <a href="{{ route('track') }}" class="btn-secondary justify-start {{ request()->routeIs('track') ? '!border-artisan-secondary/30 !bg-artisan-surface-low' : '' }}">Lacak Pesanan</a>
            <a href="{{ route('public.order.select') }}" class="btn-secondary justify-start {{ request()->routeIs('public.order*') ? '!border-artisan-secondary/30 !bg-artisan-surface-low' : '' }}">Pesan Layanan</a>

            @guest
                <div class="mt-2 grid gap-2">
                    <a href="{{ route('login') }}" class="btn-secondary justify-start">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary justify-start">Daftar</a>
                    @endif
                </div>
            @endguest
        </div>
    </div>
</nav>
