<div class="relative w-full" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        type="button" 
        class="w-full flex items-center justify-between px-5 py-4 bg-artisan-surface-lowest rounded-2xl hover:bg-artisan-surface-low transition-all duration-300 group shadow-artisan-sm"
    >
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-artisan-surface-low flex items-center justify-center text-artisan-primary/40 group-hover:bg-artisan-secondary/10 group-hover:text-artisan-secondary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="text-left">
                <p class="text-[9px] text-artisan-primary/40 font-black uppercase tracking-[0.2em] mb-1">Outlet Aktif</p>
                <p class="text-sm font-manrope font-black text-artisan-primary truncate max-w-[140px] italic leading-tight">
                    @if($currentOutletId)
                        {{ \App\Models\Outlet::find($currentOutletId)->name ?? 'Unknown Outlet' }}
                    @else
                        Semua Outlet
                    @endif
                </p>
            </div>
        </div>
        <svg 
            class="w-5 h-5 text-artisan-primary/30 transition-transform duration-300 group-hover:text-artisan-secondary" 
            :class="open ? 'rotate-180' : ''"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div 
        x-show="open" 
        @click.away="open = false" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 right-0 mt-4 w-full bg-artisan-surface-lowest rounded-[2rem] shadow-artisan-lg z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="p-3">
            <button 
                wire:click="switchOutlet('all')" 
                @click="open = false"
                class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-sm transition-all duration-300 {{ !$currentOutletId ? 'bg-artisan-secondary/10 text-artisan-secondary font-black' : 'text-artisan-primary/60 hover:bg-artisan-surface-low hover:text-artisan-primary font-bold' }}"
            >
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ !$currentOutletId ? 'bg-artisan-secondary text-white shadow-artisan-sm' : 'bg-artisan-surface-low text-artisan-primary/40' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span>Semua Outlet</span>
                </div>
                @if(!$currentOutletId)
                    <svg class="w-5 h-5 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @endif
            </button>
        </div>

        <div class="px-3 pb-3 max-h-64 overflow-y-auto artisan-scrollbar space-y-1">
            @foreach($outlets as $outlet)
                <button 
                    wire:click="switchOutlet({{ $outlet->id }})" 
                    @click="open = false"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-sm transition-all duration-300 {{ $currentOutletId == $outlet->id ? 'bg-artisan-secondary/10 text-artisan-secondary' : 'text-artisan-primary/60 hover:bg-artisan-surface-low hover:text-artisan-primary' }}"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $currentOutletId == $outlet->id ? 'bg-artisan-secondary text-white shadow-artisan-sm' : 'bg-artisan-surface-low text-artisan-primary/40' }} transition-colors">
                            <span class="text-xs font-black">{{ substr($outlet->name, 0, 1) }}</span>
                        </div>
                        <div class="text-left">
                            <span class="block truncate font-bold {{ $currentOutletId == $outlet->id ? 'text-artisan-secondary' : 'text-artisan-primary' }}">{{ Str::limit($outlet->name, 20) }}</span>
                            @if($outlet->address)
                                <span class="block text-[9px] text-artisan-primary/40 font-bold uppercase tracking-widest truncate mt-1">{{ Str::limit($outlet->address, 25) }}</span>
                            @endif
                        </div>
                    </div>
                    @if($currentOutletId == $outlet->id)
                        <svg class="w-5 h-5 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
            @endforeach
        </div>
        
        <div class="py-5 bg-artisan-surface-low text-center rounded-b-[2rem]">
            <a href="{{ route('outlets.index') }}" class="text-[10px] text-artisan-secondary hover:text-artisan-primary font-black uppercase tracking-[0.2em] transition-colors flex items-center justify-center gap-2">
                Kelola Outlet
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</div>
