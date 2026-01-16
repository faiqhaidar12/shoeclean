<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shoe Clean - Premium Shoe Cleaning Service</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen">
        <!-- Navigation -->
        <nav class="max-w-7xl mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg shadow-primary-500/30">
                        👟
                    </div>
                    <span class="text-2xl font-bold text-white">Shoe Clean</span>
                </div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-white/10 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-white/20 transition-all duration-200 border border-white/20">
                        Dashboard
                    </a>
                @endauth
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-6 py-16">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/80 text-sm mb-8 border border-white/10">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Layanan Cuci Sepatu Premium #1
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                    Sepatu Bersih,
                    <br>
                    <span class="bg-gradient-to-r from-primary-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                        Penampilan Prima
                    </span>
                </h1>
                
                <p class="text-xl text-white/60 max-w-2xl mx-auto mb-12">
                    Layanan cuci sepatu profesional dengan treatment khusus untuk semua jenis sepatu. Dijamin bersih, wangi, dan seperti baru!
                </p>

                <!-- CTA Button -->
                <div class="flex items-center justify-center mb-16">
                    <a href="#track" class="px-8 py-4 bg-gradient-to-r from-primary-600 to-purple-600 text-white font-semibold rounded-2xl hover:from-primary-700 hover:to-purple-700 transition-all duration-200 shadow-xl shadow-primary-500/30 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Lacak Sepatu Saya
                    </a>
                </div>
            </div>

            <!-- Track Order Section -->
            <div id="track" class="max-w-2xl mx-auto mb-24">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-purple-500 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-2">Lacak Status Sepatu Anda</h2>
                        <p class="text-white/60">Masukkan nomor invoice untuk cek status pesanan</p>
                    </div>
                    
                    <form onsubmit="event.preventDefault(); const invoice = document.getElementById('invoice').value; if(invoice) window.location.href = '/track?invoice=' + encodeURIComponent(invoice);">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <input 
                                type="text" 
                                id="invoice"
                                placeholder="INV/20260115/1/0001" 
                                class="flex-1 px-6 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono"
                                required
                            >
                            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-primary-600 to-purple-600 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-purple-700 transition-all duration-200 whitespace-nowrap">
                                🔍 Lacak Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Features -->
            <div class="grid md:grid-cols-3 gap-8 mb-24">
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Cepat & Efisien</h3>
                    <p class="text-white/60">Proses pengerjaan cepat dengan hasil maksimal. Sepatu siap dalam 1-3 hari kerja.</p>
                </div>

                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Aman & Terpercaya</h3>
                    <p class="text-white/60">Menggunakan bahan premium yang aman untuk semua jenis material sepatu.</p>
                </div>

                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Pickup & Delivery</h3>
                    <p class="text-white/60">Layanan antar jemput tersedia! Tidak perlu repot keluar rumah.</p>
                </div>
            </div>

            <!-- Services Preview -->
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">Layanan Kami</h2>
                <p class="text-white/60 max-w-xl mx-auto mb-12">Berbagai pilihan treatment untuk sepatu kesayangan Anda</p>
                
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl mb-4">🧼</div>
                        <h3 class="font-bold text-white mb-2">Deep Clean</h3>
                        <p class="text-primary-400 font-bold">Rp 35.000</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl mb-4">✨</div>
                        <h3 class="font-bold text-white mb-2">Premium Wash</h3>
                        <p class="text-primary-400 font-bold">Rp 50.000</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl mb-4">🎨</div>
                        <h3 class="font-bold text-white mb-2">Repaint</h3>
                        <p class="text-primary-400 font-bold">Rp 150.000</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl mb-4">🛡️</div>
                        <h3 class="font-bold text-white mb-2">Unyellowing</h3>
                        <p class="text-primary-400 font-bold">Rp 75.000</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-white/10 py-8">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="text-white/40 text-sm">
                    © {{ date('Y') }} Shoe Clean. All rights reserved.
                </p>
            </div>
        </footer>
    </body>
</html>
