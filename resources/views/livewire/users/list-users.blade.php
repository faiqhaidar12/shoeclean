<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">👤 Users</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pengguna sistem</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add User
        </a>
    </div>

    <!-- Search -->
    <div class="card mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..." 
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
    </div>

    @if($users->isEmpty())
        <div class="card text-center py-12">
            <div class="text-5xl mb-4">👥</div>
            <p class="text-gray-500 text-lg">Belum ada user</p>
        </div>
    @else
        <!-- Mobile & Desktop: Card Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($users as $user)
                <div class="card">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <span class="badge mt-1 {{ match($user->role) {
                                    'owner' => 'bg-purple-100 text-purple-800',
                                    'admin' => 'badge-info',
                                    default => 'bg-gray-100 text-gray-600'
                                } }}">{{ ucfirst($user->role) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-primary-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                    @if($user->outlet)
                        <p class="text-xs text-gray-400 mt-3 pt-3 border-t">📍 {{ $user->outlet->name }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</div>
