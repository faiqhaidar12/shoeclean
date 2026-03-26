<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-8 mb-16">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-4">Pelanggan</p>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">Daftar Pelanggan</h1>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-[0.2em] mt-4">Individu terhormat dalam ekosistem layanan kami</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn-artisan-primary">
            Tambah Pelanggan Baru
        </a>
    </div>

    <!-- Search & Outlet Filter -->
    <div class="space-y-12 mb-16">
        <!-- Search -->
        <div class="relative group max-w-2xl">
            <div class="absolute inset-y-0 left-8 flex items-center pointer-events-none text-artisan-primary/20 group-focus-within:text-artisan-secondary transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari pelanggan berdasarkan nama atau kontak..." 
                class="artisan-input !pl-20 !py-6 !bg-artisan-surface-low/50 hover:!bg-artisan-surface-low transition-all">
        </div>

        <!-- Outlet Filter Tabs -->
        <div class="flex items-center gap-6 overflow-x-auto no-scrollbar pb-2">
            <button wire:click="selectOutlet(null)" 
                class="flex-shrink-0 px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-[0.2em] transition-all {{ !$selectedOutletId ? 'bg-artisan-primary text-white shadow-artisan-sm' : 'bg-artisan-surface-low text-artisan-primary/40 hover:text-artisan-secondary' }}">
                Semua Outlet
            </button>
            @foreach($outlets as $outlet)
                <button wire:click="selectOutlet({{ $outlet->id }})" 
                    class="flex-shrink-0 px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center gap-3 {{ $selectedOutletId == $outlet->id ? 'bg-artisan-primary text-white shadow-artisan-sm' : 'bg-artisan-surface-low text-artisan-primary/40 hover:text-artisan-secondary' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1-4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $outlet->name }}
                </button>
            @endforeach
        </div>
    </div>

    @if($customers->isEmpty())
        <div class="card-artisan p-20 text-center">
            <div class="w-24 h-24 bg-artisan-surface-low rounded-[2.5rem] flex items-center justify-center text-artisan-secondary/20 mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="headline-editorial text-2xl italic mb-4">Belum Ada Pelanggan</h3>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">Menunggu penambahan pelanggan baru</p>
        </div>
    @else
        <!-- Mobile: Artisan List -->
        <div class="block lg:hidden space-y-6">
            @foreach($customers as $customer)
                <div class="card-artisan p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary text-xl font-manrope font-black italic shadow-inner">
                                {{ substr($customer->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-manrope font-black text-artisan-primary italic">{{ $customer->name }}</h3>
                                <p class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/40 mt-1">{{ $customer->phone }}</p>
                            </div>
                        </div>
                        @if(auth()->user()->isOwner() || auth()->user()->outlet_id == $customer->outlet_id)
                        <a href="{{ route('customers.edit', $customer->id) }}" class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/20 hover:text-artisan-secondary hover:bg-artisan-secondary/5 transition-all duration-300 active:scale-95 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @endif
                    </div>
                    @if($customer->outlet)
                        <div class="flex items-center gap-3 py-3 px-4 bg-artisan-surface-low/50 rounded-xl inline-flex">
                            <svg class="w-3.5 h-3.5 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1-4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="text-[9px] font-black uppercase tracking-widest text-artisan-secondary">{{ $customer->outlet->name }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Desktop: Fluid Table -->
        <div class="hidden lg:block overflow-x-auto no-scrollbar">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full border-separate border-spacing-y-4">
                    <thead>
                        <tr>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-artisan-primary/30 uppercase tracking-[0.2em]">Nama Pelanggan</th>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-artisan-primary/30 uppercase tracking-[0.2em]">Kontak</th>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-artisan-primary/30 uppercase tracking-[0.2em]">Outlet</th>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-artisan-primary/30 uppercase tracking-[0.2em]">Alamat Pelanggan</th>
                            <th class="px-10 py-6 text-right text-[10px] font-black text-artisan-primary/30 uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="space-y-4">
                        @foreach($customers as $customer)
                            <tr class="group">
                                <td class="px-10 py-8 bg-white first:rounded-l-[2rem] border-y border-artisan-outline group-hover:border-artisan-secondary/20 transition-all duration-500">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary text-xl font-manrope font-black italic shadow-inner group-hover:bg-artisan-secondary/10 transition-colors">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                        <p class="text-lg font-manrope font-black text-artisan-primary italic group-hover:text-artisan-secondary transition-colors">{{ $customer->name }}</p>
                                    </div>
                                </td>
                                <td class="px-10 py-8 bg-white border-y border-artisan-outline font-manrope font-black text-artisan-primary italic text-sm group-hover:border-artisan-secondary/20 transition-all duration-500">
                                    {{ $customer->phone }}
                                </td>
                                <td class="px-10 py-8 bg-white border-y border-artisan-outline group-hover:border-artisan-secondary/20 transition-all duration-500">
                                    @if($customer->outlet)
                                        <span class="inline-flex items-center gap-3 px-6 py-2 bg-artisan-surface-low text-artisan-secondary text-[10px] font-black uppercase tracking-widest rounded-full group-hover:bg-artisan-secondary group-hover:text-white transition-all duration-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1-4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            {{ $customer->outlet->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-10 py-8 bg-white border-y border-artisan-outline text-artisan-primary/40 text-[11px] font-black uppercase tracking-widest leading-relaxed max-w-xs truncate group-hover:border-artisan-secondary/20 transition-all duration-500">
                                    {{ $customer->address ?? 'Tidak Ada Alamat' }}
                                </td>
                                <td class="px-10 py-8 bg-white last:rounded-r-[2rem] border-y border-artisan-outline group-hover:border-artisan-secondary/20 transition-all duration-500 text-right">
                                    @if(auth()->user()->isOwner() || auth()->user()->outlet_id == $customer->outlet_id)
                                        <a href="{{ route('customers.edit', $customer->id) }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-secondary hover:text-white hover:bg-artisan-secondary transition-all duration-300 py-3 px-8 bg-artisan-surface-low rounded-full active:scale-95 shadow-artisan-sm">Ubah Data</a>
                                    @else
                                        <span class="text-[8px] font-black uppercase tracking-widest text-artisan-primary/20 italic">Akses Terbatas</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-16 sm:px-10">{{ $customers->links() }}</div>
    @endif
</div>
