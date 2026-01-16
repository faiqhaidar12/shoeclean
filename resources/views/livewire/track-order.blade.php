<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="p-4 sm:p-6">
        <div class="max-w-2xl mx-auto">
            <a href="/" class="inline-flex items-center gap-3 group">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl sm:rounded-2xl flex items-center justify-center text-white text-xl sm:text-2xl shadow-lg group-hover:scale-105 transition-transform">
                    👟
                </div>
                <span class="text-xl sm:text-2xl font-bold text-white">Shoe Clean</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-start sm:items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-2xl">
            <!-- Search Card -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-white/20 shadow-2xl mb-6">
                <div class="text-center mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">🔍 Lacak Pesanan</h1>
                    <p class="text-white/60 text-sm sm:text-base">Masukkan nomor invoice untuk cek status</p>
                </div>
                
                <form wire:submit="search">
                    <div class="flex flex-col gap-3">
                        <input 
                            type="text" 
                            wire:model="invoice" 
                            placeholder="Contoh: INV/20260115/1/0001"
                            class="w-full px-4 sm:px-6 py-3 sm:py-4 bg-white/10 border border-white/20 rounded-xl sm:rounded-2xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono text-sm sm:text-base"
                        >
                        <button type="submit" class="w-full px-6 py-3 sm:py-4 bg-gradient-to-r from-primary-600 to-purple-600 text-white font-semibold rounded-xl sm:rounded-2xl hover:from-primary-700 hover:to-purple-700 transition-all shadow-lg shadow-primary-500/30 text-sm sm:text-base">
                            🔍 Lacak Sekarang
                        </button>
                    </div>
                </form>
                
                @if($error)
                    <div class="mt-4 p-3 bg-red-500/20 border border-red-500/30 rounded-xl">
                        <p class="text-red-300 text-sm text-center">❌ {{ $error }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Result -->
            @if($order)
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden animate-fade-in">
                    <!-- Order Header -->
                    <div class="bg-gradient-to-r from-primary-600 to-purple-600 p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="text-primary-100 text-xs sm:text-sm mb-1">Nomor Invoice</p>
                                <h2 class="text-base sm:text-xl font-mono font-bold text-white break-all">{{ $order->invoice_number }}</h2>
                                <p class="text-primary-200 text-xs sm:text-sm mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-2 self-start sm:self-auto">
                                <span class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold
                                    {{ match($order->status) {
                                        'completed', 'picked_up' => 'bg-emerald-500',
                                        'cancelled' => 'bg-red-500',
                                        'ready' => 'bg-blue-400',
                                        'processing' => 'bg-amber-400 text-gray-800',
                                        default => 'bg-gray-400'
                                    } }}">
                                    <span class="text-base">{{ match($order->status) {
                                        'pending' => '⏳',
                                        'processing' => '⚙️',
                                        'ready' => '📦',
                                        'picked_up' => '✅',
                                        'completed' => '✅',
                                        'cancelled' => '❌',
                                    } }}</span>
                                    {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Progress -->
                    <div class="p-4 sm:p-6 border-b border-gray-100">
                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-4">📍 Progress Status</h3>
                        
                        @php
                            $statuses = ['pending', 'processing', 'ready', 'picked_up'];
                            $labels = ['Diterima', 'Dikerjakan', 'Siap Ambil', 'Selesai'];
                            $icons = ['📥', '🔧', '📦', '✅'];
                            $currentIndex = array_search($order->status, $statuses);
                            if ($order->status === 'cancelled') $currentIndex = -1;
                            if ($order->status === 'completed') $currentIndex = 3;
                        @endphp

                        <!-- Mobile: Vertical Timeline -->
                        <div class="block sm:hidden space-y-3">
                            @foreach($statuses as $index => $status)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0
                                        {{ $currentIndex >= $index ? 'bg-primary-600 text-white shadow-lg' : 'bg-gray-200 text-gray-400' }}">
                                        @if($currentIndex >= $index) ✓ @else {{ $icons[$index] }} @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium {{ $currentIndex >= $index ? 'text-primary-600' : 'text-gray-400' }}">
                                            {{ $labels[$index] }}
                                        </p>
                                    </div>
                                    @if($currentIndex == $index && !in_array($order->status, ['cancelled', 'completed', 'picked_up']))
                                        <span class="text-xs bg-primary-100 text-primary-700 px-2 py-1 rounded-full animate-pulse">Saat ini</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop: Horizontal Timeline -->
                        <div class="hidden sm:flex items-center justify-between">
                            @foreach($statuses as $index => $status)
                                <div class="flex flex-col items-center z-10">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all
                                        {{ $currentIndex >= $index ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-gray-200 text-gray-500' }}">
                                        @if($currentIndex >= $index) ✓ @else {{ $index + 1 }} @endif
                                    </div>
                                    <span class="text-xs mt-2 text-center {{ $currentIndex >= $index ? 'text-primary-600 font-semibold' : 'text-gray-400' }}">
                                        {{ $labels[$index] }}
                                    </span>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex-1 h-1 mx-2 rounded {{ $currentIndex > $index ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                                @endif
                            @endforeach
                        </div>

                        @if($order->status === 'cancelled')
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-center">
                                <p class="text-red-600 font-bold text-sm">❌ Order ini telah dibatalkan</p>
                            </div>
                        @endif
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 sm:p-6 border-b border-gray-100">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Customer</p>
                            <p class="font-semibold text-gray-900">{{ $order->customer->name }}</p>
                            <p class="text-sm text-gray-600">{{ substr($order->customer->phone, 0, 4) . '****' . substr($order->customer->phone, -4) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Outlet</p>
                            <p class="font-semibold text-gray-900">{{ $order->outlet->name }}</p>
                            <p class="text-sm text-gray-600">📞 {{ $order->outlet->phone ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="p-4 sm:p-6">
                        <h4 class="text-xs font-medium text-gray-500 uppercase mb-3">Detail Items</h4>
                        <div class="space-y-2">
                            @foreach($order->items as $item)
                                <div class="flex justify-between items-center py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm sm:text-base">{{ $item->service->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="font-bold text-gray-900 text-sm sm:text-base">Rp {{ number_format($item->total_price, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total -->
                        <div class="mt-4 pt-4 border-t-2 border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 font-medium">Total Pembayaran</span>
                                <span class="text-xl sm:text-2xl font-bold text-primary-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Status Pembayaran</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                                    {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $order->payment_status === 'paid' ? '✅ LUNAS' : '⏳ BELUM BAYAR' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="p-4 sm:p-6">
        <div class="max-w-2xl mx-auto text-center">
            <a href="/" class="inline-flex items-center gap-2 text-white/60 hover:text-white text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Home
            </a>
        </div>
    </footer>
</div>
