<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-8 mb-16">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-4">Katalog Layanan</p>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">Protokol Restorasi</h1>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-[0.2em] mt-4">Menetapkan standar perawatan artisan</p>
        </div>
        <a href="{{ route('services.create') }}" class="btn-artisan-primary">
            Tambah Protokol Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-10 p-6 bg-emerald-50 text-emerald-700 rounded-[2rem] text-[10px] font-black uppercase tracking-widest animate-fade-in shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($services->isEmpty())
        <div class="card-artisan p-20 text-center">
            <div class="w-24 h-24 bg-artisan-surface-low rounded-[2.5rem] flex items-center justify-center text-artisan-secondary/20 mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86 1.404l-1.396 1.117a2 2 0 01-1.127.426l-1.352.135a2 2 0 01-2.008-1.503l-.403-1.61a2 2 0 01.328-1.645l1.048-1.402a2 2 0 00.322-1.666l-.504-2.115a2 2 0 01.554-1.854l1.248-1.248a2 2 0 012.356-.37l2.126 1.063a2 2 0 001.366.184l2.16-.54a2 2 0 012.067 2.067l-.54 2.16a2 2 0 00.184 1.366l1.063 2.126a2 2 0 01-.37 2.356l-1.248 1.248z"/></svg>
            </div>
            <h3 class="headline-editorial text-2xl italic mb-4">Belum Ada Protokol</h3>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">Tentukan keunggulan layanan Anda</p>
        </div>
    @else
        <!-- Premium Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $service)
                <div class="card-artisan p-10 group hover:shadow-artisan-lg transition-all duration-500 relative overflow-hidden {{ $service->status === 'inactive' ? 'opacity-50 grayscale' : '' }}">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-artisan-secondary/5 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-artisan-secondary/10 transition-colors"></div>
                    
                    <div class="flex items-start justify-between relative z-10">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h3 class="text-2xl font-manrope font-black text-artisan-primary italic group-hover:text-artisan-secondary transition-colors">{{ $service->name }}</h3>
                            </div>
                            
                            <div class="mt-8 space-y-1">
                                <p class="text-3xl font-manrope font-black text-artisan-primary italic">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-primary/30">Tingkat Standar / Per {{ ucfirst($service->unit) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a href="{{ route('services.edit', $service->id) }}" class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/30 hover:bg-artisan-primary hover:text-white transition-all duration-300 shadow-sm active:scale-95" title="Ubah Protokol">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button 
                                wire:click="delete({{ $service->id }})" 
                                wire:confirm="Arsipkan protokol '{{ $service->name }}'? Tindakan ini dicatat di buku besar."
                                class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/20 hover:bg-red-600 hover:text-white transition-all duration-300 shadow-sm active:scale-95"
                                title="Hapus"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    
                    @if($service->outlet)
                        <div class="mt-12 pt-8 border-t border-artisan-outline/10 flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">{{ $service->outlet->name }}</span>
                            </div>
                            @if($service->status === 'inactive')
                                <span class="text-[8px] font-black uppercase tracking-widest text-red-600 bg-red-50 px-3 py-1 rounded-full">Nonaktif</span>
                            @else
                                <span class="text-[8px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Tersedia</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-16 sm:px-4">{{ $services->links() }}</div>
    @endif
</div>
