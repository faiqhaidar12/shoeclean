<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-8 mb-16">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-4">Ringkasan Jaringan</p>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">Registri Outlet</h1>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-[0.2em] mt-4">Mengawasi semua pusat restorasi aktif</p>
        </div>
        @if(auth()->user()->canCreateOutlet())
            <a href="{{ route('outlets.create') }}" class="btn-artisan-primary">
                Tambah Outlet Baru
            </a>
        @else
            <div class="flex flex-col items-end">
                <a href="{{ route('subscription') }}" class="btn-artisan-primary !bg-amber-100 !text-amber-700 hover:!bg-amber-500 hover:!text-white border border-amber-200 shadow-none">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Upgrade Paket Business
                </a>
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-red-500 mt-2">Maksimal {{ auth()->user()->maxOutlets() }} outlet pada paket {{ ucfirst(auth()->user()->currentPlan()) }}</p>
            </div>
        @endif
    </div>

    @if($outlets->isEmpty())
        <div class="card-artisan p-20 text-center">
            <div class="w-24 h-24 bg-artisan-surface-low rounded-[2.5rem] flex items-center justify-center text-artisan-secondary/20 mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="headline-editorial text-2xl italic mb-4">Belum Ada Outlet</h3>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">Mulai bangun jaringan artisan Anda</p>
        </div>
    @else
        <!-- Premium Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($outlets as $outlet)
                <div class="card-artisan p-10 group hover:shadow-artisan-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-artisan-secondary/5 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-artisan-secondary/10 transition-colors"></div>
                    
                    <div class="flex items-start justify-between relative z-10">
                        <div class="flex-1">
                            <h3 class="text-2xl font-manrope font-black text-artisan-primary italic group-hover:text-artisan-secondary transition-colors">{{ $outlet->name }}</h3>
                            <div class="space-y-4 mt-8">
                                @if($outlet->phone)
                                    <div class="flex items-center gap-3 text-artisan-primary/60">
                                        <svg class="w-4 h-4 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $outlet->phone }}</span>
                                    </div>
                                @endif
                                @if($outlet->address)
                                    <div class="flex items-start gap-3 text-artisan-primary/40 leading-relaxed">
                                        <svg class="w-4 h-4 mt-0.5 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="text-[9px] font-medium uppercase tracking-[0.1em]">{{ Str::limit($outlet->address, 60) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('outlets.edit', $outlet) }}" class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/30 hover:bg-artisan-primary hover:text-white transition-all duration-300 shadow-sm active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                    
                    <div class="mt-12 pt-8 flex items-center justify-between relative z-10">
                         <div class="flex items-center gap-2">
                             <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                             <span class="text-[9px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Sistem Aktif</span>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-artisan-secondary bg-artisan-secondary/5 px-4 py-1.5 rounded-full">
                            {{ $outlet->users_count ?? 0 }} Staf Outlet
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 sm:px-4">{{ $outlets->links() }}</div>
    @endif
</div>
