<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-primary-600 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Orders
            </a>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 font-mono">{{ $order->invoice_number }}</h1>
            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }} • {{ $order->outlet->name }}</p>
        </div>
        <a href="{{ route('orders.print', $order->id) }}" target="_blank" class="btn-secondary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Invoice
        </a>
    </div>

    <!-- Status Banner -->
    <div class="card mb-6 {{ match($order->status) {
        'completed', 'picked_up' => 'bg-emerald-50 border-emerald-200',
        'cancelled' => 'bg-red-50 border-red-200',
        'ready' => 'bg-blue-50 border-blue-200',
        default => 'bg-amber-50 border-amber-200'
    } }}">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="text-3xl">
                    {{ match($order->status) {
                        'completed', 'picked_up' => '✅',
                        'cancelled' => '❌',
                        'ready' => '📦',
                        'processing' => '⚙️',
                        default => '⏳'
                    } }}
                </span>
                <div>
                    <p class="font-bold text-lg {{ match($order->status) {
                        'completed', 'picked_up' => 'text-emerald-700',
                        'cancelled' => 'text-red-700',
                        'ready' => 'text-blue-700',
                        default => 'text-amber-700'
                    } }}">{{ strtoupper($order->status) }}</p>
                    <p class="text-sm text-gray-600">{{ $order->payment_status === 'paid' ? '💰 Lunas' : '💳 Belum Bayar' }}</p>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Items -->
            <div class="card">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">📋 Items</h3>
                
                <!-- Mobile: Stack View -->
                <div class="block sm:hidden space-y-3">
                    @foreach($order->items as $item)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between">
                                <p class="font-medium">{{ $item->service->name }}</p>
                                <p class="font-bold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</p>
                            </div>
                            <p class="text-sm text-gray-500">{{ $item->quantity }} {{ $item->unit }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                
                <!-- Desktop: Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2">Service</th>
                                <th class="text-center text-xs font-semibold text-gray-500 uppercase py-2">Qty</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase py-2">Price</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="border-b">
                                <td class="py-3 text-sm">{{ $item->service->name }}</td>
                                <td class="py-3 text-center text-sm">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="py-3 text-right text-sm">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-3 text-right font-bold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="py-3 text-right font-bold uppercase">Total</td>
                                <td class="py-3 text-right text-lg font-bold text-primary-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">👤 Customer</h3>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        {{ substr($order->customer->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900">{{ $order->customer->name }}</p>
                        <p class="text-gray-600">{{ $order->customer->phone }}</p>
                        @if($order->customer->address)
                            <p class="text-sm text-gray-500 mt-1">📍 {{ $order->customer->address }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- WhatsApp Button -->
                @php
                    $phone = preg_replace('/[^0-9]/', '', $order->customer->phone);
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }
                    $waMessage = urlencode("Halo {$order->customer->name}, pesanan Anda dengan nomor invoice {$order->invoice_number} sudah siap diambil. Terima kasih! - Shoe Clean");
                @endphp
                <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat WhatsApp
                </a>
                
                @if($order->notes)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm font-medium text-gray-500 mb-1">Notes</p>
                        <p class="text-gray-900">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1 space-y-4 sm:space-y-6">
            <!-- Status Workflow -->
            <div class="card">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">⚡ Actions</h3>
                
                <div class="space-y-2">
                    @if($order->status === 'pending')
                        <button wire:click="updateStatus('processing')" class="w-full py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium transition-colors">
                            ⚙️ Mulai Processing
                        </button>
                        <button wire:click="updateStatus('cancelled')" class="w-full py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium transition-colors">
                            ❌ Cancel Order
                        </button>
                    @endif

                    @if($order->status === 'processing')
                        <button wire:click="updateStatus('ready')" class="w-full py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium transition-colors">
                            📦 Mark Ready
                        </button>
                    @endif

                    @if($order->status === 'ready')
                        <button wire:click="updateStatus('picked_up')" class="w-full py-2.5 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 font-medium transition-colors">
                            ✅ Picked Up / Selesai
                        </button>
                    @endif

                    @if(in_array($order->status, ['completed', 'picked_up', 'cancelled']))
                        <p class="text-center py-4 text-gray-500">Order sudah selesai</p>
                    @endif
                </div>
            </div>

            <!-- Payment -->
            <div class="card">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">💰 Payment</h3>
                
                <div class="mb-4 p-3 rounded-lg {{ $order->payment_status === 'paid' ? 'bg-emerald-50' : 'bg-gray-50' }}">
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="font-bold text-lg {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-gray-900' }}">
                        {{ strtoupper($order->payment_status) }}
                    </p>
                </div>

                <!-- Payment History -->
                @if($order->payments->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-2">History</p>
                        <div class="space-y-2">
                            @foreach($order->payments as $payment)
                                <div class="flex items-center justify-between text-sm p-2 bg-gray-50 rounded">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $payment->method === 'cash' ? '💵' : '💳' }}</span>
                                        <div>
                                            <span class="font-medium">{{ ucfirst($payment->method) }}</span>
                                            <span class="badge text-xs ml-1 {{ match($payment->status) {
                                                'success' => 'badge-success',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'failed' => 'badge-danger',
                                                default => 'bg-gray-100 text-gray-600'
                                            } }}">{{ ucfirst($payment->status) }}</span>
                                        </div>
                                    </div>
                                    <span class="font-bold {{ match($payment->status) {
                                        'success' => 'text-emerald-600',
                                        'pending' => 'text-yellow-600',
                                        'failed' => 'text-red-600',
                                        default => 'text-gray-600'
                                    } }}">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                    <div class="space-y-2">
                        <button wire:click="markPaid" wire:confirm="Konfirmasi pembayaran cash Rp {{ number_format($order->total_price, 0, ',', '.') }}?" 
                            class="w-full py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium transition-colors">
                            💵 Pay Cash (Full)
                        </button>
                        <button wire:click="payOnline" class="w-full py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium transition-colors">
                            💳 Pay Online
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>

@script
<script>
    $wire.on('showSnapPopup', ({ snapToken }) => {
        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                $wire.checkPaymentStatus();
                window.location.reload();
            },
            onPending: function(result) {
                alert('Payment pending. Please complete the payment.');
                $wire.checkPaymentStatus();
            },
            onError: function(result) {
                alert('Payment failed. Please try again.');
            },
            onClose: function() {
                $wire.checkPaymentStatus();
            }
        });
    });
</script>
@endscript
