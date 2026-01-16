<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">⚙️ Services</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola layanan cuci sepatu</p>
        </div>
        <a href="{{ route('services.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Service
        </a>
    </div>

    @if($services->isEmpty())
        <div class="card text-center py-12">
            <div class="text-5xl mb-4">🧼</div>
            <p class="text-gray-500 text-lg">Belum ada service</p>
        </div>
    @else
        <!-- Responsive Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($services as $service)
                <div class="card hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $service->name }}</h3>
                            <p class="text-2xl font-bold text-primary-600 mt-2">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($service->is_per_item)
                                    <span class="badge badge-info">Per Item</span>
                                @else
                                    <span class="badge bg-gray-100 text-gray-600">Flat Rate</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('services.edit', $service->id) }}" class="p-2 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-primary-50" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button 
                                wire:click="delete({{ $service->id }})" 
                                wire:confirm="Hapus service '{{ $service->name }}'? Data yang sudah dihapus tidak bisa dikembalikan."
                                class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" 
                                title="Hapus"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @if($service->description)
                        <p class="text-sm text-gray-500 mt-3 border-t pt-3">{{ $service->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $services->links() }}</div>
    @endif
</div>
