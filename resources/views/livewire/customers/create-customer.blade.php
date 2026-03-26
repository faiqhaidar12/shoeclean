<div class="py-8">
    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('customers.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl italic">Tambah Pelanggan Baru</h1>
    </div>

    <!-- Form Container -->
    <div class="max-w-3xl">
        <div class="card-artisan p-10 lg:p-16">
            <form wire:submit="save" class="space-y-12">
                <div class="space-y-10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-secondary">Data Pelanggan</h4>
                    
                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Nama Pelanggan *</label>
                        <input type="text" wire:model="name" class="artisan-input" placeholder="Contoh: Budi Santoso">
                        @error('name') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Nomor HP / WhatsApp *</label>
                        <input type="text" wire:model="phone" class="artisan-input font-mono" placeholder="08xxxxxxxxxx">
                        @error('phone') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Alamat Lengkap</label>
                        <textarea wire:model="address" rows="4" class="artisan-input" placeholder="Masukkan alamat lengkap pelanggan (opsional)..."></textarea>
                        @error('address') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-6 pt-10 border-t border-artisan-outline/10">
                    <a href="{{ route('customers.index') }}" class="btn-artisan-secondary flex-1 text-center">Batalkan Tambah</a>
                    <button type="submit" class="btn-artisan-primary flex-1 group">
                        <span wire:loading.remove>Simpan Pelanggan</span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
