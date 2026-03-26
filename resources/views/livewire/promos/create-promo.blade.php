<div class="py-8">
    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('promos.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Katalog
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl italic">Buat Promo Baru</h1>
    </div>

    <!-- Form Container -->
    <div class="max-w-4xl">
        <div class="card-artisan p-10 lg:p-16">
            <form wire:submit="save" class="space-y-12">
                <div class="space-y-10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-secondary">Parameter Promo Strategis</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Kode Promo *</label>
                            <input type="text" wire:model="code" class="artisan-input font-mono !uppercase !tracking-widest" placeholder="Contoh: PROMO20">
                            @error('code') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Nama Promo *</label>
                            <input type="text" wire:model="name" class="artisan-input" placeholder="Contoh: Promo Lebaran">
                            @error('name') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @if($outlets->count() > 0)
                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Berlaku di Outlet</label>
                        <select wire:model="outlet_id" class="artisan-input">
                            <option value="">Semua Outlet (Omni-Outlet)</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-artisan-primary/30 font-black uppercase tracking-widest mt-4 italic text-right">Biarkan kosong agar berlaku secara global</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Jenis Potongan *</label>
                            <select wire:model="type" class="artisan-input">
                                <option value="percentage">Potongan Persentase (%)</option>
                                <option value="fixed">Potongan Harga Tetap (Rp)</option>
                            </select>
                            @error('type') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Besaran Potongan *</label>
                            <input type="number" wire:model="value" class="artisan-input" placeholder="20">
                            @error('value') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Minimal Pesanan (Rp)</label>
                            <input type="number" wire:model="min_order" class="artisan-input" placeholder="0">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Maksimal Diskon (Rp)</label>
                            <input type="number" wire:model="max_discount" class="artisan-input" placeholder="Tanpa batas">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Batas Maksimal Penggunaan</label>
                            <input type="number" wire:model="max_uses" class="artisan-input" placeholder="Tanpa batas penggunaan">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Tanggal Mulai *</label>
                            <input type="date" wire:model="start_date" class="artisan-input">
                            @error('start_date') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Tanggal Berakhir *</label>
                        <input type="date" wire:model="end_date" class="artisan-input">
                        @error('end_date') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-6">
                         <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" wire:model="is_active" id="is_active" class="sr-only peer">
                                <div class="w-14 h-7 bg-artisan-surface-low rounded-full peer peer-checked:bg-artisan-secondary transition-all shadow-inner"></div>
                                <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full peer-checked:translate-x-7 transition-transform shadow-artisan-sm"></div>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-artisan-primary group-hover:text-artisan-secondary transition-colors">Aktifkan Promo Sekarang</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-6 pt-10 border-t border-artisan-outline/10">
                    <a href="{{ route('promos.index') }}" class="btn-artisan-secondary flex-1 text-center">Batalkan Pembuatan</a>
                    <button type="submit" class="btn-artisan-primary flex-1">Simpan Promo</button>
                </div>
            </form>
        </div>
    </div>
</div>
