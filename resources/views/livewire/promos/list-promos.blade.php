<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">🎁 Promos</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kode promo dan diskon</p>
        </div>
        <a href="{{ route('promos.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Promo
        </a>
    </div>

    <!-- Search -->
    <div class="card mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode promo..." 
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
    </div>

    @if($promos->isEmpty())
        <div class="card text-center py-12">
            <div class="text-5xl mb-4">🎫</div>
            <p class="text-gray-500 text-lg">Belum ada promo</p>
        </div>
    @else
        <!-- Responsive Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($promos as $promo)
                <div class="card {{ !$promo->is_active ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-lg text-primary-600">{{ $promo->code }}</span>
                                @if(!$promo->is_active)
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </div>
                            <p class="text-2xl font-bold mt-2">
                                @if($promo->type === 'percentage')
                                    {{ $promo->value }}%
                                @else
                                    Rp {{ number_format($promo->value, 0, ',', '.') }}
                                @endif
                            </p>
                            @if($promo->max_discount)
                                <p class="text-xs text-gray-500">Max: Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        <button wire:click="toggle({{ $promo->id }})" class="p-2 rounded-lg {{ $promo->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($promo->is_active)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @endif
                            </svg>
                        </button>
                    </div>
                    <div class="mt-3 pt-3 border-t flex items-center justify-between text-xs text-gray-500">
                        <span>Min: Rp {{ number_format($promo->min_order ?? 0, 0, ',', '.') }}</span>
                        <span>Used: {{ $promo->used_count }}/{{ $promo->max_usage ?? '∞' }}</span>
                    </div>
                    @if($promo->valid_until)
                        <p class="text-xs text-gray-400 mt-1">Expires: {{ $promo->valid_until->format('d M Y') }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $promos->links() }}</div>
    @endif
</div>
