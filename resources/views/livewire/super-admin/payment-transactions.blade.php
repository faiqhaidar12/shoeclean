<div class="py-8" x-data="{ openDetail: false, detail: null }">
    <div class="mb-12 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="font-manrope font-extrabold text-4xl lg:text-5xl tracking-tight" style="color: var(--sa-primary);">Log Transaksi Duitku</h1>
            <p class="font-medium mt-2 opacity-50">Pantau transaksi langganan dan top-up, lengkap dengan reference, metode bayar, dan status sinkronisasi.</p>
        </div>
        <button wire:click="resetFilters" class="inline-flex items-center justify-center rounded-[1.4rem] px-5 py-4 text-sm font-bold text-slate-700 bg-white shadow-sm transition hover:shadow-lg">
            Reset Filter
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">
        <div class="rounded-[2rem] p-8 shadow-sm bg-white"><p class="text-xs font-black uppercase tracking-[0.2em] opacity-40 mb-2">Total</p><p class="text-4xl font-manrope font-extrabold" style="color: var(--sa-primary);">{{ number_format($summary['total']) }}</p></div>
        <div class="rounded-[2rem] p-8 shadow-sm text-white" style="background: linear-gradient(135deg, #059669, #10b981);"><p class="text-xs font-black uppercase tracking-[0.2em] text-white/50 mb-2">Sukses</p><p class="text-4xl font-manrope font-extrabold">{{ number_format($summary['success']) }}</p></div>
        <div class="rounded-[2rem] p-8 shadow-sm text-white" style="background: linear-gradient(135deg, #f59e0b, #f97316);"><p class="text-xs font-black uppercase tracking-[0.2em] text-white/50 mb-2">Pending</p><p class="text-4xl font-manrope font-extrabold">{{ number_format($summary['pending']) }}</p></div>
        <div class="rounded-[2rem] p-8 shadow-sm text-white" style="background: linear-gradient(135deg, #dc2626, #ef4444);"><p class="text-xs font-black uppercase tracking-[0.2em] text-white/50 mb-2">Belum Selesai</p><p class="text-4xl font-manrope font-extrabold">{{ number_format($summary['failed']) }}</p></div>
    </div>

    <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
            <div class="xl:col-span-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari owner, email, merchant order id, atau reference" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
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
                <select wire:model.live="kindFilter" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    <option value="">Semua Jenis</option>
                    <option value="subscription">Langganan</option>
                    <option value="topup">Top Up</option>
                </select>
            </div>
            <div>
                <select wire:model.live="statusFilter" class="w-full px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    <option value="">Semua Status</option>
                    <option value="00">Sukses</option>
                    <option value="01">Pending</option>
                    <option value="02">Dibatalkan</option>
                    <option value="03">Gagal</option>
                </select>
            </div>
        </div>

        <div class="hidden xl:block overflow-x-auto">
            <table class="w-full min-w-[1200px]">
                <thead>
                    <tr class="border-b-2" style="border-color: var(--sa-surface-low);">
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Owner</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Jenis</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Merchant Order ID</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Reference</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Metode</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Status</th>
                        <th class="text-right text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Nominal</th>
                        <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Lunas</th>
                        <th class="text-right text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $detailPayload = [
                                'owner' => $transaction->user->name ?? '-',
                                'email' => $transaction->customer_email ?? ($transaction->user->email ?? '-'),
                                'kind' => $transaction->kind === 'subscription' ? 'Langganan' : 'Top Up',
                                'plan' => $transaction->plan_key ?? '-',
                                'merchant_order_id' => $transaction->merchant_order_id,
                                'reference' => $transaction->reference ?? '-',
                                'payment_method' => $transaction->payment_method ?? '-',
                                'status_code' => $transaction->status_code ?? '-',
                                'status_message' => $transaction->status_message ?? '-',
                                'amount' => 'Rp ' . number_format($transaction->amount, 0, ',', '.'),
                                'fee' => $transaction->fee !== null ? 'Rp ' . number_format((float) $transaction->fee, 0, ',', '.') : '-',
                                'paid_at' => $transaction->paid_at?->format('d/m/Y H:i') ?? '-',
                                'last_synced_at' => $transaction->last_synced_at?->format('d/m/Y H:i') ?? '-',
                                'product_detail' => $transaction->product_detail ?? '-',
                                'billable' => class_basename($transaction->billable_type ?? '') . ($transaction->billable_id ? ' #' . $transaction->billable_id : ''),
                                'checkout_payload' => $transaction->checkout_payload ? json_encode($transaction->checkout_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                                'callback_payload' => $transaction->callback_payload ? json_encode($transaction->callback_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                                'status_payload' => $transaction->status_payload ? json_encode($transaction->status_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                            ];
                            $detailPayloadEncoded = base64_encode(json_encode($detailPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                        @endphp
                        <tr class="border-b transition-colors duration-200 hover:bg-slate-50" style="border-color: var(--sa-surface-low);">
                            <td class="py-4 px-4">
                                <p class="font-manrope font-bold text-sm" style="color: var(--sa-primary);">{{ $transaction->user->name ?? '-' }}</p>
                                <p class="text-xs opacity-50 mt-1">{{ $transaction->customer_email ?? ($transaction->user->email ?? '-') }}</p>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $transaction->kind === 'subscription' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $transaction->kind === 'subscription' ? 'Langganan' : 'Top Up' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm font-semibold">{{ $transaction->merchant_order_id }}</td>
                            <td class="py-4 px-4 text-sm">{{ $transaction->reference ?? '-' }}</td>
                            <td class="py-4 px-4 text-sm uppercase">{{ $transaction->payment_method ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $transaction->status_code === '00' ? 'bg-emerald-100 text-emerald-700' : ($transaction->status_code === '01' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $transaction->status_message ?? ($transaction->status_code ?? '-') }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right font-manrope font-extrabold text-sm">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 text-sm">{{ $transaction->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="py-4 px-4 text-right">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:shadow-md"
                                    data-detail="{{ $detailPayloadEncoded }}"
                                    x-on:click="detail = JSON.parse(atob($el.dataset.detail)); openDetail = true"
                                >
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-10 text-center text-sm opacity-50">Belum ada transaksi yang cocok dengan filter saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="xl:hidden space-y-3">
            @forelse($transactions as $transaction)
                @php
                    $detailPayload = [
                        'owner' => $transaction->user->name ?? '-',
                        'email' => $transaction->customer_email ?? ($transaction->user->email ?? '-'),
                        'kind' => $transaction->kind === 'subscription' ? 'Langganan' : 'Top Up',
                        'plan' => $transaction->plan_key ?? '-',
                        'merchant_order_id' => $transaction->merchant_order_id,
                        'reference' => $transaction->reference ?? '-',
                        'payment_method' => $transaction->payment_method ?? '-',
                        'status_code' => $transaction->status_code ?? '-',
                        'status_message' => $transaction->status_message ?? '-',
                        'amount' => 'Rp ' . number_format($transaction->amount, 0, ',', '.'),
                        'fee' => $transaction->fee !== null ? 'Rp ' . number_format((float) $transaction->fee, 0, ',', '.') : '-',
                        'paid_at' => $transaction->paid_at?->format('d/m/Y H:i') ?? '-',
                        'last_synced_at' => $transaction->last_synced_at?->format('d/m/Y H:i') ?? '-',
                        'product_detail' => $transaction->product_detail ?? '-',
                        'billable' => class_basename($transaction->billable_type ?? '') . ($transaction->billable_id ? ' #' . $transaction->billable_id : ''),
                        'checkout_payload' => $transaction->checkout_payload ? json_encode($transaction->checkout_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                        'callback_payload' => $transaction->callback_payload ? json_encode($transaction->callback_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                        'status_payload' => $transaction->status_payload ? json_encode($transaction->status_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                    ];
                    $detailPayloadEncoded = base64_encode(json_encode($detailPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                @endphp
                <div class="rounded-[1.6rem] p-4 shadow-sm" style="background-color: var(--sa-surface-low);">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-manrope font-bold text-sm truncate" style="color: var(--sa-primary);">{{ $transaction->user->name ?? '-' }}</p>
                            <p class="text-xs opacity-50 mt-1">{{ $transaction->customer_email ?? ($transaction->user->email ?? '-') }}</p>
                        </div>
                        <p class="font-manrope font-extrabold text-sm whitespace-nowrap">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-3 space-y-1 text-sm">
                        <p><span class="opacity-40">Jenis:</span> {{ $transaction->kind === 'subscription' ? 'Langganan' : 'Top Up' }}</p>
                        <p><span class="opacity-40">Order ID:</span> {{ $transaction->merchant_order_id }}</p>
                        <p><span class="opacity-40">Reference:</span> {{ $transaction->reference ?? '-' }}</p>
                        <p><span class="opacity-40">Metode:</span> {{ $transaction->payment_method ?? '-' }}</p>
                        <p><span class="opacity-40">Lunas:</span> {{ $transaction->paid_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $transaction->status_code === '00' ? 'bg-emerald-100 text-emerald-700' : ($transaction->status_code === '01' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                            {{ $transaction->status_message ?? ($transaction->status_code ?? '-') }}
                        </span>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:shadow-md"
                            data-detail="{{ $detailPayloadEncoded }}"
                            x-on:click="detail = JSON.parse(atob($el.dataset.detail)); openDetail = true"
                        >
                            Detail
                        </button>
                    </div>
                </div>
            @empty
                <div class="rounded-[1.6rem] p-5 text-sm font-semibold opacity-50" style="background-color: var(--sa-surface-low);">Belum ada transaksi yang cocok dengan filter saat ini.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $transactions->links() }}</div>
    </div>

    <div
        x-show="openDetail"
        x-cloak
        class="fixed inset-0 z-[80] overflow-y-auto"
        style="background-color: rgba(15, 23, 42, 0.55);"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click.self="openDetail = false"
    >
        <div class="flex min-h-full items-start justify-center p-3 sm:p-5 lg:p-8">
            <div
                class="my-4 w-full max-w-6xl overflow-hidden rounded-[1.75rem] bg-white shadow-2xl sm:my-8 sm:rounded-[2rem]"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                <div class="sticky top-0 z-10 border-b bg-white/95 px-4 py-4 backdrop-blur sm:px-6 sm:py-5" style="border-color: var(--sa-surface-low);">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Log Pembayaran</p>
                            <h2 class="mt-2 break-all font-manrope text-xl font-extrabold sm:text-2xl" style="color: var(--sa-primary);" x-text="detail?.merchant_order_id ?? 'Detail Transaksi'"></h2>
                            <p class="mt-2 text-sm opacity-60" x-text="detail?.reference ? 'Reference: ' + detail.reference : 'Reference belum tersedia'"></p>
                        </div>
                        <button type="button" class="shrink-0 rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700" x-on:click="openDetail = false">Tutup</button>
                    </div>
                </div>

                <div class="max-h-[calc(100vh-64px)] overflow-y-auto px-4 py-4 sm:px-6 sm:py-6">
                    <div class="grid gap-5 2xl:grid-cols-[420px_minmax(0,1fr)]">
                        <section class="space-y-4">
                            <div class="rounded-[1.5rem] border p-5 shadow-sm" style="background-color: var(--sa-surface-low); border-color: rgba(15, 23, 42, 0.08);">
                                <h3 class="mb-4 font-manrope font-bold" style="color: var(--sa-primary);">Ringkasan</h3>
                                <dl class="space-y-3 text-sm">
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Owner</dt><dd class="font-semibold break-words" x-text="detail?.owner"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Email</dt><dd class="font-semibold break-all" x-text="detail?.email"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Jenis</dt><dd class="font-semibold break-words" x-text="detail?.kind"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Paket</dt><dd class="font-semibold break-words uppercase" x-text="detail?.plan"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Reference</dt><dd class="font-semibold break-all" x-text="detail?.reference"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Metode</dt><dd class="font-semibold break-words uppercase" x-text="detail?.payment_method"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Status</dt><dd class="font-semibold break-words" x-text="detail?.status_message + ' (' + detail?.status_code + ')'"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Nominal</dt><dd class="font-semibold break-words" x-text="detail?.amount"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Biaya</dt><dd class="font-semibold break-words" x-text="detail?.fee"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Produk</dt><dd class="font-semibold break-words" x-text="detail?.product_detail"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Terkait data</dt><dd class="font-semibold break-words" x-text="detail?.billable"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Lunas pada</dt><dd class="font-semibold break-words" x-text="detail?.paid_at"></dd></div>
                                    <div class="grid gap-1 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"><dt class="opacity-50">Sinkron terakhir</dt><dd class="font-semibold break-words" x-text="detail?.last_synced_at"></dd></div>
                                </dl>
                            </div>
                        </section>

                        <section class="space-y-4">
                            <template x-if="detail?.checkout_payload">
                                <div class="rounded-[1.5rem] border p-5 shadow-sm" style="background-color: var(--sa-surface-low); border-color: rgba(15, 23, 42, 0.08);">
                                    <h3 class="mb-3 font-manrope font-bold" style="color: var(--sa-primary);">Payload Checkout</h3>
                                    <pre class="max-h-[260px] overflow-auto rounded-2xl bg-slate-950 p-4 text-xs leading-6 text-slate-100" x-text="detail?.checkout_payload"></pre>
                                </div>
                            </template>
                            <template x-if="detail?.callback_payload">
                                <div class="rounded-[1.5rem] border p-5 shadow-sm" style="background-color: var(--sa-surface-low); border-color: rgba(15, 23, 42, 0.08);">
                                    <h3 class="mb-3 font-manrope font-bold" style="color: var(--sa-primary);">Payload Callback</h3>
                                    <pre class="max-h-[260px] overflow-auto rounded-2xl bg-slate-950 p-4 text-xs leading-6 text-slate-100" x-text="detail?.callback_payload"></pre>
                                </div>
                            </template>
                            <template x-if="detail?.status_payload">
                                <div class="rounded-[1.5rem] border p-5 shadow-sm" style="background-color: var(--sa-surface-low); border-color: rgba(15, 23, 42, 0.08);">
                                    <h3 class="mb-3 font-manrope font-bold" style="color: var(--sa-primary);">Payload Status Sync</h3>
                                    <pre class="max-h-[260px] overflow-auto rounded-2xl bg-slate-950 p-4 text-xs leading-6 text-slate-100" x-text="detail?.status_payload"></pre>
                                </div>
                            </template>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
