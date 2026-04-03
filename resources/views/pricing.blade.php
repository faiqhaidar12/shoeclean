<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Harga | ShoeClean</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="public-shell antialiased">
        <header class="public-topbar">
            <div class="mx-auto flex h-16 max-w-[1440px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="text-2xl font-bold tracking-tighter text-artisan-primary font-manrope">ShoeClean</div>
                @include('livewire.welcome.navigation')
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 pb-24 pt-32 lg:px-8">
            <header class="mb-16 text-center">
                <h1 class="font-manrope text-5xl font-extrabold tracking-tighter text-artisan-primary md:text-6xl">
                    Disesuaikan untuk setiap <span class="text-artisan-secondary">tahap pertumbuhan outlet</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-artisan-primary/60">
                    Alat manajemen presisi yang dirancang untuk membantu bisnis shoe care berkembang dari satu outlet hingga operasi multi-cabang.
                </p>
            </header>

            <div class="grid grid-cols-1 items-stretch gap-8 md:grid-cols-3">
                <div class="flex flex-col rounded-3xl border border-transparent bg-white p-8 shadow-[0_20px_40px_rgba(25,28,30,0.06)] transition-all hover:border-artisan-outline/20">
                    <div class="mb-8">
                        <h3 class="font-manrope text-xl font-bold text-artisan-primary">{{ $planDetails['free']['name'] }}</h3>
                        <p class="mt-2 text-sm text-artisan-primary/55">{{ $planDetails['free']['description'] }}</p>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl font-extrabold tracking-tight text-artisan-primary">{{ $planDetails['free']['price_label'] }}</span>
                        </div>
                    </div>
                    <div class="mb-10 flex-grow space-y-4">
                        @foreach($planDetails['free']['features'] as $feature)
                            <div class="flex items-center gap-3">
                                <span class="h-5 w-5 rounded-full bg-artisan-secondary/20"></span>
                                <span class="text-sm font-medium text-artisan-primary">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('register') }}" class="w-full rounded-full bg-artisan-surface-low py-4 text-center text-sm font-bold text-artisan-primary">Mulai Gratis</a>
                </div>

                <div class="relative z-10 flex scale-105 flex-col overflow-hidden rounded-3xl bg-artisan-primary p-8 text-white shadow-2xl">
                    <div class="absolute right-0 top-0 rounded-bl-2xl bg-artisan-secondary px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.24em] text-artisan-primary">
                        Paling Populer
                    </div>
                    <div class="mb-8">
                        <h3 class="font-manrope text-xl font-bold text-artisan-secondary">{{ $planDetails['pro']['name'] }}</h3>
                        <p class="mt-2 text-sm text-white/70">{{ $planDetails['pro']['description'] }}</p>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl font-extrabold tracking-tight">{{ $planDetails['pro']['price_label'] }}</span>
                        </div>
                    </div>
                    <div class="mb-10 flex-grow space-y-4">
                        @foreach($planDetails['pro']['features'] as $feature)
                            <div class="flex items-center gap-3">
                                <span class="h-5 w-5 rounded-full bg-artisan-secondary"></span>
                                <span class="text-sm font-medium">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if($planDetails['pro']['is_published'] ?? true)
                        <a href="{{ route('register') }}" class="w-full rounded-full bg-artisan-secondary py-4 text-center text-sm font-bold text-artisan-primary shadow-lg">Dapatkan Akses Pro</a>
                    @else
                        <span class="w-full rounded-full bg-white/10 py-4 text-center text-sm font-bold text-white/80">Segera Hadir</span>
                    @endif
                </div>

                <div class="flex flex-col rounded-3xl border border-transparent bg-white p-8 shadow-[0_20px_40px_rgba(25,28,30,0.06)] transition-all hover:border-artisan-outline/20">
                    <div class="mb-8">
                        <h3 class="font-manrope text-xl font-bold text-artisan-primary">{{ $planDetails['business']['name'] }}</h3>
                        <p class="mt-2 text-sm text-artisan-primary/55">{{ $planDetails['business']['description'] }}</p>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl font-extrabold tracking-tight text-artisan-primary">{{ $planDetails['business']['price_label'] }}</span>
                        </div>
                    </div>
                    <div class="mb-10 flex-grow space-y-4">
                        @foreach($planDetails['business']['features'] as $feature)
                            <div class="flex items-center gap-3">
                                <span class="h-5 w-5 rounded-full bg-artisan-secondary/20"></span>
                                <span class="text-sm font-medium text-artisan-primary">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if($planDetails['business']['is_published'] ?? true)
                        <a href="{{ route('register') }}" class="w-full rounded-full bg-artisan-primary py-4 text-center text-sm font-bold text-white">Pilih Business</a>
                    @else
                        <span class="w-full rounded-full bg-artisan-surface-low py-4 text-center text-sm font-bold text-artisan-primary/60">Segera Hadir</span>
                    @endif
                </div>
            </div>

            <section class="mt-32">
                <h2 class="mb-12 text-center font-manrope text-3xl font-bold text-artisan-primary">Setiap detail sudah dipersiapkan untuk operasional outlet.</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-2 rounded-[2rem] bg-artisan-surface-low p-8">
                        <h4 class="mb-2 font-manrope text-xl font-bold text-artisan-primary">Integrasi Operasional yang Tangguh</h4>
                        <p class="text-sm text-artisan-primary/60">Hubungkan alur order, customer, pembayaran, dan laporan dalam satu sistem yang lebih rapi.</p>
                    </div>
                    <div class="md:col-span-2 rounded-[2rem] bg-artisan-secondary/18 p-8">
                        <h4 class="mb-2 font-manrope text-xl font-bold text-artisan-primary">Branding Outlet yang Lebih Meyakinkan</h4>
                        <p class="text-sm text-artisan-primary/70">Halaman order, tracking, dan komunikasi pelanggan terasa lebih profesional untuk cabang Anda.</p>
                    </div>
                    <div class="md:col-span-4 rounded-[2rem] bg-white p-8 shadow-sm">
                        <div class="flex flex-col gap-10 md:flex-row md:items-center">
                            <div class="md:w-1/2">
                                <h4 class="mb-4 font-manrope text-xl font-bold text-artisan-primary">Dasbor Pelacakan Langsung</h4>
                                <p class="text-sm leading-relaxed text-artisan-primary/60">Berikan ketenangan kepada pelanggan dengan status pesanan yang jelas, sambil membantu owner membaca performa usaha lebih cepat.</p>
                            </div>
                            <div class="md:w-1/2 rounded-2xl bg-artisan-surface-low p-6">
                                <div class="flex h-40 items-end justify-around gap-3">
                                    <div class="h-[38%] w-full rounded-t-lg bg-artisan-primary/15"></div>
                                    <div class="h-[58%] w-full rounded-t-lg bg-artisan-primary/25"></div>
                                    <div class="h-[82%] w-full rounded-t-lg bg-artisan-primary/40"></div>
                                    <div class="h-[72%] w-full rounded-t-lg bg-artisan-secondary/65"></div>
                                    <div class="h-[100%] w-full rounded-t-lg bg-artisan-primary"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
