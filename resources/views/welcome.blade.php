<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ShoeClean | Software Operasional Shoe Care dan Laundry Sepatu</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="public-shell antialiased">
        <header class="public-topbar">
            <div class="mx-auto flex h-16 max-w-[1440px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="text-2xl font-bold tracking-tighter text-artisan-primary font-manrope">ShoeClean</div>
                @include('livewire.welcome.navigation')
            </div>
        </header>

        <main class="pt-16">
            <section class="relative overflow-hidden py-24 md:py-32">
                <div class="mx-auto grid max-w-7xl grid-cols-1 gap-16 px-6 lg:grid-cols-12 lg:items-center lg:px-8">
                    <div class="lg:col-span-7">
                        <div class="mb-8 inline-flex items-center space-x-2 rounded-full bg-artisan-secondary/20 px-4 py-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-artisan-secondary"></span>
                            <span class="text-xs font-black uppercase tracking-[0.24em] text-artisan-primary">Operasional Masa Depan</span>
                        </div>

                        <h1 class="font-manrope text-5xl font-extrabold tracking-tighter text-artisan-primary sm:text-6xl lg:text-7xl leading-[1.08]">
                            Otomatiskan <span class="bg-gradient-to-r from-artisan-primary to-artisan-tertiary bg-clip-text text-transparent">operasional outlet</span> Anda dengan presisi yang lebih rapi.
                        </h1>

                        <p class="mt-8 max-w-xl text-lg leading-relaxed text-artisan-primary/62">
                            Platform manajemen all-in-one untuk bisnis shoe care modern. Kelola pesanan, staf, cabang, pembayaran, dan laporan usaha dalam satu alur yang lebih jelas.
                        </p>

                        <div class="mt-12 flex flex-col gap-4 sm:flex-row">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-artisan-primary px-8 py-4 text-lg normal-case tracking-normal">Buka Dasbor</a>
                            @else
                                <a href="{{ route('register') }}" class="btn-artisan-primary px-8 py-4 text-lg normal-case tracking-normal">Mulai Sekarang</a>
                            @endauth
                            <a href="{{ route('pricing') }}" class="btn-artisan-secondary flex items-center justify-center gap-2 px-8 py-4 text-lg normal-case tracking-normal">
                                Lihat Paket
                            </a>
                        </div>
                    </div>

                    <div class="relative lg:col-span-5">
                        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-artisan-secondary/15 blur-3xl"></div>
                        <div class="relative z-10 rounded-[2rem] bg-white p-6 shadow-[0_20px_60px_rgba(0,32,69,0.12)]">
                            <img src="{{ asset('images/hero_artisan.png') }}" alt="Preview ShoeClean" class="h-[450px] w-full rounded-2xl object-cover">
                            <div class="absolute -bottom-6 -left-6 flex items-center gap-4 rounded-2xl border border-artisan-outline/20 bg-white p-6 shadow-xl">
                                <div class="rounded-full bg-artisan-secondary/20 p-3">
                                    <svg class="h-6 w-6 text-artisan-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-artisan-primary">Pendapatan Lebih Terpantau</div>
                                    <div class="text-xs text-artisan-primary/55">Owner melihat pertumbuhan dan performa outlet lebih cepat</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-artisan-surface-low py-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto mb-20 max-w-3xl text-center">
                        <h2 class="font-manrope text-3xl font-extrabold text-artisan-primary md:text-4xl">Kuasai setiap aspek operasional outlet Anda.</h2>
                        <p class="mt-6 text-lg text-artisan-primary/60">Kami membangun fitur yang menyelesaikan masalah nyata dalam order, pelayanan customer, dan kontrol cabang.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="md:col-span-2 rounded-[2rem] bg-white p-10">
                            <div class="max-w-md">
                                <div class="mb-8 flex h-12 w-12 items-center justify-center rounded-xl bg-artisan-primary">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.243-4.243a8 8 0 1111.313 0z" />
                                    </svg>
                                </div>
                                <h3 class="mb-4 text-2xl font-bold text-artisan-primary">Pelacakan Pesanan yang Lebih Jelas</h3>
                                <p class="text-artisan-primary/60">Customer dapat memantau status pesanan sendiri, sementara outlet tetap punya kontrol penuh atas progres layanan.</p>
                            </div>
                            <div class="mt-12 rounded-xl border border-artisan-outline/15 bg-artisan-surface-low p-4">
                                <div class="mb-4 flex items-center justify-between">
                                    <span class="text-xs font-black text-artisan-primary">PESANAN #9942</span>
                                    <span class="rounded-full bg-artisan-secondary/20 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary">Diproses</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-artisan-outline/20">
                                    <div class="h-full w-2/3 bg-artisan-secondary"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col justify-between rounded-[2rem] bg-artisan-primary p-10 text-white">
                            <div>
                                <div class="mb-8 flex h-12 w-12 items-center justify-center rounded-xl bg-white/10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="mb-4 text-2xl font-bold">Optimalisasi Tim</h3>
                                <p class="text-white/72">Bagi peran admin dan staf outlet, atur tugas, dan jaga operasional tetap terarah tanpa kekacauan chat manual.</p>
                            </div>
                            <div class="mt-8 flex -space-x-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-artisan-primary bg-white/10 text-sm font-black">A</div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-artisan-primary bg-white/10 text-sm font-black">S</div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-artisan-primary bg-white/10 text-sm font-black">T</div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-artisan-primary bg-artisan-secondary text-sm font-black text-artisan-primary">+12</div>
                            </div>
                        </div>

                        <div class="rounded-[2rem] border border-artisan-outline/15 bg-white p-10">
                            <div class="mb-8 flex h-12 w-12 items-center justify-center rounded-xl bg-artisan-secondary/20">
                                <svg class="h-6 w-6 text-artisan-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="mb-4 text-2xl font-bold text-artisan-primary">Pembayaran dan Laporan</h3>
                            <p class="text-artisan-primary/60">QRIS cabang, langganan SaaS, pengeluaran, dan insight bisnis tersedia dalam satu alur yang rapi.</p>
                        </div>

                        <div class="md:col-span-2 rounded-[2rem] bg-gradient-to-br from-artisan-secondary to-artisan-primary p-10 text-white">
                            <div class="flex flex-col gap-12 md:flex-row md:items-center">
                                <div class="flex-1">
                                    <h3 class="mb-4 text-2xl font-bold">Analitik Real-time</h3>
                                    <p class="mb-8 text-white/80">Pantau omzet, total order, performa cabang, dan penggunaan layanan dari satu dasbor yang enak dibaca owner.</p>
                                    <a href="{{ route('pricing') }}" class="inline-flex rounded-full bg-white px-6 py-3 text-sm font-bold text-artisan-primary">Lihat Modul Analitik</a>
                                </div>
                                <div class="flex h-32 flex-1 items-end justify-around gap-2">
                                    <div class="h-[40%] w-full rounded-t-lg bg-white/20"></div>
                                    <div class="h-[60%] w-full rounded-t-lg bg-white/35"></div>
                                    <div class="h-[90%] w-full rounded-t-lg bg-white/55"></div>
                                    <div class="h-[75%] w-full rounded-t-lg bg-white/75"></div>
                                    <div class="h-[100%] w-full rounded-t-lg bg-white"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white py-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-16 flex flex-col justify-between gap-8 md:flex-row md:items-end">
                        <div class="max-w-xl">
                            <h2 class="font-manrope text-3xl font-extrabold text-artisan-primary md:text-4xl">Dipakai oleh owner yang ingin outletnya lebih tertata.</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-3xl border border-artisan-outline/10 bg-white p-8 shadow-sm">
                            <div class="mb-6 flex text-artisan-secondary">★★★★★</div>
                            <p class="mb-8 italic leading-relaxed text-artisan-primary/65">"Sekarang order lebih rapi, customer tidak lagi terus-terusan tanya progres, dan saya bisa lihat performa outlet dengan cepat."</p>
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-artisan-surface-low"></div>
                                <div>
                                    <div class="font-bold text-artisan-primary">Owner Shoe Care</div>
                                    <div class="text-xs text-artisan-primary/45">Jakarta</div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-artisan-outline/10 bg-white p-8 shadow-sm">
                            <div class="mb-6 flex text-artisan-secondary">★★★★★</div>
                            <p class="mb-8 italic leading-relaxed text-artisan-primary/65">"Bukan cuma software kasir, tapi benar-benar membantu kami mengatur layanan, staf, dan cabang dalam satu tempat."</p>
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-artisan-surface-low"></div>
                                <div>
                                    <div class="font-bold text-artisan-primary">Admin Outlet</div>
                                    <div class="text-xs text-artisan-primary/45">Bandung</div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-artisan-outline/10 bg-white p-8 shadow-sm">
                            <div class="mb-6 flex text-artisan-secondary">★★★★★</div>
                            <p class="mb-8 italic leading-relaxed text-artisan-primary/65">"Customer sekarang bisa pesan dan lacak sendiri. Tim outlet juga lebih fokus kerja daripada menjawab chat yang berulang."</p>
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-artisan-surface-low"></div>
                                <div>
                                    <div class="font-bold text-artisan-primary">Pemilik Cabang</div>
                                    <div class="text-xs text-artisan-primary/45">Surabaya</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-artisan-surface-low py-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="section-label">Pilih Outlet</p>
                            <h2 class="mt-4 font-manrope text-4xl font-extrabold text-artisan-primary">Arahkan customer langsung ke cabang yang siap menerima order.</h2>
                        </div>
                        <a href="{{ route('public.order.select') }}" class="btn-artisan-secondary">Lihat Semua Outlet</a>
                    </div>

                    @if($outlets->isNotEmpty())
                        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($outlets as $outlet)
                                <a href="{{ route('public.order', $outlet) }}" class="rounded-[2rem] border border-artisan-outline/15 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-artisan">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-[1.4rem] bg-artisan-primary text-xl font-manrope font-extrabold italic text-white">
                                            {{ strtoupper(substr($outlet->name, 0, 1)) }}
                                        </div>
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <span class="rounded-full bg-artisan-secondary/20 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary">Order</span>
                                            @if($outlet->qris_image_path)
                                                <span class="rounded-full bg-artisan-surface-low px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/60">QRIS</span>
                                            @endif
                                        </div>
                                    </div>
                                    <h3 class="mt-5 text-2xl font-manrope font-extrabold text-artisan-primary">{{ $outlet->name }}</h3>
                                    <p class="mt-3 text-sm font-semibold leading-7 text-artisan-primary/60">{{ $outlet->address }}</p>
                                    <div class="mt-5 flex flex-wrap gap-2">
                                        <span class="rounded-full bg-artisan-surface-low px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-primary/60">{{ $outlet->services_count }} layanan</span>
                                        @if($outlet->pickup_fee > 0)
                                            <span class="rounded-full bg-artisan-surface-low px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-primary/60">Pickup</span>
                                        @endif
                                    </div>
                                    <div class="mt-6 flex items-center justify-between gap-4">
                                        <span class="text-[11px] font-black uppercase tracking-[0.2em] text-artisan-primary/35">{{ $outlet->phone }}</span>
                                        <span class="text-sm font-bold text-artisan-primary">Pesan Sekarang</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-[2rem] border border-dashed border-artisan-outline/25 bg-white p-10 text-center">
                            <h3 class="text-2xl font-manrope font-extrabold text-artisan-primary">Outlet publik belum tersedia.</h3>
                            <p class="mt-4 text-sm font-semibold leading-7 text-artisan-primary/60">Saat cabang sudah aktif menerima order online, daftar outlet akan muncul di sini.</p>
                        </div>
                    @endif
                </div>
            </section>

            <section class="bg-artisan-primary py-24 text-white">
                <div class="mx-auto max-w-5xl px-6 text-center lg:px-8">
                    <p class="mb-8 text-[10px] font-black uppercase tracking-[0.32em] text-artisan-secondary">Mulai Sekarang</p>
                    <h2 class="font-manrope text-5xl font-extrabold leading-[1] sm:text-6xl">Rapikan order, kurangi chat manual, dan kontrol outlet dengan lebih tenang.</h2>
                    <div class="mt-12 flex flex-col justify-center gap-4 sm:flex-row">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-full bg-white px-8 py-4 text-lg font-bold text-artisan-primary shadow-lg">Buka Dasbor</a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-full bg-white px-8 py-4 text-lg font-bold text-artisan-primary shadow-lg">Coba Gratis Sekarang</a>
                        @endauth
                        <a href="{{ route('pricing') }}" class="rounded-full border border-white/20 px-8 py-4 text-lg font-bold text-white">Bandingkan Paket</a>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
