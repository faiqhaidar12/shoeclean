<div class="py-8">
    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('expenses.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Buku Besar
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl italic">Catat Pengeluaran Baru</h1>
    </div>

    <!-- Form Container -->
    <div class="max-w-3xl">
        <div class="card-artisan p-10 lg:p-16">
            <form wire:submit="save" class="space-y-12">
                <div class="space-y-10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-secondary">Rincian Alokasi</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Tanggal Alokasi *</label>
                            <input type="date" wire:model="expense_date" class="artisan-input">
                            @error('expense_date') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Klasifikasi Sumber Daya *</label>
                            <select wire:model="category" class="artisan-input">
                                <option value="">Pilih Klasifikasi</option>
                                <option value="supplies">Perlengkapan Material</option>
                                <option value="utilities">Utilitas Outlet</option>
                                <option value="salary">Honorarium Staf</option>
                                <option value="rent">Sewa Outlet</option>
                                <option value="maintenance">Pemeliharaan Fasilitas</option>
                                <option value="other">Lain-lain</option>
                            </select>
                            @error('category') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Jumlah Alokasi (Rp) *</label>
                        <input type="number" wire:model="amount" class="artisan-input" placeholder="Contoh: 250000">
                        @error('amount') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Keterangan Alokasi</label>
                        <textarea wire:model="description" rows="4" class="artisan-input" placeholder="Berikan keterangan rinci untuk alokasi ini..."></textarea>
                        @error('description') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-6 pt-10 border-t border-artisan-outline/10">
                    <a href="{{ route('expenses.index') }}" class="btn-artisan-secondary flex-1 text-center">Batalkan Alokasi</a>
                    <button type="submit" class="btn-artisan-primary flex-1">Simpan Alokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
