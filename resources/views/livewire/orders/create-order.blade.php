<div class="py-8">
    @if(!$outlet_id)
        <!-- Full Screen Blur Modal for Outlet Selection inside CreateOrder -->
        <div class="fixed inset-0 bg-artisan-primary/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[2rem] w-full max-w-md p-8 sm:p-12 shadow-artisan-xl">
                <div class="w-16 h-16 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                
                <h3 class="text-2xl font-manrope font-black text-artisan-primary mb-2">Pilih Outlet Tujuan</h3>
                <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest mb-8">Anda perlu memilih outlet aktif sebelum membuat pesanan baru.</p>
                
                <div class="space-y-6">
                    <div>
                        <select wire:model="fallbackSelectionId" class="artisan-input !bg-artisan-surface-low focus:!border-artisan-secondary focus:!ring-artisan-secondary/20 w-full mb-2">
                            <option value="">-- Silakan Pilih Outlet --</option>
                            @foreach($availableOutlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('fallbackSelectionId') 
                            <span class="text-red-500 text-[10px] uppercase font-bold tracking-widest block">{{ $message }}</span> 
                        @enderror
                    </div>
                    
                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('dashboard') }}" class="flex-1 px-6 py-4 rounded-xl text-center text-xs font-black uppercase tracking-widest text-artisan-primary/60 hover:text-artisan-primary hover:bg-artisan-surface-low transition-colors">Batal</a>
                        <button wire:click="confirmFallbackOutlet" type="button" class="flex-1 bg-artisan-primary text-white px-6 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-artisan-secondary transition-all shadow-artisan">
                            <span wire:loading.remove wire:target="confirmFallbackOutlet">Lanjutkan</span>
                            <span wire:loading wire:target="confirmFallbackOutlet">Memuat...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Header -->
        <div class="mb-12">
            <a href="{{ route('orders.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">Pesanan Baru</h1>
        </div>
    
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-12">

            <!-- ========== CLIENT SECTION ========== -->
            <div class="card-artisan p-8 sm:p-12">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-manrope font-black text-artisan-primary">Profil Pelanggan</h3>
                </div>
                
                @if($customer_id)
                    {{-- Customer Selected State --}}
                    <div class="flex items-center justify-between p-6 bg-artisan-secondary/5 rounded-[2rem] border border-artisan-secondary/10">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 bg-artisan-secondary text-white rounded-2xl flex items-center justify-center shadow-artisan">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-lg font-manrope font-black text-artisan-primary">{{ $customerSearch }}</p>
                                <p class="text-[10px] text-artisan-secondary font-black uppercase tracking-widest mt-1">Pelanggan Terdaftar</p>
                            </div>
                        </div>
                        <button wire:click="clearCustomer" type="button" class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/40 hover:text-red-600 transition-colors">
                            Ganti Pelanggan
                        </button>
                    </div>
                @else
                    {{-- Search + Quick Add State --}}
                    <div class="relative space-y-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1 relative group">
                                <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none text-artisan-primary/20 group-focus-within:text-artisan-secondary transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="customerSearch" 
                                    class="artisan-input !pl-16 !bg-artisan-surface-low/50" 
                                    placeholder="Cari Nama atau Kontak...">
                            </div>
                            <button wire:click="toggleQuickAdd" type="button" 
                                class="px-8 py-4 {{ $showQuickAdd ? 'bg-red-50 text-red-600' : 'bg-artisan-secondary text-white' }} rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-artisan hover:scale-[1.02] active:scale-[0.98]">
                                @if($showQuickAdd)
                                    Batal
                                @else
                                    Tambah Pelanggan Baru
                                @endif
                            </button>
                        </div>
                        
                        {{-- Search Results Dropdown --}}
                        @if(!empty($availableCustomers))
                            <div class="absolute z-50 w-full bg-white shadow-artisan-lg rounded-[2rem] mt-4 overflow-hidden border border-artisan-outline/20">
                                @foreach($availableCustomers as $cust)
                                    <div wire:click="selectCustomer({{ $cust->id }}, '{{ addslashes($cust->name) }}', '{{ $cust->phone }}', '{{ addslashes($cust->outlet?->name) }}')" 
                                        class="p-6 hover:bg-artisan-surface-low cursor-pointer flex items-center gap-4 transition-colors group">
                                        <div class="w-12 h-12 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary font-black group-hover:bg-white transition-colors">
                                            {{ strtoupper(substr($cust->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-manrope font-bold text-artisan-primary">{{ $cust->name }}</p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">{{ $cust->phone }}</p>
                                                @if($cust->outlet)
                                                    <span class="text-[8px] px-2 py-0.5 bg-artisan-primary/10 text-artisan-primary rounded-full font-black uppercase tracking-widest">{{ $cust->outlet->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- No Results Hint --}}
                        @if(strlen($customerSearch) >= 2 && empty($availableCustomers) && !$showQuickAdd && !$customer_id)
                            <div class="p-4 bg-artisan-surface-low rounded-2xl text-center">
                                <p class="text-xs text-artisan-primary/60 font-medium">Pelanggan tidak ditemukan. <button wire:click="toggleQuickAdd" type="button" class="text-artisan-secondary font-black hover:underline uppercase tracking-widest">Tambah baru?</button></p>
                            </div>
                        @endif
                    </div>

                    {{-- Quick Add Customer Form --}}
                    @if($showQuickAdd)
                        <div class="mt-8 p-8 bg-artisan-surface-low rounded-[2rem] space-y-8 animate-in fade-in slide-in-from-top-4">
                            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-artisan-secondary">Pendaftaran Pelanggan Baru</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-3">Nama Lengkap</label>
                                    <input type="text" wire:model="newCustomerName" class="artisan-input !bg-white" placeholder="Nama Pelanggan">
                                    @error('newCustomerName') <p class="text-red-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-3">Nomor Kontak</label>
                                    <input type="text" wire:model="newCustomerPhone" class="artisan-input !bg-white" placeholder="08xxxxxxxxxx">
                                    @error('newCustomerPhone') <p class="text-red-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <button wire:click="quickAddCustomer" type="button" class="btn-artisan-primary w-full !rounded-2xl">
                                <span wire:loading.remove wire:target="quickAddCustomer">Simpan Pendaftaran</span>
                                <span wire:loading wire:target="quickAddCustomer">Menyimpan...</span>
                            </button>
                        </div>
                    @endif

                    @error('customer_id') <p class="text-red-500 text-xs mt-4 font-black uppercase tracking-widest text-center">Pilih Pelanggan Dulu</p> @enderror
                @endif
            </div>

            <!-- Logistics Type -->
            <div class="card-artisan p-8 sm:p-12">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-manrope font-black text-artisan-primary">Tipe Pengiriman</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <label class="group relative flex flex-col p-8 rounded-[2rem] cursor-pointer transition-all border-2 {{ $order_type === 'regular' ? 'bg-artisan-primary border-artisan-primary' : 'bg-artisan-surface-low border-transparent hover:border-artisan-outline' }}">
                        <input type="radio" wire:model.live="order_type" value="regular" class="sr-only">
                        <span class="text-xs font-black uppercase tracking-widest {{ $order_type === 'regular' ? 'text-white' : 'text-artisan-primary' }}">Antar ke Outlet</span>
                        <span class="text-[10px] mt-2 {{ $order_type === 'regular' ? 'text-white/60' : 'text-artisan-primary/40' }} font-bold uppercase">Pelanggan datang</span>
                    </label>
                    <label class="group relative flex flex-col p-8 rounded-[2rem] cursor-pointer transition-all border-2 {{ !$pickup_enabled ? 'opacity-50 cursor-not-allowed' : '' }} {{ $order_type === 'pickup' ? 'bg-artisan-primary border-artisan-primary' : 'bg-artisan-surface-low border-transparent hover:border-artisan-outline' }}">
                        <input type="radio" wire:model.live="order_type" value="pickup" class="sr-only" @disabled(!$pickup_enabled)>
                        <span class="text-xs font-black uppercase tracking-widest {{ $order_type === 'pickup' ? 'text-white' : 'text-artisan-primary' }}">Layanan Penjemputan (Pickup)</span>
                        <span class="text-[10px] mt-2 {{ $order_type === 'pickup' ? 'text-white/60' : 'text-artisan-primary/40' }} font-bold uppercase">{{ $pickup_enabled ? 'Mulai dari Rp ' . number_format($pickupFee, 0, ',', '.') : 'Sedang Off' }}</span>
                    </label>
                    <label class="group relative flex flex-col p-8 rounded-[2rem] cursor-pointer transition-all border-2 {{ !$delivery_enabled ? 'opacity-50 cursor-not-allowed' : '' }} {{ $order_type === 'delivery' ? 'bg-artisan-primary border-artisan-primary' : 'bg-artisan-surface-low border-transparent hover:border-artisan-outline' }}">
                        <input type="radio" wire:model.live="order_type" value="delivery" class="sr-only" @disabled(!$delivery_enabled)>
                        <span class="text-xs font-black uppercase tracking-widest {{ $order_type === 'delivery' ? 'text-white' : 'text-artisan-primary' }}">Layanan Antar (Delivery)</span>
                        <span class="text-[10px] mt-2 {{ $order_type === 'delivery' ? 'text-white/60' : 'text-artisan-primary/40' }} font-bold uppercase">{{ $delivery_enabled ? 'Mulai dari Rp ' . number_format($deliveryFee, 0, ',', '.') : 'Sedang Off' }}</span>
                    </label>
                </div>

                @if($order_type === 'pickup' || $order_type === 'delivery')
                    <div class="mt-10 animate-in fade-in slide-in-from-top-4">
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Detail Alamat Pengiriman/Penjemputan</label>
                        <textarea wire:model="{{ $order_type }}_address" rows="3" class="artisan-input !bg-artisan-surface-low/50" placeholder="Masukkan detail alamat lengkap..."></textarea>
                    </div>
                @endif
            </div>

            <!-- Artisan Services Selection -->
            <div class="card-artisan p-8 sm:p-12">
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86 1.404l-1.396 1.117a2 2 0 01-1.127.426l-1.352.135a2 2 0 01-2.008-1.503l-.403-1.61a2 2 0 01.328-1.645l1.048-1.402a2 2 0 00.322-1.666l-.504-2.115a2 2 0 01.554-1.854l1.248-1.248a2 2 0 012.356-.37l2.126 1.063a2 2 0 001.366.184l2.16-.54a2 2 0 012.067 2.067l-.54 2.16a2 2 0 00.184 1.366l1.063 2.126a2 2 0 01-.37 2.356l-1.248 1.248z"/></svg>
                        </div>
                        <h3 class="text-xl font-manrope font-black text-artisan-primary">Layanan Cuci</h3>
                    </div>
                    <button wire:click="addItem" type="button" class="text-[10px] font-black uppercase tracking-widest text-artisan-secondary hover:text-artisan-primary transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah Layanan
                    </button>
                </div>
                
                <div class="space-y-6">
                    @foreach($items as $index => $item)
                        <div class="p-8 bg-artisan-surface-low rounded-[2rem] group transition-all hover:bg-white hover:shadow-artisan relative overflow-hidden">
                            <div class="flex flex-col lg:flex-row gap-8 items-start relative z-10">
                                <div class="flex-1 w-full">
                                    <label class="block text-[10px] font-black text-artisan-primary/30 uppercase tracking-widest mb-3">Pilih Layanan</label>
                                    <select wire:model.live="items.{{ $index }}.service_id" class="artisan-input !bg-white">
                                        <option value="">-- Pilih Layanan --</option>
                                        @foreach($availableServices as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-8 items-end w-full lg:w-auto">
                                    <div class="w-24">
                                        <label class="block text-[10px] font-black text-artisan-primary/30 uppercase tracking-widest mb-3 text-center">Jumlah</label>
                                        <input type="number" wire:model.live="items.{{ $index }}.quantity" class="artisan-input !bg-white text-center" min="1">
                                    </div>
                                    <div class="min-w-[120px] text-right">
                                        <label class="block text-[10px] font-black text-artisan-primary/30 uppercase tracking-widest mb-3">Subtotal</label>
                                        <p class="h-[48px] flex items-center justify-end font-manrope font-black text-artisan-primary text-sm whitespace-nowrap">
                                            Rp {{ number_format(((int)$items[$index]['price'] * (int)$items[$index]['quantity']), 0, ',', '.') }}
                                        </p>
                                    </div>
                                    @if(count($items) > 1)
                                        <button wire:click="removeItem({{ $index }})" class="h-[48px] px-4 text-artisan-primary/20 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @error('items') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest text-center mt-4">Minimal satu layanan harus dipilih</p> @enderror
                </div>
            </div>
            
            <!-- Artisan Notes -->
            <div class="card-artisan p-8 sm:p-12">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <label class="text-xs font-black uppercase tracking-[0.2em] text-artisan-primary">Catatan Pesanan / Perhatian Khusus</label>
                </div>
                <textarea wire:model="notes" rows="4" class="artisan-input !bg-artisan-surface-low/50" placeholder="Masukkan catatan pesanan seperti noda, bahan sepatu, dll..."></textarea>
            </div>
        </div>

        <!-- Order Manifest (Sidebar) -->
        <div class="lg:col-span-1">
            <div class="bg-artisan-primary rounded-[2.5rem] p-10 text-white lg:sticky lg:top-12 shadow-artisan-lg">
                <h3 class="headline-editorial text-3xl mb-10 italic">Ringkasan Pesanan</h3>
                
                <!-- Applied Promotions -->
                <div class="mb-10">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-4">Kode Promo</label>
                    <div class="flex gap-3">
                        <input type="text" wire:model="promo_code" class="flex-1 bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-xs font-mono uppercase tracking-widest focus:ring-2 focus:ring-artisan-secondary outline-none placeholder:text-white/20" placeholder="CODE000">
                        <button wire:click="applyPromo" type="button" class="px-6 py-4 bg-artisan-secondary text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-artisan-primary transition-all">Gunakan</button>
                    </div>
                    @if(session('promo_error'))
                        <p class="text-red-300 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ session('promo_error') }}</p>
                    @endif
                    @if(session('promo_success'))
                        <p class="text-emerald-300 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ session('promo_success') }}</p>
                    @endif
                </div>

                <!-- Financial Breakdown -->
                <div class="space-y-6 border-t border-white/10 pt-10 mb-10">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Total Layanan</span>
                        <span class="text-sm font-manrope font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order_type === 'pickup')
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Biaya Pickup</span>
                            <span class="text-sm font-manrope font-bold">Rp {{ number_format($pickupFee, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($order_type === 'delivery')
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Biaya Antar</span>
                            <span class="text-sm font-manrope font-bold">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($discount_amount > 0)
                        <div class="flex justify-between items-center text-emerald-300">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Diskon Promo</span>
                            <span class="text-sm font-manrope font-bold">- Rp {{ number_format($discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Grand Total -->
                <div class="border-t border-white/20 pt-10">
                    <div class="flex flex-col mb-10">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Total Tagihan</span>
                        <span class="text-4xl font-manrope font-black">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="space-y-4">
                        <button wire:click="save" type="button" class="w-full bg-artisan-secondary text-white py-5 rounded-2xl font-manrope font-black text-xs uppercase tracking-[0.2em] shadow-artisan hover:bg-white hover:text-artisan-primary transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <span wire:loading.remove wire:target="save">Buat Pesanan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                        <a href="{{ route('orders.index') }}" class="block w-full text-center py-5 border border-white/10 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-white/60 hover:text-white transition-colors">Batalkan Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
