<div class="py-8">
    <div class="mb-12 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="font-manrope font-extrabold text-4xl lg:text-5xl tracking-tight" style="color: var(--sa-primary);">Order Platform</h1>
            <p class="font-medium mt-2 opacity-50">Audit order lintas outlet dari satu halaman dengan filter owner, outlet, dan status pembayaran.</p>
        </div>
        <button wire:click="resetFilters" class="inline-flex items-center justify-center rounded-[1.4rem] px-5 py-4 text-sm font-bold text-slate-700 bg-white shadow-sm transition hover:shadow-lg">
            Reset Filter
        </button>
    </div>

    <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
            <div class="xl:col-span-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari invoice atau customer" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
            </div>
            <div>
                <select wire:model.live="selectedOutlet" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    <option value="">Semua Outlet</option>
                    @foreach($outletOptions as $outletOption)
                        <option value="{{ $outletOption->id }}">{{ $outletOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="selectedOwner" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    <option value="">Semua Owner</option>
                    @foreach($ownerOptions as $ownerOption)
                        <option value="{{ $ownerOption->id }}">{{ $ownerOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="orderStatus" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    <option value="">Semua Status Order</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="ready">Ready</option>
                    <option value="completed">Completed</option>
                    <option value="picked_up">Picked Up</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-1">
                <select wire:model.live="paymentStatus" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    <option value="">Semua Status Bayar</option>
                    <option value="unpaid">Belum Lunas</option>
                    <option value="waiting_confirmation">Menunggu Verifikasi</option>
                    <option value="paid">Lunas</option>
                </select>
            </div>
        </div>

        <div class="hidden xl:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2" style="border-color: var(--sa-surface-low);">
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Invoice</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Tanggal</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Customer</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Outlet</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Owner</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Status</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Pembayaran</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Metode</th>
                        <th class="text-right text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="border-b transition-colors duration-200 hover:bg-slate-50" style="border-color: var(--sa-surface-low);">
                            <td class="py-4 px-4"><p class="font-manrope font-bold text-sm" style="color: var(--sa-primary);">{{ $order->invoice_number }}</p></td>
                            <td class="py-4 px-4 text-sm opacity-70">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-4 px-4 text-sm">{{ $order->customer->name ?? '-' }}</td>
                            <td class="py-4 px-4 text-sm">{{ $order->outlet->name ?? '-' }}</td>
                            <td class="py-4 px-4 text-sm">{{ $order->outlet->owner->name ?? '-' }}</td>
                            <td class="py-4 px-4"><span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ match($order->status) { 'completed', 'picked_up' => 'bg-emerald-100 text-emerald-600', 'cancelled' => 'bg-red-100 text-red-500', 'ready' => 'bg-blue-100 text-blue-500', default => 'bg-amber-100 text-amber-600' } }}">{{ str_replace('_', ' ', $order->status) }}</span></td>
                            <td class="py-4 px-4"><span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-600' : ($order->payment_status === 'waiting_confirmation' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ $order->paymentStatusLabel() }}</span></td>
                            <td class="py-4 px-4 text-sm">{{ $order->paymentMethodLabel() }}</td>
                            <td class="py-4 px-4 text-right font-manrope font-extrabold text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-10 text-center text-sm opacity-50">Belum ada order yang cocok dengan filter saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="xl:hidden space-y-3">
            @forelse($orders as $order)
                <div class="rounded-[1.6rem] p-4 shadow-sm" style="background-color: var(--sa-surface-low);">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-manrope font-bold text-sm truncate" style="color: var(--sa-primary);">{{ $order->invoice_number }}</p>
                            <p class="text-xs opacity-50 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <p class="font-manrope font-extrabold text-sm whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-3 space-y-1 text-sm">
                        <p><span class="opacity-40">Customer:</span> {{ $order->customer->name ?? '-' }}</p>
                        <p><span class="opacity-40">Outlet:</span> {{ $order->outlet->name ?? '-' }}</p>
                        <p><span class="opacity-40">Owner:</span> {{ $order->outlet->owner->name ?? '-' }}</p>
                        <p><span class="opacity-40">Metode:</span> {{ $order->paymentMethodLabel() }}</p>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ match($order->status) { 'completed', 'picked_up' => 'bg-emerald-100 text-emerald-600', 'cancelled' => 'bg-red-100 text-red-500', 'ready' => 'bg-blue-100 text-blue-500', default => 'bg-amber-100 text-amber-600' } }}">{{ str_replace('_', ' ', $order->status) }}</span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-600' : ($order->payment_status === 'waiting_confirmation' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ $order->paymentStatusLabel() }}</span>
                    </div>
                </div>
            @empty
                <div class="rounded-[1.6rem] p-5 text-sm font-semibold opacity-50" style="background-color: var(--sa-surface-low);">Belum ada order yang cocok dengan filter saat ini.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
</div>
