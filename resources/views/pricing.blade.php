<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Harga ShoeClean - Paket Free, Pro, dan Business</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-artisan-background text-artisan-primary selection:bg-artisan-secondary/20 font-sans">
        <nav class="sticky top-0 z-50 border-b border-artisan-secondary/10 bg-artisan-background/85 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto flex h-20 items-center justify-between px-6 lg:px-8">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-artisan-primary rounded-xl flex items-center justify-center text-white shadow-artisan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <span class="text-2xl font-manrope font-extrabold tracking-tighter uppercase">ShoeClean<span class="text-artisan-secondary">.</span></span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="/" class="btn-artisan-secondary text-sm px-6 py-3">Kembali</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-artisan-primary text-sm px-6 py-3">Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn-artisan-primary text-sm px-6 py-3">Mulai Gratis</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="py-16 lg:py-24">
            <section class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="rounded-[3rem] bg-gradient-to-br from-artisan-primary via-artisan-primary to-[#143f41] px-8 py-12 text-white shadow-artisan-lg lg:px-12 lg:py-16">
                    <p class="text-[10px] font-black uppercase tracking-[0.32em] text-artisan-secondary">Pricing</p>
                    <h1 class="headline-editorial mt-4 text-4xl lg:text-6xl text-white">Pilih Paket Sesuai Tahap Bisnis Anda.</h1>
                    <p class="mt-5 max-w-3xl text-sm lg:text-lg font-semibold leading-relaxed text-white/78">
                        Mulai dari `Free` untuk mencoba alur operasional, lanjut ke `Pro` untuk 1 outlet yang sudah aktif, atau gunakan `Business` saat Anda mulai mengelola banyak cabang.
                    </p>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 lg:mt-16">
                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                    <article class="card-artisan p-8 flex flex-col">
                        <p class="text-[10px] font-black uppercase tracking-[0.24em] text-artisan-primary/35">{{ $planDetails['free']['subtitle'] }}</p>
                        <h2 class="mt-3 text-3xl font-manrope font-extrabold text-artisan-primary">{{ $planDetails['free']['name'] }}</h2>
                        <p class="mt-3 text-4xl font-manrope font-black italic text-artisan-primary">{{ $planDetails['free']['price_label'] }}</p>
                        <p class="mt-4 text-sm font-semibold leading-relaxed text-artisan-primary/60">{{ $planDetails['free']['description'] }}</p>
                        <ul class="mt-8 space-y-3 text-sm font-semibold text-artisan-primary/70">
                            @foreach($planDetails['free']['features'] as $feature)
                                <li class="rounded-2xl bg-artisan-surface-low/60 px-4 py-3">{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <div class="mt-auto pt-8">
                            <a href="{{ route('register') }}" class="btn-artisan-secondary w-full text-center">Mulai Gratis</a>
                        </div>
                    </article>

                    <article class="card-artisan p-8 flex flex-col border-2 border-blue-100 bg-gradient-to-br from-blue-50 to-indigo-50 relative">
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 px-4 py-1 text-[10px] font-black uppercase tracking-[0.22em] text-white shadow-lg">Paling Cocok</span>
                        <p class="text-[10px] font-black uppercase tracking-[0.24em] text-indigo-500">{{ $planDetails['pro']['subtitle'] }}</p>
                        <h2 class="mt-3 text-3xl font-manrope font-extrabold text-indigo-700">{{ $planDetails['pro']['name'] }}</h2>
                        <p class="mt-3 text-4xl font-manrope font-black italic text-artisan-primary">{{ $planDetails['pro']['price_label'] }}</p>
                        <p class="mt-4 text-sm font-semibold leading-relaxed text-artisan-primary/60">{{ $planDetails['pro']['description'] }}</p>
                        <ul class="mt-8 space-y-3 text-sm font-semibold text-artisan-primary/70">
                            @foreach($planDetails['pro']['features'] as $feature)
                                <li class="rounded-2xl bg-white/80 px-4 py-3 shadow-sm">{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <div class="mt-auto pt-8">
                            @if($planDetails['pro']['is_published'] ?? true)
                                <a href="{{ route('register') }}" class="btn-artisan-primary w-full text-center">Pilih Pro</a>
                            @else
                                <span class="btn-artisan-secondary w-full text-center opacity-70">Coming Soon</span>
                            @endif
                        </div>
                    </article>

                    <article class="card-artisan p-8 flex flex-col border-2 border-purple-100 bg-gradient-to-br from-purple-50 to-pink-50">
                        <p class="text-[10px] font-black uppercase tracking-[0.24em] text-purple-500">{{ $planDetails['business']['subtitle'] }}</p>
                        <h2 class="mt-3 text-3xl font-manrope font-extrabold text-purple-700">{{ $planDetails['business']['name'] }}</h2>
                        <p class="mt-3 text-4xl font-manrope font-black italic text-artisan-primary">{{ $planDetails['business']['price_label'] }}</p>
                        <p class="mt-4 text-sm font-semibold leading-relaxed text-artisan-primary/60">{{ $planDetails['business']['description'] }}</p>
                        <ul class="mt-8 space-y-3 text-sm font-semibold text-artisan-primary/70">
                            @foreach($planDetails['business']['features'] as $feature)
                                <li class="rounded-2xl bg-white/80 px-4 py-3 shadow-sm">{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <div class="mt-auto pt-8">
                            @if($planDetails['business']['is_published'] ?? true)
                                <a href="{{ route('register') }}" class="btn-artisan-primary w-full text-center">Pilih Business</a>
                            @else
                                <span class="btn-artisan-secondary w-full text-center opacity-70">Coming Soon</span>
                            @endif
                        </div>
                    </article>
                </div>
            </section>

            <section class="max-w-6xl mx-auto px-6 lg:px-8 mt-16">
                <div class="rounded-[3rem] bg-artisan-surface-low p-8 lg:p-12">
                    <div class="grid gap-8 lg:grid-cols-3">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary">Kapan Upgrade?</p>
                            <h2 class="headline-editorial mt-4 text-3xl">Gunakan Paket yang Sesuai Kebutuhan Hari Ini.</h2>
                        </div>
                        <div class="rounded-[2rem] bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-manrope font-extrabold text-artisan-primary">Mulai dengan Free</h3>
                            <p class="mt-3 text-sm font-semibold leading-relaxed text-artisan-primary/60">Cocok untuk outlet baru yang ingin mulai mencatat order, menerima QRIS outlet, dan membiasakan tim dengan sistem digital.</p>
                        </div>
                        <div class="rounded-[2rem] bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-manrope font-extrabold text-artisan-primary">Naik ke Pro atau Business</h3>
                            <p class="mt-3 text-sm font-semibold leading-relaxed text-artisan-primary/60">Pilih `Pro` saat 1 outlet Anda sudah aktif. Pilih `Business` saat owner perlu kontrol lintas cabang dan laporan gabungan semua outlet.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-6 lg:px-8 mt-16 lg:mt-20">
                <div class="rounded-[3rem] bg-white p-8 shadow-artisan lg:p-12">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between mb-10">
                        <div class="max-w-2xl">
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary">Mulai dari Cabang</p>
                            <h2 class="headline-editorial mt-4 text-3xl lg:text-4xl">Sudah Punya Outlet yang Aktif? Langsung Arahkan Customer ke Halaman Order.</h2>
                        </div>
                        <div class="max-w-xl space-y-4">
                            <p class="text-sm font-semibold leading-relaxed text-artisan-primary/60">
                                Setiap outlet punya halaman order sendiri. Customer bisa pilih layanan, cek QRIS cabang, lalu kirim pesanan langsung ke outlet yang dipilih.
                            </p>
                            <a href="{{ route('public.order.select') }}" class="inline-flex items-center gap-3 text-sm font-black uppercase tracking-[0.22em] text-artisan-primary hover:text-artisan-secondary transition-colors">
                                Lihat Semua Outlet
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        </div>
                    </div>

                    @if($outlets->isNotEmpty())
                        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($outlets as $outlet)
                                <a href="{{ route('public.order', $outlet) }}" class="rounded-[2rem] border border-artisan-secondary/10 bg-artisan-background px-5 py-6 transition-all duration-300 hover:-translate-y-1 hover:border-artisan-secondary/25 hover:shadow-artisan">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="text-xl font-manrope font-extrabold text-artisan-primary">{{ $outlet->name }}</h3>
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <span class="rounded-full bg-artisan-surface-low px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-secondary">Order</span>
                                            @if($outlet->qris_image_path)
                                                <span class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">QRIS</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold leading-relaxed text-artisan-primary/60">{{ $outlet->address }}</p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <span class="rounded-full bg-white px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-secondary/75 shadow-sm">
                                            {{ $outlet->services_count }} Layanan
                                        </span>
                                        @if($outlet->pickup_fee > 0)
                                            <span class="rounded-full bg-white px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-secondary/75 shadow-sm">
                                                Pickup
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-4 text-[11px] font-black uppercase tracking-[0.22em] text-artisan-secondary/70">{{ $outlet->phone }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-[2rem] border border-dashed border-artisan-secondary/20 bg-artisan-surface-low px-6 py-10 text-center">
                            <p class="text-sm font-semibold leading-relaxed text-artisan-primary/60">
                                Outlet aktif akan tampil di sini agar calon customer bisa langsung masuk ke halaman order cabang terkait.
                            </p>
                        </div>
                    @endif
                </div>
            </section>
        </main>
    </body>
</html>
