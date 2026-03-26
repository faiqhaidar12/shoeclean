<div class="py-8">
    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('users.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Registri
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl italic">Ubah Kredensial Artisan</h1>
    </div>

    <!-- Form Container -->
    <div class="max-w-3xl">
        <div class="card-artisan p-10 lg:p-16">
            <form wire:submit="save" class="space-y-12">
                <div class="space-y-10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-secondary">Kredensial Profesional</h4>
                    
                    <div class="grid grid-cols-1 gap-10 sm:grid-cols-2">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Nama Lengkap *</label>
                            <input type="text" wire:model="name" class="artisan-input" placeholder="Nama Artisan">
                            @error('name') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Email Profesional *</label>
                            <input type="email" wire:model="email" class="artisan-input" placeholder="nama@shoeclean.com">
                            @error('email') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-10 sm:grid-cols-2">
                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Kata Sandi <span class="text-[8px] opacity-50 ml-2">(Kosongkan jika tidak ingin diubah)</span></label>
                            <input type="password" wire:model="password" class="artisan-input" placeholder="••••••••">
                            @error('password') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Peran Hierarki *</label>
                            <select wire:model="role" class="artisan-input">
                                <option value="staff">Staf Karyawan</option>
                                <option value="admin">Administrator Outlet</option>
                                @if(auth()->user()->isOwner())
                                    <option value="owner">Pemilik Bisnis</option>
                                @endif
                            </select>
                            @error('role') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Penugasan Outlet *</label>
                        <select wire:model="outlet_id" class="artisan-input">
                            <option value="">Pilih Outlet yang Ditugaskan</option>
                            @foreach($availableOutlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <p class="text-red-500 text-[10px] mt-3 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-6 pt-10">
                    <a href="{{ route('users.index') }}" class="btn-artisan-secondary flex-1 text-center">Batalkan Perubahan</a>
                    <button type="submit" class="btn-artisan-primary flex-1">Perbarui Kredensial</button>
                </div>
            </form>
        </div>
    </div>
</div>
