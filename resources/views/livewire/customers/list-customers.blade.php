<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">👥 Customers</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data pelanggan</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Customer
        </a>
    </div>

    <!-- Search -->
    <div class="card mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau telepon..." 
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
    </div>

    @if($customers->isEmpty())
        <div class="card text-center py-12">
            <div class="text-5xl mb-4">👤</div>
            <p class="text-gray-500 text-lg">Belum ada customer</p>
        </div>
    @else
        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @foreach($customers as $customer)
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ substr($customer->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                                <p class="text-sm text-gray-500">{{ $customer->phone }}</p>
                            </div>
                        </div>
                        <a href="{{ route('customers.edit', $customer->id) }}" class="p-2 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-primary-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                    @if($customer->address)
                        <p class="text-xs text-gray-400 mt-2 pl-13">📍 {{ Str::limit($customer->address, 40) }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block card overflow-hidden p-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Address</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $customer->phone }}</td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $customer->address ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('customers.edit', $customer->id) }}" class="text-primary-600 hover:text-primary-800 font-medium text-sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $customers->links() }}</div>
    @endif
</div>
