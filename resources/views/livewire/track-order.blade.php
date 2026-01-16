<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="fixed w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-primary-600/20 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <span class="text-xl font-bold text-gray-900 tracking-tight">Shoe Clean<span class="text-primary-600">.</span></span>
            </a>
            <a href="/" class="text-sm font-semibold text-gray-600 hover:text-primary-600 transition-colors">
                Kembali
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 pt-32 pb-16 px-6">
        <div class="max-w-2xl mx-auto">
            <!-- Search Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Lacak Pesanan</h1>
                <p class="text-gray-500 mb-6">Masukkan nomor invoice tracking sepatu Anda</p>
                
                <form wire:submit="search" class="max-w-md mx-auto">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            wire:model="invoice" 
                            placeholder="INV/2026/..."
                            class="flex-1 px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-primary-500 focus:ring-0 rounded-xl text-gray-900 placeholder-gray-400 font-medium transition-all"
                        >
                        <button type="submit" class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/20">
                            Cek
                        </button>
                    </div>
                </form>
                
                @if($error)
                    <div class="mt-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm font-medium">
                        {{ $error }}
                    </div>
                @endif
            </div>

            <!-- Order Result -->
            @if($order)
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 overflow-hidden animate-fade-in relative">
                    <!-- Status Header -->
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Invoice</p>
                            <h2 class="text-xl font-mono font-bold text-gray-900">{{ $order->invoice_number }}</h2>
                        </div>
                        <div class="flex items-center gap-2">
                             <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold shadow-sm
                                {{ match($order->status) {
                                    'completed', 'picked_up' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                    'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
                                    'ready' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                    default => 'bg-amber-100 text-amber-700 border border-amber-200'
                                } }}">
                                {{ match($order->status) {
                                    'pending' => '⏳ Pending',
                                    'processing' => '⚙️ Dikerjakan',
                                    'ready' => '📦 Siap Ambil',
                                    'picked_up' => '✅ Diambil',
                                    'completed' => '✅ Selesai',
                                    'cancelled' => '❌ Batal',
                                    default => ucfirst($order->status)
                                } }}
                            </span>
                        </div>
                    </div>

                    <!-- Progress Timeline -->
                    <div class="p-8 border-b border-gray-100">
                        @php
                            $statuses = ['pending', 'processing', 'ready', 'picked_up'];
                            $labels = ['Diterima', 'Dikerjakan', 'Siap', 'Selesai'];
                            $currentIndex = array_search($order->status, $statuses);
                            if ($order->status === 'completed') $currentIndex = 3; 
                            if ($order->status === 'cancelled') $currentIndex = -1;
                        @endphp

                        <div class="relative flex items-center justify-between w-full">
                            <!-- Line Background -->
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-100 rounded-full -z-0"></div>
                            
                            <!-- Active Line -->
                            @if($currentIndex >= 0)
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-primary-600 rounded-full -z-0 transition-all duration-500"
                                style="width: {{ ($currentIndex / (count($statuses) - 1)) * 100 }}%"></div>
                            @endif

                            @foreach($statuses as $index => $status)
                                <div class="relative z-10 flex flex-col items-center group">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all duration-300
                                        {{ $currentIndex >= $index 
                                            ? 'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-600/30 scale-110' 
                                            : 'bg-white border-gray-200 text-gray-300' }}">
                                        @if($currentIndex > $index)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($currentIndex == $index)
                                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                        @else
                                            <div class="w-2.5 h-2.5 bg-gray-100 rounded-full"></div>
                                        @endif
                                    </div>
                                    <span class="absolute top-10 text-xs font-semibold whitespace-nowrap transition-colors duration-300
                                        {{ $currentIndex >= $index ? 'text-primary-700' : 'text-gray-400' }}">
                                        {{ $labels[$index] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                         @if($order->status === 'cancelled')
                            <div class="mt-8 text-center text-red-600 font-medium p-3 bg-red-50 rounded-lg">
                                Pesanan ini dibatalkan.
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="p-6">
                        <div class="grid sm:grid-cols-2 gap-6 mb-8">
                             <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Detail Pengerjaan</h4>
                                <ul class="space-y-3">
                                    @foreach($order->items as $item)
                                        <li class="flex justify-between text-sm">
                                            <span class="text-gray-700 overflow-hidden text-ellipsis whitespace-nowrap pr-2">{{ $item->service->name }} <span class="text-gray-400">x{{ $item->quantity }}</span></span>
                                            <span class="font-medium text-gray-900">Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Info Outlet</h4>
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-primary-50 text-primary-600 rounded-lg">
                                       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $order->outlet->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->outlet->address ?? 'Alamat tidak tersedia' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Info -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                             <div>
                                <p class="text-xs text-gray-400">Total Tagihan</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                 <p class="text-xs text-gray-400 mb-1">Status Pembayaran</p>
                                 <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold
                                    {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-sm text-gray-400">
        &copy; {{ date('Y') }} Shoe Clean.
    </footer>
</div>
