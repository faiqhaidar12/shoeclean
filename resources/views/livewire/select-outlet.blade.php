<div class="min-h-screen pb-20 selection:bg-artisan-secondary/30">
    <!-- Hero / Header Section -->
    <div class="relative bg-artisan-primary text-white overflow-hidden pb-24 pt-16">
        <!-- Abstract Decorative Elements -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-96 h-96 bg-artisan-secondary/10 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-80 h-80 bg-white/5 rounded-full blur-[80px]"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 backdrop-blur-md border border-white/10 rounded-full text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-8 animate-fade-in-down">
                <span class="w-1.5 h-1.5 rounded-full bg-artisan-secondary animate-ping"></span>
                Official Storefront
            </div>
            <h1 class="headline-editorial text-4xl sm:text-6xl italic leading-tight mb-6 translate-y-0 opacity-100 transition-all duration-700">Pilih Cabang <br class="sm:hidden"> Restorasi</h1>
            <p class="text-white/50 text-xs sm:text-sm font-bold uppercase tracking-[0.2em] max-w-lg mx-auto leading-relaxed">
                Temukan artisan terdekat untuk mengembalikan kemilau setiap langkah Anda.
            </p>
        </div>
    </div>

    <!-- Interface Section -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
        <!-- Control Center (Filters) -->
        <div class="bg-white/70 backdrop-blur-2xl border border-white rounded-[2.5rem] p-4 sm:p-6 shadow-artisan-lg mb-12">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <!-- Search Input -->
                <div class="relative flex-1 w-full group">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-artisan-primary/30 group-focus-within:text-artisan-secondary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live="search" type="text" 
                        class="block w-full pl-14 pr-6 py-4 bg-slate-50/50 border-none rounded-2xl text-[13px] font-bold text-artisan-primary placeholder-artisan-primary/20 focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none" 
                        placeholder="Nama cabang atau alamat...">
                </div>
                
                <div class="flex gap-4 w-full lg:w-auto h-full">
                    <div class="relative flex-1 lg:w-56 h-full">
                        <select wire:model.live="selected_province" 
                            class="block w-full px-6 py-4 bg-slate-50/50 border-none rounded-2xl text-[11px] font-black uppercase tracking-widest text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all appearance-none outline-none">
                            <option value="">Provinsi</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov->province_id }}">{{ $prov->province_name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none text-artisan-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <div class="relative flex-1 lg:w-56 h-full">
                        <select wire:model.live="selected_city" 
                            class="block w-full px-6 py-4 bg-slate-50/50 border-none rounded-2xl text-[11px] font-black uppercase tracking-widest text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all appearance-none outline-none disabled:opacity-50" 
                            {{ empty($selected_province) ? 'disabled' : '' }}>
                            <option value="">Kota/Kab</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none text-artisan-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outlets Listing -->
        @if($outlets->isEmpty())
            <div class="text-center py-24 bg-white/50 backdrop-blur-sm rounded-[3rem] border border-white flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mb-8 text-slate-200">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="headline-editorial text-2xl italic text-artisan-primary mb-2">Outlet Tidak Ditemukan</h3>
                <p class="text-[10px] text-artisan-primary/30 font-black uppercase tracking-[0.2em]">Coba perluas radius pencarian Anda</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($outlets as $branch)
                    <a href="{{ route('public.order', ['outlet' => $branch->slug, 'skip_branch' => 1]) }}" 
                        class="group bg-white rounded-[2.5rem] p-10 border border-slate-100/50 shadow-artisan transition-all duration-500 hover:-translate-y-2 hover:shadow-artisan-lg flex flex-col h-full relative overflow-hidden">
                        
                        <!-- Card Border Gradient Glow -->
                        <div class="absolute inset-0 border-2 border-transparent group-hover:border-artisan-secondary/10 rounded-[2.5rem] transition-all duration-500"></div>

                        <div class="flex items-start justify-between mb-10 relative z-10">
                            <div class="w-14 h-14 bg-artisan-primary text-white rounded-2xl flex items-center justify-center text-xl font-manrope font-black italic transition-all duration-500 group-hover:scale-110 group-hover:bg-artisan-secondary group-hover:shadow-lg group-hover:shadow-artisan-secondary/20">
                                {{ substr($branch->name, 0, 1) }}
                            </div>
                            <div class="p-3 rounded-full bg-slate-50 text-slate-200 transition-all duration-500 group-hover:bg-artisan-secondary group-hover:text-white group-hover:rotate-45 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10 flex-1 flex flex-col">
                            <div class="mb-4">
                                <span class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary bg-artisan-secondary/5 px-3 py-1 rounded-full mb-3 inline-block">Aktif</span>
                                <h3 class="headline-editorial text-2xl italic text-artisan-primary group-hover:text-artisan-secondary transition-colors duration-500">{{ $branch->name }}</h3>
                            </div>
                            
                            <div class="space-y-4 mt-auto">
                                <p class="text-[10px] text-artisan-primary/40 font-bold flex items-start gap-3 leading-relaxed">
                                    <svg class="w-4 h-4 shrink-0 text-artisan-secondary mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="line-clamp-2">{{ $branch->address }}</span>
                                </p>
                                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-artisan-primary/20">Mulai Order</span>
                                    <svg class="w-4 h-4 text-artisan-secondary opacity-0 group-hover:opacity-100 -translate-x-4 group-hover:translate-x-0 transition-all duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

