<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-primary-600 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Orders
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">🛒 New Order</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Customer Section -->
            <div class="card">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">👤 Customer</h3>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="customerSearch" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                        placeholder="Cari nama atau telepon...">
                    
                    @if(!empty($availableCustomers))
                        <div class="absolute z-50 w-full bg-white shadow-lg rounded-lg mt-1 border border-gray-200 max-h-48 overflow-y-auto">
                            @foreach($availableCustomers as $cust)
                                <div wire:click="selectCustomer({{ $cust->id }}, '{{ $cust->name }}')" 
                                    class="p-3 hover:bg-primary-50 cursor-pointer border-b last:border-b-0">
                                    <p class="font-medium">{{ $cust->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $cust->phone }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    @if($customer_id)
                        <div class="mt-2 flex items-center gap-2 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm font-medium">{{ $customerSearch }}</span>
                        </div>
                    @endif
                    @error('customer_id') <p class="text-red-500 text-sm mt-1">Customer wajib dipilih</p> @enderror
                </div>
            </div>

            <!-- Order Type -->
            <div class="card">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">🚚 Tipe Order</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $order_type === 'regular' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="order_type" value="regular" class="text-primary-600">
                        <div>
                            <p class="font-medium">Regular</p>
                            <p class="text-xs text-gray-500">Antar sendiri</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $order_type === 'pickup' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="order_type" value="pickup" class="text-primary-600">
                        <div>
                            <p class="font-medium">Pickup</p>
                            <p class="text-xs text-gray-500">+Rp {{ number_format($pickup_fee, 0, ',', '.') }}</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $order_type === 'delivery' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="order_type" value="delivery" class="text-primary-600">
                        <div>
                            <p class="font-medium">Delivery</p>
                            <p class="text-xs text-gray-500">+Rp {{ number_format($delivery_fee, 0, ',', '.') }}</p>
                        </div>
                    </label>
                </div>

                @if($order_type === 'pickup')
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pickup</label>
                        <textarea wire:model="pickup_address" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="Alamat pengambilan sepatu..."></textarea>
                    </div>
                @endif

                @if($order_type === 'delivery')
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Delivery</label>
                        <textarea wire:model="delivery_address" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="Alamat pengiriman setelah selesai..."></textarea>
                    </div>
                @endif
            </div>

            <!-- Items Section -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">🧼 Services</h3>
                    <button wire:click="addItem" type="button" class="text-sm text-primary-600 hover:text-primary-800 font-medium">+ Add Item</button>
                </div>
                
                <div class="space-y-3">
                    @foreach($items as $index => $item)
                        <div class="p-3 sm:p-4 bg-gray-50 rounded-lg">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Service</label>
                                    <select wire:model.live="items.{{ $index }}.service_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
                                        <option value="">-- Pilih Service --</option>
                                        @foreach($availableServices as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-3 items-end">
                                    <div class="w-20">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                                        <input type="number" wire:model.live="items.{{ $index }}.quantity" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-center" min="1">
                                    </div>
                                    <div class="w-28 text-right">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Subtotal</label>
                                        <p class="py-2 font-bold text-sm">Rp {{ number_format(((int)$items[$index]['price'] * (int)$items[$index]['quantity']), 0, ',', '.') }}</p>
                                    </div>
                                    <button wire:click="removeItem({{ $index }})" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @error('items') <p class="text-red-500 text-sm">Minimal 1 item diperlukan</p> @enderror
                </div>
            </div>
            
            <!-- Notes -->
            <div class="card">
                <label class="block text-sm font-medium text-gray-700 mb-1">📝 Notes (Optional)</label>
                <textarea wire:model="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="Catatan tambahan..."></textarea>
            </div>
        </div>

        <!-- Summary Sidebar - Sticky on desktop, fixed bottom on mobile -->
        <div class="lg:col-span-1">
            <div class="card lg:sticky lg:top-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">📋 Order Summary</h3>
                
                <!-- Promo Code -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo</label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="promo_code" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm uppercase font-mono" placeholder="DISKON20">
                        <button wire:click="applyPromo" type="button" class="px-4 py-2 bg-gray-100 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Apply</button>
                    </div>
                    @if(session('promo_error'))
                        <p class="text-red-500 text-xs mt-1">{{ session('promo_error') }}</p>
                    @endif
                    @if(session('promo_success'))
                        <p class="text-emerald-500 text-xs mt-1">{{ session('promo_success') }}</p>
                    @endif
                </div>

                <!-- Price Breakdown -->
                <div class="space-y-2 text-sm border-t pt-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order_type === 'pickup')
                        <div class="flex justify-between text-gray-600">
                            <span>Pickup Fee</span>
                            <span>Rp {{ number_format($pickup_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($order_type === 'delivery')
                        <div class="flex justify-between text-gray-600">
                            <span>Delivery Fee</span>
                            <span>Rp {{ number_format($delivery_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($discount_amount > 0)
                        <div class="flex justify-between text-emerald-600">
                            <span>Discount</span>
                            <span>- Rp {{ number_format($discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="pt-4 mt-4 border-t">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600">Total</span>
                        <span class="text-2xl sm:text-3xl font-bold text-primary-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="space-y-3">
                        <button wire:click="save" type="button" class="w-full btn-primary py-3">
                            <span wire:loading.remove>✅ Process Order</span>
                            <span wire:loading>Processing...</span>
                        </button>
                        <a href="{{ route('orders.index') }}" class="block text-center btn-secondary py-3">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
