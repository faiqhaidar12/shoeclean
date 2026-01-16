<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">🏪 Outlets</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua outlet</p>
        </div>
        <a href="{{ route('outlets.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Outlet
        </a>
    </div>

    @if($outlets->isEmpty())
        <div class="card text-center py-12">
            <div class="text-5xl mb-4">🏬</div>
            <p class="text-gray-500 text-lg">Belum ada outlet</p>
        </div>
    @else
        <!-- Responsive Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($outlets as $outlet)
                <div class="card hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-lg">{{ $outlet->name }}</h3>
                            @if($outlet->phone)
                                <p class="text-sm text-gray-600 mt-1">📞 {{ $outlet->phone }}</p>
                            @endif
                            @if($outlet->address)
                                <p class="text-sm text-gray-500 mt-1">📍 {{ Str::limit($outlet->address, 50) }}</p>
                            @endif
                        </div>
                        <a href="{{ route('outlets.edit', $outlet->id) }}" class="p-2 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-primary-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                    <div class="mt-4 pt-4 border-t flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $outlet->users_count ?? 0 }} staff</span>
                        <span class="badge badge-success">Active</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $outlets->links() }}</div>
    @endif
</div>
