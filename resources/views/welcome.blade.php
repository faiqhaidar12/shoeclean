<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ShoeClean - Sistem Manajemen Artisan Restorasi Sepatu</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            .bg-noise { background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
        </style>
    </head>
    <body class="antialiased bg-artisan-background text-artisan-primary selection:bg-artisan-secondary/20 font-sans">
        <!-- Navigation -->
        <nav class="fixed w-full bg-artisan-background/80 backdrop-blur-xl z-50">
            <div class="max-w-7xl mx-auto px-8 h-24 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-artisan-primary rounded-xl flex items-center justify-center text-white shadow-artisan">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <span class="text-2xl font-manrope font-extrabold tracking-tighter uppercase whitespace-nowrap">ShoeClean<span class="text-artisan-secondary">.</span></span>
                </div>
                
                <div class="flex items-center gap-10">
                    <div class="hidden md:flex items-center gap-8 text-sm font-bold uppercase tracking-widest text-artisan-secondary/80">
                        <a href="#features" class="hover:text-artisan-primary transition-colors">Fitur</a>
                        <a href="{{ route('pricing') }}" class="hover:text-artisan-primary transition-colors">Harga</a>
                        <a href="#tracking" class="hover:text-artisan-primary transition-colors">Lacak Pesanan</a>
                    </div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-artisan-primary text-sm uppercase tracking-widest px-8 py-3.5">
                            Dashboard
                        </a>
                    @else
                        <div class="flex items-center gap-6">
                            <a href="{{ route('login') }}" class="text-sm font-bold uppercase tracking-widest text-artisan-secondary hover:text-artisan-primary transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="btn-artisan-primary text-sm uppercase tracking-widest px-8 py-3.5">
                                Daftar Gratis
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative pt-48 lg:pt-64 pb-32">
            <div class="max-w-7xl mx-auto px-8 relative">
                <div class="grid lg:grid-cols-2 gap-24 items-start">
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-artisan-primary text-white text-[10px] font-bold uppercase tracking-[0.2em] mb-10 shadow-artisan">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-artisan-secondary opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-artisan-secondary"></span>
                            </span>
                            Operasional Outlet Lebih Rapi
                        </div>
                        
                        <h1 class="text-6xl lg:text-8xl headline-editorial mb-10 leading-[0.95] -ml-1">
                            Kelola Order, <br>
                            Pembayaran, dan <br>
                            <span class="text-artisan-secondary italic">Outlet</span> Lebih Mudah.
                        </h1>
                        
                        <p class="text-xl text-artisan-secondary/80 max-w-lg mb-14 leading-relaxed font-medium">
                            Web app untuk bisnis shoe care dan laundry yang ingin order lebih tertata, customer bisa tracking sendiri, pembayaran QRIS lebih rapi, dan owner punya laporan yang siap dipakai setiap hari.
                        </p>

                        <div class="flex flex-wrap gap-6 mb-24">
                            <a href="{{ route('register') }}" class="btn-artisan-primary text-base py-5 px-10">
                                Mulai Gratis
                            </a>
                            <a href="{{ route('pricing') }}" class="btn-artisan-secondary text-base py-5 px-10">
                                Lihat Paket
                            </a>
                        </div>

                        <!-- Artisan Tracking Tool -->
                        <div id="tracking" class="p-10 card-artisan shadow-artisan relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-artisan-surface-low rounded-full -translate-y-1/2 translate-x-1/2 opacity-50"></div>
                            <div class="relative">
                                <h3 class="text-2xl font-manrope font-extrabold text-artisan-primary mb-6 flex items-center gap-3">
                                    Lacak Pesanan Customer
                                    <span class="text-artisan-secondary">/</span>
                                </h3>
                                <p class="text-sm text-artisan-secondary/70 font-semibold leading-relaxed mb-5">
                                    Customer cukup masukkan nomor invoice untuk cek progres order, status pembayaran, dan info outlet tanpa perlu chat admin berulang.
                                </p>
                                <form onsubmit="event.preventDefault(); const invoice = document.getElementById('invoice').value; if(invoice) window.location.href = '/track?invoice=' + encodeURIComponent(invoice);" 
                                    class="flex flex-col sm:flex-row gap-4">
                                    <div class="flex-1 relative">
                                        <input 
                                            type="text" 
                                            id="invoice"
                                            placeholder="Masukkan Nomor Invois (INV/...)" 
                                            class="artisan-input font-bold"
                                            required
                                        >
                                    </div>
                                    <button type="submit" class="btn-artisan-primary whitespace-nowrap px-10">
                                        Lacak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-12 lg:mt-0">
                        <!-- Asymmetrical Layering -->
                        <div class="absolute -top-12 -left-12 w-32 h-32 bg-artisan-secondary/10 rounded-full blur-3xl animate-pulse"></div>
                        <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-artisan-primary/5 rounded-full blur-3xl"></div>
                        
                        <div class="relative group">
                            <div class="absolute inset-0 bg-artisan-primary/5 rounded-[40px] translate-x-6 translate-y-6 -z-10 transition-transform group-hover:translate-x-4 group-hover:translate-y-4"></div>
                            <div class="rounded-[40px] overflow-hidden shadow-artisan relative">
                                <img src="{{ asset('images/hero_artisan.png') }}" alt="Artisan Management System" class="w-full h-auto scale-105 group-hover:scale-100 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-artisan-primary/40 to-transparent"></div>
                                <div class="absolute bottom-10 left-10 right-10 flex justify-between items-end">
                                    <div>
                                        <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest mb-1">Kualitas Restorasi</p>
                                        <p class="text-white text-2xl font-manrope font-bold italic tracking-tight">Restorasi Lebih Dari Sekadar Cuci.</p>
                                    </div>
                                    <div class="w-12 h-12 glass rounded-full flex items-center justify-center text-white">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <section class="py-28 px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between mb-14">
                    <div class="max-w-2xl">
                        <p class="text-artisan-secondary text-xs font-black uppercase tracking-[0.3em] mb-5">/ Pilih Outlet</p>
                        <h2 class="text-4xl lg:text-5xl headline-editorial">Langsung Order ke Cabang yang Sudah Aktif.</h2>
                    </div>
                    <div class="max-w-xl space-y-4">
                        <p class="text-base font-semibold leading-relaxed text-artisan-secondary/65">
                            Pilih outlet terdekat untuk mulai order, lihat QRIS cabang, dan kirim pesanan tanpa perlu chat admin terlebih dahulu.
                        </p>
                        <a href="{{ route('public.order.select') }}" class="inline-flex items-center gap-3 text-sm font-black uppercase tracking-[0.22em] text-artisan-primary hover:text-artisan-secondary transition-colors">
                            Lihat Semua Outlet
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>

                @if($outlets->isNotEmpty())
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($outlets as $outlet)
                            <a href="{{ route('public.order', $outlet) }}" class="group rounded-[2rem] border border-artisan-secondary/10 bg-artisan-background p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-artisan-secondary/25 hover:shadow-artisan">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-artisan-primary text-white flex items-center justify-center text-xl font-black italic shadow-artisan">
                                        {{ strtoupper(substr($outlet->name, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600">Siap Order</span>
                                        @if($outlet->qris_image_path)
                                            <span class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">QRIS Tersedia</span>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="mt-6 text-2xl font-manrope font-extrabold text-artisan-primary group-hover:text-artisan-secondary transition-colors">
                                    {{ $outlet->name }}
                                </h3>
                                <p class="mt-3 text-sm font-semibold leading-relaxed text-artisan-secondary/70">
                                    {{ $outlet->address }}
                                </p>
                                <div class="mt-5 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-artisan-surface-low px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-secondary/75">
                                        {{ $outlet->services_count }} Layanan Aktif
                                    </span>
                                    @if($outlet->pickup_fee > 0)
                                        <span class="rounded-full bg-artisan-surface-low px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-secondary/75">
                                            Pickup Tersedia
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-6 flex items-center justify-between gap-4">
                                    <span class="text-[11px] font-black uppercase tracking-[0.22em] text-artisan-secondary/70">{{ $outlet->phone }}</span>
                                    <span class="text-sm font-black text-artisan-primary group-hover:text-artisan-secondary transition-colors">Pesan Sekarang</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-[2.5rem] border border-dashed border-artisan-secondary/20 bg-artisan-surface-low px-8 py-14 text-center">
                        <h3 class="text-2xl font-manrope font-extrabold text-artisan-primary">Outlet publik belum tersedia.</h3>
                        <p class="mt-4 text-sm font-semibold leading-relaxed text-artisan-secondary/65">
                            Outlet yang aktif akan tampil di sini agar customer bisa langsung masuk ke halaman order per cabang.
                        </p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Features Section (The Fluid Grid) -->
        <section id="features" class="py-48 bg-artisan-surface-low relative overflow-hidden">
             <div class="bg-noise absolute inset-0 opacity-[0.03] pointer-events-none"></div>
            <div class="max-w-7xl mx-auto px-8 relative">
                <div class="lg:flex items-end justify-between mb-24 gap-12">
                    <div class="max-w-2xl">
                        <p class="text-artisan-secondary text-xs font-black uppercase tracking-[0.3em] mb-6">/ Fitur Utama</p>
                        <h2 class="text-5xl lg:text-6xl headline-editorial">Sistem Digital <br> untuk Outlet yang Ingin Tumbuh.</h2>
                    </div>
                    <p class="text-artisan-secondary/60 text-lg font-medium max-w-sm lg:mb-4">Dari order masuk sampai laporan, semua dibuat lebih jelas untuk tim outlet dan lebih nyaman untuk customer.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <!-- Feature: Multi-Outlet -->
                    <div class="group py-12 px-2 transition-all">
                        <div class="w-16 h-16 bg-artisan-primary/10 rounded-2xl flex items-center justify-center mb-10 group-hover:bg-artisan-primary group-hover:text-white transition-all duration-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-2xl font-manrope font-extrabold text-artisan-primary mb-6">Manajemen Multi-Cabang</h3>
                        <p class="text-artisan-secondary/70 leading-relaxed font-medium mb-8">Kelola satu atau banyak cabang dengan alur yang konsisten, termasuk QRIS per outlet dan kontrol owner lintas outlet.</p>
                        <div class="h-0.5 bg-artisan-secondary/10 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>

                    <!-- Feature: POS -->
                    <div class="group py-12 px-2 transition-all">
                        <div class="w-16 h-16 bg-artisan-primary/10 rounded-2xl flex items-center justify-center mb-10 group-hover:bg-artisan-primary group-hover:text-white transition-all duration-500 text-artisan-primary">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-2xl font-manrope font-extrabold text-artisan-primary mb-6">Order & Tracking</h3>
                        <p class="text-artisan-secondary/70 leading-relaxed font-medium mb-8">Customer bisa order lebih rapi, tim outlet lebih mudah memproses, dan status pesanan bisa dicek kapan saja lewat invoice.</p>
                        <div class="h-0.5 bg-artisan-secondary/10 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>

                    <!-- Feature: Finances -->
                    <div class="group py-12 px-2 transition-all">
                        <div class="w-16 h-16 bg-artisan-primary/10 rounded-2xl flex items-center justify-center mb-10 group-hover:bg-artisan-primary group-hover:text-white transition-all duration-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-2xl font-manrope font-extrabold text-artisan-primary mb-6">Pembayaran & Laporan</h3>
                        <p class="text-artisan-secondary/70 leading-relaxed font-medium mb-8">QRIS outlet, verifikasi bukti bayar, export laporan, dan insight bisnis untuk membantu owner mengambil keputusan lebih cepat.</p>
                        <div class="h-0.5 bg-artisan-secondary/10 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-48 px-8 bg-artisan-primary text-white relative overflow-hidden">
            <div class="absolute -top-64 -right-64 w-[600px] h-[600px] bg-artisan-secondary/20 rounded-full blur-[120px]"></div>
            <div class="max-w-4xl mx-auto text-center relative">
                <p class="text-artisan-secondary font-black uppercase tracking-[0.4em] mb-10">/ Mulai Sekarang</p>
                <h2 class="text-5xl lg:text-7xl headline-editorial text-white mb-14 leading-[0.9]">Buat Operasional Outlet Anda Lebih Cepat, Lebih Rapi, dan Lebih Mudah Dikontrol.</h2>
                <div class="flex flex-wrap justify-center gap-8">
                    <a href="{{ route('register') }}" class="btn-artisan-primary bg-white text-artisan-primary hover:bg-artisan-secondary hover:text-white text-lg py-5 px-12">
                        Coba Gratis Sekarang
                    </a>
                    <a href="{{ route('pricing') }}" class="btn-artisan-secondary border-white/20 text-white hover:bg-white hover:text-artisan-primary text-lg py-5 px-12">
                        Bandingkan Paket
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-artisan-background py-24 px-8 border-t border-artisan-secondary/5">
            <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-16">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-10">
                        <div class="w-8 h-8 bg-artisan-primary rounded-lg flex items-center justify-center text-white">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <span class="text-xl font-manrope font-extrabold text-artisan-primary uppercase tracking-tighter">ShoeClean<span class="text-artisan-secondary italic">.</span></span>
                    </div>
                    <p class="text-artisan-secondary/60 font-medium max-w-sm">Web app operasional untuk bisnis shoe care dan laundry yang ingin order lebih tertata, customer lebih tenang, dan owner lebih mudah membaca bisnis.</p>
                </div>
                
                <div>
                    <h4 class="text-xs font-black uppercase tracking-[0.3em] text-artisan-primary mb-8">Sosial</h4>
                    <div class="flex flex-col gap-6 text-sm font-bold text-artisan-secondary/60">
                         <a href="#" class="hover:text-artisan-primary transition-colors">Instagram</a>
                         <a href="#" class="hover:text-artisan-primary transition-colors">Twitter</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-[0.3em] text-artisan-primary mb-8">Tautan</h4>
                    <div class="flex flex-col gap-6 text-sm font-bold text-artisan-secondary/60">
                         <a href="#features" class="hover:text-artisan-primary transition-colors">Fitur</a>
                         <a href="{{ route('pricing') }}" class="hover:text-artisan-primary transition-colors">Harga</a>
                         <a href="{{ route('login') }}" class="hover:text-artisan-primary transition-colors">Masuk</a>
                    </div>
                </div>
            </div>
            
            <div class="max-w-7xl mx-auto mt-24 pt-12 border-t border-artisan-secondary/5 flex flex-col md:flex-row justify-between items-center gap-8">
                <p class="text-[10px] font-black uppercase tracking-[0.5em] text-artisan-secondary/40">© {{ date('Y') }} Sistem Artisan ShoeClean. Dibangun untuk Preservasi.</p>
                <div class="flex gap-10 text-[10px] font-black uppercase tracking-[0.5em] text-artisan-secondary/40">
                    <a href="#" class="hover:text-artisan-primary">Privasi</a>
                    <a href="#" class="hover:text-artisan-primary">Ketentuan Web</a>
                </div>
            </div>
        </footer>
    </body>
</html>
