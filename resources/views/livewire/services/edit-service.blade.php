<div class="py-8">
    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('services.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Katalog
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl italic">Ubah Definisi Protokol</h1>
    </div>

    <!-- Form Container -->
    <div class="max-w-3xl">
        <div class="card-artisan p-10 lg:p-16">
            <form wire:submit="save" class="space-y-12">
                @if($availableOutlets->count() > 1)
                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Penugasan Outlet *</label>
                        <select wire:model="outlet_id" class="artisan-input">
                            @foreach($availableOutlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                @elseif($availableOutlets->count() == 1)
                    <div class="p-8 bg-artisan-surface-low rounded-2xl flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-1">Outlet Ditugaskan</p>
                            <p class="text-sm font-manrope font-black text-artisan-primary">{{ $availableOutlets->first()->name }}</p>
                        </div>
                        <svg class="w-5 h-5 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                @endif

                <div class="space-y-10 pt-10 border-t border-artisan-outline/10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-secondary">Detail Protokol</h4>
                    
                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Nama Protokol *</label>
                        <input type="text" wire:model="name" class="artisan-input" placeholder="Contoh: Cuci Ekstra Mendalam">
                        @error('name') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Satuan Metrik *</label>
                            <select wire:model="unit" class="artisan-input">
                                <option value="pasang">Per Pasang</option>
                                <option value="pcs">Per Buah</option>
                                <option value="kg">Per Kilogram</option>
                                <option value="meter">Per Meter</option>
                            </select>
                            @error('unit') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Harga Layanan (Rp) *</label>
                            <input type="number" wire:model="price" class="artisan-input" placeholder="50000">
                            @error('price') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Status Operasional</label>
                        <select wire:model="status" class="artisan-input">
                            <option value="active">Operasional</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                        @error('status') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-6 pt-10 border-t border-artisan-outline/10">
                    <a href="{{ route('services.index') }}" class="btn-artisan-secondary flex-1 text-center">Batalkan Perubahan</a>
                    <button type="submit" class="btn-artisan-primary flex-1">Perbarui Protokol</button>
                </div>
            </form>
        </div>
    </div>
</div>
