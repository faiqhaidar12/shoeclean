<div class="relative w-full" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        type="button" 
        class="w-full flex items-center justify-between px-4 py-3 bg-white border border-gray-200 rounded-xl hover:border-primary-500 hover:ring-1 hover:ring-primary-500 transition-all duration-200 group"
    >
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="text-left">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Outlet Aktif</p>
                <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">
                    @if($currentOutletId)
                        {{ \App\Models\Outlet::find($currentOutletId)->name ?? 'Unknown Outlet' }}
                    @else
                        Semua Outlet
                    @endif
                </p>
            </div>
        </div>
        <svg 
            class="w-5 h-5 text-gray-400 transition-transform duration-200" 
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
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute left-0 right-0 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden divide-y divide-gray-100"
        style="display: none;"
    >
        <div class="p-1">
            <button 
                wire:click="switchOutlet('all')" 
                @click="open = false"
                class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ !$currentOutletId ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
            >
                <div class="flex items-center gap-2">
                    <span class="text-lg">🏢</span>
                    <span>Semua Outlet</span>
                </div>
                @if(!$currentOutletId)
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @endif
            </button>
        </div>

        <div class="p-1 max-h-60 overflow-y-auto">
            @foreach($outlets as $outlet)
                <button 
                    wire:click="switchOutlet({{ $outlet->id }})" 
                    @click="open = false"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ $currentOutletId == $outlet->id ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50' }}"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🏪</span>
                        <div class="text-left">
                            <span class="block truncate">{{ $outlet->name }}</span>
                            <span class="block text-xs text-gray-500 font-normal truncate">{{ $outlet->address }}</span>
                        </div>
                    </div>
                    @if($currentOutletId == $outlet->id)
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
            @endforeach
        </div>
        
        <div class="p-2 bg-gray-50 text-center">
            <a href="{{ route('outlets.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium hover:underline">
                Kelola Outlet &rarr;
            </a>
        </div>
    </div>
</div>
