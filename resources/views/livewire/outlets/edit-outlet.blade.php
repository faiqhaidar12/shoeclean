<div class="py-8">
    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('outlets.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Registri
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl italic">Ubah Registri Outlet</h1>
    </div>

    <!-- Form Container -->
    <div class="max-w-3xl">
        <div class="card-artisan p-10 lg:p-16">
            <form wire:submit="save" class="space-y-10">
                <div class="grid grid-cols-1 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Identitas Outlet *</label>
                        <input type="text" wire:model.live="name" class="artisan-input" placeholder="Contoh: Shoe Clean Heritage Outlet">
                        <p class="text-[9px] text-artisan-primary/30 mt-2 font-bold uppercase tracking-widest">Slug: <span class="text-artisan-secondary">{{ $slug ?: '-' }}</span></p>
                        @error('name') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Protokol Kontak</label>
                            <input type="text" wire:model="phone" class="artisan-input" placeholder="+62xxxxxxxxxx">
                            @error('phone') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div class="rounded-[2rem] border border-slate-100 bg-slate-50/60 p-6">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 mb-3">Pengiriman Cabang</p>
                            <p class="text-[11px] leading-relaxed text-artisan-primary/55 font-bold">Atur layanan jemput dan antar berdasarkan radius dasar serta biaya tambahan per km.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-6 space-y-5">
                            <label class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary">Layanan Jemput</p>
                                    <p class="mt-2 text-[11px] font-bold leading-relaxed text-artisan-primary/50">Cabang menjemput sepatu dari lokasi pelanggan.</p>
                                </div>
                                <input type="checkbox" wire:model.live="pickup_enabled" class="mt-1 h-5 w-5 rounded border-slate-300 text-artisan-secondary focus:ring-artisan-secondary">
                            </label>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 {{ !$pickup_enabled ? 'opacity-50' : '' }}">
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/35 uppercase tracking-widest mb-3">Jarak Dasar (Km)</label>
                                    <input type="number" min="0" step="0.1" wire:model="pickup_base_distance_km" class="artisan-input" @disabled(!$pickup_enabled)>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/35 uppercase tracking-widest mb-3">Biaya Dasar</label>
                                    <input type="number" min="0" wire:model="pickup_base_fee" class="artisan-input" @disabled(!$pickup_enabled)>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/35 uppercase tracking-widest mb-3">Tambah / Km</label>
                                    <input type="number" min="0" wire:model="pickup_extra_fee_per_km" class="artisan-input" @disabled(!$pickup_enabled)>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-6 space-y-5">
                            <label class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary">Layanan Antar</p>
                                    <p class="mt-2 text-[11px] font-bold leading-relaxed text-artisan-primary/50">Cabang mengantar hasil perawatan ke lokasi pelanggan.</p>
                                </div>
                                <input type="checkbox" wire:model.live="delivery_enabled" class="mt-1 h-5 w-5 rounded border-slate-300 text-artisan-secondary focus:ring-artisan-secondary">
                            </label>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 {{ !$delivery_enabled ? 'opacity-50' : '' }}">
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/35 uppercase tracking-widest mb-3">Jarak Dasar (Km)</label>
                                    <input type="number" min="0" step="0.1" wire:model="delivery_base_distance_km" class="artisan-input" @disabled(!$delivery_enabled)>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/35 uppercase tracking-widest mb-3">Biaya Dasar</label>
                                    <input type="number" min="0" wire:model="delivery_base_fee" class="artisan-input" @disabled(!$delivery_enabled)>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-artisan-primary/35 uppercase tracking-widest mb-3">Tambah / Km</label>
                                    <input type="number" min="0" wire:model="delivery_extra_fee_per_km" class="artisan-input" @disabled(!$delivery_enabled)>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Wilayah</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <select wire:model.live="province_id" class="artisan-input w-full">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($this->provinces as $prov)
                                        <option value="{{ $prov['id'] }}">{{ $prov['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('province_id') <p class="text-red-500 text-[10px] mt-2 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <select wire:model.live="city_id" class="artisan-input w-full" {{ empty($cities) ? 'disabled' : '' }}>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('city_id') <p class="text-red-500 text-[10px] mt-2 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <select wire:model.live="district_id" class="artisan-input w-full" {{ empty($districts) ? 'disabled' : '' }}>
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district['id'] }}">{{ $district['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('district_id') <p class="text-red-500 text-[10px] mt-2 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Koordinat Geografis</label>
                        <textarea wire:model="address" rows="4" class="artisan-input" placeholder="Alamat fisik lengkap untuk registri outlet..."></textarea>
                        @error('address') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">QRIS Outlet</label>
                            <input type="file" wire:model="qris_image" accept=".jpg,.jpeg,.png,.webp" class="artisan-input file:mr-4 file:border-0 file:bg-artisan-primary file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-white">
                            <p class="text-[9px] text-artisan-primary/30 mt-2 font-bold uppercase tracking-widest">Biarkan kosong jika tidak ingin mengganti QRIS.</p>
                            @error('qris_image') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        @if($outlet->qris_image_path && !$remove_qris && !$qris_image)
                            <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-artisan-secondary mb-4">QRIS Saat Ini</p>
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($outlet->qris_image_path) }}" alt="QRIS outlet saat ini" class="max-h-80 rounded-2xl border border-slate-100 bg-white object-contain">
                                        @if($outlet->qris_image_original_name)
                                            <p class="text-[9px] text-artisan-primary/30 mt-3 font-bold uppercase tracking-widest">{{ $outlet->qris_image_original_name }}</p>
                                        @endif
                                    </div>
                                    <button type="button" wire:click="removeQris" class="btn-artisan-secondary !py-3 !px-5">Hapus QRIS</button>
                                </div>
                            </div>
                        @endif

                        @if($qris_image)
                            <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-6">
                                <p class="text-[9px] font-black uppercase tracking-widest text-artisan-secondary mb-4">Preview QRIS Baru</p>
                                <img src="{{ $qris_image->temporaryUrl() }}" alt="Preview QRIS baru" class="max-h-80 rounded-2xl border border-slate-100 bg-white object-contain">
                            </div>
                        @endif

                        @if($remove_qris)
                            <div class="rounded-[2rem] border border-red-100 bg-red-50 p-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-red-500">QRIS outlet akan dihapus saat perubahan disimpan.</p>
                                <button type="button" wire:click="keepQris" class="btn-artisan-secondary !py-3 !px-5">Batalkan Hapus</button>
                            </div>
                        @endif

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Catatan QRIS</label>
                            <textarea wire:model="qris_notes" rows="3" class="artisan-input" placeholder="Contoh: Scan QRIS lalu bayar sesuai total invoice."></textarea>
                            @error('qris_notes') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-6 pt-6">
                    <a href="{{ route('outlets.index') }}" class="btn-artisan-secondary flex-1 text-center">Batalkan Perubahan</a>
                    <button type="submit" class="btn-artisan-primary flex-1">Perbarui Registri</button>
                </div>
            </form>
        </div>
    </div>
</div>
