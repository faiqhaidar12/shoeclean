<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shoe Clean - Layanan Cuci Sepatu Premium</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="antialiased bg-white text-gray-900">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-primary-600/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">Shoe Clean<span class="text-primary-600">.</span></span>
                </div>
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gray-900 text-white font-medium rounded-full hover:bg-gray-800 transition-all text-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-primary-600 transition-colors">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="pt-32 pb-16 lg:pt-48 lg:pb-32 px-6">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 text-primary-700 text-sm font-medium mb-8 border border-primary-100">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                    </span>
                    Jasa Cuci Sepatu Premium #1
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 tracking-tight leading-tight">
                    Langkah Baru <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-indigo-600">Sepatu Bersih.</span>
                </h1>
                
                <p class="text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Kembalikan kilau sepatu kesayangan Anda dengan perawatan profesional. Cepat, bersih, dan terpercaya.
                </p>

                <!-- Tracking Form -->
                <div class="max-w-xl mx-auto bg-white p-2 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100">
                    <form onsubmit="event.preventDefault(); const invoice = document.getElementById('invoice').value; if(invoice) window.location.href = '/track?invoice=' + encodeURIComponent(invoice);" 
                        class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input 
                                type="text" 
                                id="invoice"
                                placeholder="Masukkan Nomor Invoice (INV/...)" 
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-primary-500 focus:ring-0 rounded-xl text-gray-900 placeholder-gray-400 font-medium transition-all"
                                required
                            >
                        </div>
                        <button type="submit" class="px-8 py-3.5 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/20">
                            Cek Status
                        </button>
                    </form>
                </div>
                <p class="mt-4 text-sm text-gray-400">Contoh: INV/2026/01/001</p>
            </div>
        </main>

        <!-- Services -->
        <section class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Layanan Unggulan</h2>
                    <p class="text-gray-500">Pilih perawatan terbaik untuk sepatu Anda</p>
                </div>

                @if(!isset($services) || $services->isEmpty())
                    <div class="col-span-full text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <p class="text-gray-500">Belum ada layanan yang tersedia saat ini.</p>
                    </div>
                @else
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($services as $index => $service)
                            @php
                                $colors = ['blue', 'emerald', 'purple', 'amber', 'rose', 'cyan'];
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group cursor-pointer">
                                <div class="w-12 h-12 bg-{{ $color }}-50 text-{{ $color }}-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    @if($index % 4 == 0)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    @elseif($index % 4 == 1)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @elseif($index % 4 == 2)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    @endif
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-500 mb-4">Layanan terbaik dengan hasil maksimal. {{ $service->unit ? 'Hitungan: ' . $service->unit : '' }}</p>
                                <a href="#" class="inline-flex items-center text-primary-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                    Konsultasi
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="py-24 bg-white">
             <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-12 text-center">
                    <div>
                        <div class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Pengerjaan Cepat</h3>
                        <p class="text-gray-500 text-sm">Estimasi selesai 1-3 hari sesuai jenis layanan.</p>
                    </div>
                     <div>
                        <div class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Jaminan Kualitas</h3>
                        <p class="text-gray-500 text-sm">Garansi cuci ulang jika hasil kurang maksimal.</p>
                    </div>
                     <div>
                        <div class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Harga Terjangkau</h3>
                        <p class="text-gray-500 text-sm">Biaya ramah kantong dengan hasil premium.</p>
                    </div>
                </div>
             </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                     <span class="text-lg font-bold text-gray-900">Shoe Clean<span class="text-primary-600">.</span></span>
                </div>
                <p class="text-gray-400 text-sm">
                    © {{ date('Y') }} Shoe Clean. All rights reserved.
                </p>
                <div class="flex gap-6">
                    <a href="#" class="text-gray-400 hover:text-gray-900">Instagram</a>
                    <a href="#" class="text-gray-400 hover:text-gray-900">WhatsApp</a>
                </div>
            </div>
        </footer>
    </body>
</html>
