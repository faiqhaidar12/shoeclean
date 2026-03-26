<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-8 mb-16">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-4">Pemasaran</p>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">Penawaran Promo</h1>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-[0.2em] mt-4">Insentif strategis untuk pelanggan terbaik kami</p>
        </div>
        <a href="{{ route('promos.create') }}" class="btn-artisan-primary">
            Buat Promo Baru
        </a>
    </div>

    <!-- Trace Incentive -->
    <div class="mb-12 relative group">
        <div class="absolute inset-y-0 left-8 flex items-center pointer-events-none text-artisan-primary/20 group-focus-within:text-artisan-secondary transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari promo berdasarkan kode..." 
            class="artisan-input !pl-20 !py-6 !bg-artisan-surface-low/50 hover:!bg-artisan-surface-low transition-all">
    </div>

    @if($promos->isEmpty())
        <div class="card-artisan p-20 text-center">
            <div class="w-24 h-24 bg-artisan-surface-low rounded-[2.5rem] flex items-center justify-center text-artisan-secondary/20 mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <h3 class="headline-editorial text-2xl italic mb-4">Belum Ada Promo</h3>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">Menunggu langkah strategis pemasaran</p>
        </div>
    @else
        <!-- Premium Proposition Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($promos as $promo)
                <div class="card-artisan p-10 group hover:shadow-artisan-lg transition-all duration-500 relative overflow-hidden {{ !$promo->is_active ? 'opacity-50 grayscale' : '' }}">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-artisan-secondary/5 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-artisan-secondary/10 transition-colors"></div>
                    
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <span class="font-mono text-xl font-black tracking-widest text-artisan-secondary">{{ $promo->code }}</span>
                                @if(!$promo->is_active)
                                    <span class="text-[8px] font-black uppercase tracking-[0.2em] bg-red-50 text-red-600 px-3 py-1.5 rounded-full">Nonaktif</span>
                                @endif
                            </div>
                            <h3 class="text-sm font-manrope font-black text-artisan-primary italic">{{ $promo->name }}</h3>
                            <p class="text-3xl font-manrope font-black text-artisan-primary mt-6">
                                @if($promo->type === 'percentage')
                                    {{ $promo->value }}% <span class="text-[10px] uppercase font-black tracking-widest text-artisan-primary/40 italic ml-2">Diskon</span>
                                @else
                                    <span class="text-[10px] uppercase font-black tracking-widest text-artisan-primary/40 italic mr-2">Rp</span> {{ number_format($promo->value, 0, ',', '.') }}
                                @endif
                            </p>
                            @if($promo->max_discount)
                                <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/30 mt-2">Maks. Diskon Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        
                        <div class="flex flex-col gap-3">
                            <button wire:click="toggle({{ $promo->id }})" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 active:scale-95 bg-artisan-surface-low shadow-sm {{ $promo->is_active ? 'text-emerald-600 hover:bg-emerald-600 hover:text-white' : 'text-artisan-primary/30 hover:bg-artisan-primary hover:text-white' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($promo->is_active)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                            </button>
                            <button wire:click="delete({{ $promo->id }})" wire:confirm="Hapus promo ini?" class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/20 hover:bg-red-600 hover:text-white transition-all duration-300 active:scale-95 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-artisan-outline/10 grid grid-cols-2 gap-6 relative z-10">
                        <div>
                            <p class="text-[8px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-1">Penggunaan</p>
                            <p class="text-xs font-manrope font-black text-artisan-primary">{{ $promo->used_count }} <span class="text-artisan-primary/30">/</span> {{ $promo->max_uses ?? '∞' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-1">Minimal Pesanan</p>
                            <p class="text-xs font-manrope font-black text-artisan-primary italic">Rp {{ number_format($promo->min_order ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/30">{{ $promo->start_date->format('d M') }} — {{ $promo->end_date->format('d M Y') }}</span>
                        </div>
                        @if($promo->outlet)
                            <span class="text-[9px] font-black uppercase tracking-widest text-artisan-secondary">{{ $promo->outlet->name }}</span>
                        @else
                            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Semua Outlet</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 sm:px-4">{{ $promos->links() }}</div>
    @endif
</div>
