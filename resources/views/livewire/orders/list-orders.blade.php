<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="headline-editorial text-4xl lg:text-5xl">Daftar Pesanan</h1>
            <p class="text-artisan-secondary/60 font-medium mt-2">Kelola semua pesanan aktif dan riwayat outlet.</p>
        </div>
        <button wire:click="startCreateOrder" class="btn-artisan-primary flex items-center justify-center gap-3 !py-4 shadow-artisan">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Buat Pesanan Baru
        </button>
    </div>

    <!-- Search & Filter Area -->
    <div class="bg-artisan-surface-low rounded-[2rem] p-8 mb-12">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6">
            <div class="flex-1 relative group">
                <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none text-artisan-primary/30 group-focus-within:text-artisan-secondary transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Invois atau Pelanggan..." 
                    class="artisan-input !pl-16 !bg-white">
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/40 whitespace-nowrap">Filter Status</span>
                <select wire:model.live="status" class="artisan-input !w-64 !bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="processing">Diproses</option>
                    <option value="ready">Selesai Cuci</option>
                    <option value="picked_up">Diambil</option>
                </select>
            </div>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="card-artisan p-20 text-center">
            <div class="w-24 h-24 bg-artisan-surface-low rounded-[2rem] flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10 text-artisan-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-2xl font-manrope font-extrabold text-artisan-primary mb-2">Belum Ada Pesanan</p>
            <p class="text-artisan-primary/40 font-medium mb-8">Tidak ada pesanan yang ditemukan.</p>
            <button wire:click="startCreateOrder" class="btn-artisan-primary inline-flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Buat Pesanan Pertama
            </button>
        </div>
    @else
        <!-- Fluid Ledger View -->
        <div class="card-artisan overflow-hidden p-0">
            <div class="overflow-x-auto artisan-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-artisan-surface-low/50">
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Invois</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Pelanggan</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Status</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Pembayaran</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Total Tagihan</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-artisan-outline/50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-artisan-surface-low transition-all group">
                                <td class="px-8 py-8 whitespace-nowrap">
                                    <span class="text-sm font-manrope font-black text-artisan-primary group-hover:text-artisan-secondary transition-colors">{{ $order->invoice_number }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <p class="text-[10px] text-artisan-primary/30 font-black uppercase tracking-widest">{{ $order->created_at->format('M d, Y') }}</p>
                                        @if($order->order_source === 'customer')
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest rounded-full">🌐 Online</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-8 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary font-black shadow-sm group-hover:bg-white group-hover:text-artisan-secondary transition-all">
                                            {{ substr($order->customer->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-manrope font-bold text-artisan-primary">{{ $order->customer->name }}</p>
                                            <p class="text-[10px] text-artisan-primary/40 font-black tracking-widest">{{ $order->customer->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-8 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ match($order->status) {
                                            'completed', 'picked_up' => 'bg-emerald-500',
                                            'cancelled' => 'bg-red-500',
                                            'ready' => 'bg-blue-500',
                                            default => 'bg-orange-500'
                                        } }} shadow-[0_0_10px_rgba(0,0,0,0.1)]"></div>
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ match($order->status) {
                                            'completed', 'picked_up' => 'text-emerald-700',
                                            'cancelled' => 'text-red-700',
                                            'ready' => 'text-blue-700',
                                            default => 'text-orange-700'
                                        } }}">{{ $order->status }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-8 whitespace-nowrap">
                                    @php
                                        $hasPending = $order->payments->where('status', 'pending')->isNotEmpty();
                                        $hasFailed = $order->payments->where('status', 'failed')->isNotEmpty();
                                    @endphp
                                    
                                    @if($order->payment_status === 'paid')
                                        <span class="px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full">Lunas</span>
                                    @elseif($hasPending)
                                        <span class="px-4 py-1.5 bg-orange-50 text-orange-700 text-[10px] font-black uppercase tracking-widest rounded-full animate-pulse">Menunggu Pembayaran</span>
                                    @elseif($hasFailed)
                                        <div class="flex items-center gap-2">
                                            <span class="px-4 py-1.5 bg-red-50 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-full">Ditolak</span>
                                            <div class="flex gap-1">
                                                <button wire:click="markPaid({{ $order->id }})" class="p-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                                <button wire:click="payOnline({{ $order->id }})" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="px-4 py-1.5 bg-artisan-primary text-white text-[10px] font-black uppercase tracking-widest rounded-full">Belum Lunas</span>
                                            <div class="flex gap-1">
                                                <button wire:click="markPaid({{ $order->id }})" class="p-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all duration-300 active:scale-95 shadow-sm" title="Tandai Lunas (Tunai)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                                <button wire:click="payOnline({{ $order->id }})" class="p-2 bg-artisan-secondary text-white rounded-lg hover:bg-artisan-primary transition-all duration-300 active:scale-95 shadow-sm" title="Kirim Link Pembayaran">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-8 whitespace-nowrap text-right">
                                    <p class="text-lg font-manrope font-extrabold text-artisan-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                    @if($order->discount_amount > 0)
                                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 mt-1">
                                            Hemat Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-8 py-8 whitespace-nowrap text-right">
                                    <a href="{{ route('orders.view', $order->id) }}" class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-artisan-secondary hover:text-artisan-primary transition-all duration-300 active:scale-95">
                                        Buka Detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-12 px-8">
            {{ $orders->links() }}
        </div>
    @endif

    <!-- Outlet Selection Modal Interceptor -->
    @if($showOutletSelectionModal)
        <div class="fixed inset-0 bg-artisan-primary/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[2rem] w-full max-w-md p-8 sm:p-12 shadow-artisan-xl">
                <div class="w-16 h-16 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                
                <h3 class="text-2xl font-manrope font-black text-artisan-primary mb-2">Pilih Outlet Tujuan</h3>
                <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest mb-8">Pilih outlet tempat pesanan ini akan dibuat</p>
                
                <div class="space-y-6">
                    <div>
                        <select wire:model="selectedInterceptorOutletId" class="artisan-input !bg-artisan-surface-low focus:!border-artisan-secondary focus:!ring-artisan-secondary/20 w-full mb-2">
                            <option value="">-- Silakan Pilih Outlet --</option>
                            @foreach($availableOutletsForSelection as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedInterceptorOutletId') 
                            <span class="text-red-500 text-[10px] uppercase font-bold tracking-widest block">{{ $message }}</span> 
                        @enderror
                    </div>
                    
                    <div class="flex gap-4 pt-4">
                        <button wire:click="closeModal" type="button" class="flex-1 px-6 py-4 rounded-xl text-xs font-black uppercase tracking-widest text-artisan-primary/60 hover:text-artisan-primary hover:bg-artisan-surface-low transition-colors">Batal</button>
                        <button wire:click="confirmOutletSelection" type="button" class="flex-1 bg-artisan-primary text-white px-6 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-artisan-secondary transition-all shadow-artisan">
                            <span wire:loading.remove wire:target="confirmOutletSelection">Lanjutkan</span>
                            <span wire:loading wire:target="confirmOutletSelection">Memuat...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
