<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <div class="p-2 bg-primary-100 text-primary-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Orders</h1>
                    <p class="text-sm text-gray-500">Kelola semua pesanan</p>
                </div>
            </div>
        </div>
        <a href="{{ route('orders.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Order
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari invoice atau customer..." 
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            </div>
            <select wire:model.live="status" class="w-full sm:w-auto px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm bg-white">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="ready">Ready</option>
                <option value="picked_up">Picked Up</option>
            </select>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="card text-center py-12">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-gray-500 text-lg">Belum ada order</p>
            <a href="{{ route('orders.create') }}" class="btn-primary mt-4 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Buat Order Pertama
            </a>
        </div>
    @else
        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @foreach($orders as $order)
                <div class="card block hover:shadow-md transition-shadow">
                    <a href="{{ route('orders.view', $order->id) }}" class="block">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-mono text-sm text-primary-600 font-medium">{{ $order->invoice_number }}</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $order->customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->customer->phone }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                <div class="flex gap-1 mt-2 justify-end">
                                    <span class="badge {{ match($order->status) {
                                        'completed', 'picked_up' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        'ready' => 'badge-info',
                                        default => 'badge-warning'
                                    } }}">{{ ucfirst($order->status) }}</span>
                                    <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @php
                        $hasPending = $order->payments->where('status', 'pending')->isNotEmpty();
                        $hasFailed = $order->payments->where('status', 'failed')->isNotEmpty();
                    @endphp
                    
                    @if($order->payment_status === 'paid')
                        {{-- Already paid, no action needed --}}
                    @elseif($hasPending)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-2 text-yellow-600">
                                <span class="badge bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                <span class="text-xs">Menunggu pembayaran online</span>
                            </div>
                        </div>
                    @elseif($hasFailed)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="badge badge-danger">❌ Failed</span>
                                <span class="text-xs text-gray-500">Pembayaran gagal</span>
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    wire:click="markPaid({{ $order->id }})" 
                                    wire:confirm="Tandai lunas dengan pembayaran cash?"
                                    class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700"
                                >
                                    💵 Bayar Cash
                                </button>
                                <button 
                                    wire:click="payOnline({{ $order->id }})"
                                    class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                >
                                    🔄 Coba Lagi
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                            <button 
                                wire:click="markPaid({{ $order->id }})" 
                                wire:confirm="Tandai lunas dengan pembayaran cash?"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700"
                            >
                                💵 Bayar Cash
                            </button>
                            <button 
                                wire:click="payOnline({{ $order->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                            >
                                💳 Midtrans
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-primary-600">{{ $order->invoice_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->customer->phone }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="badge {{ match($order->status) {
                                        'completed', 'picked_up' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        'ready' => 'badge-info',
                                        default => 'badge-warning'
                                    } }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $latestPayment = $order->payments->sortByDesc('created_at')->first();
                                        $hasPending = $order->payments->where('status', 'pending')->isNotEmpty();
                                        $hasFailed = $order->payments->where('status', 'failed')->isNotEmpty();
                                    @endphp
                                    
                                    @if($order->payment_status === 'paid')
                                        <span class="badge badge-success">✅ Paid</span>
                                    @elseif($hasPending)
                                        <div class="flex flex-col gap-1">
                                            <span class="badge bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                            <span class="text-xs text-gray-500">Menunggu pembayaran</span>
                                        </div>
                                    @elseif($hasFailed)
                                        <div class="flex flex-col gap-1">
                                            <span class="badge badge-danger">❌ Failed</span>
                                            <div class="flex gap-1 mt-1">
                                                <button 
                                                    wire:click="markPaid({{ $order->id }})" 
                                                    wire:confirm="Tandai lunas dengan pembayaran cash?"
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-emerald-600 rounded hover:bg-emerald-700"
                                                >
                                                    💵 Cash
                                                </button>
                                                <button 
                                                    wire:click="payOnline({{ $order->id }})"
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700"
                                                >
                                                    🔄 Retry
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="badge bg-gray-100 text-gray-600">Unpaid</span>
                                            <button 
                                                wire:click="markPaid({{ $order->id }})" 
                                                wire:confirm="Tandai lunas dengan pembayaran cash?"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-emerald-600 rounded hover:bg-emerald-700"
                                                title="Bayar Cash"
                                            >
                                                💵 Cash
                                            </button>
                                            <button 
                                                wire:click="payOnline({{ $order->id }})"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700"
                                                title="Bayar Online"
                                            >
                                                💳 Online
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-900">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('orders.view', $order->id) }}" class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
