<div class="min-h-screen bg-artisan-bg font-manrope selection:bg-artisan-secondary selection:text-white">
    <header class="sticky top-0 z-50 border-b border-artisan-outline/20 bg-artisan-bg/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="/" class="flex min-w-0 items-center gap-3 group">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-artisan-primary text-white shadow-artisan transition-all group-hover:bg-artisan-secondary sm:h-12 sm:w-12">
                    <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-black tracking-tighter text-artisan-primary sm:text-2xl">ShoeClean<span class="italic text-artisan-secondary">.</span></p>
                    <p class="text-[9px] font-black uppercase tracking-[0.24em] text-artisan-primary/30">Track Order</p>
                </div>
            </a>

            <a href="/" class="rounded-full border border-artisan-outline/20 px-4 py-2 text-[9px] font-black uppercase tracking-[0.22em] text-artisan-primary/50 transition-colors hover:border-artisan-secondary/30 hover:text-artisan-secondary sm:px-5">
                Kembali
            </a>
        </div>
    </header>

    <main class="px-4 pb-16 pt-6 sm:px-6 sm:pt-8 lg:px-8 lg:pb-24">
        <div class="mx-auto max-w-5xl space-y-6 sm:space-y-8">
            <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-artisan-primary via-artisan-primary to-[#143f41] px-5 py-6 text-white shadow-artisan-lg sm:rounded-[3rem] sm:px-8 sm:py-8 lg:px-10">
                <div class="absolute -right-12 -top-16 h-40 w-40 rounded-full bg-artisan-secondary/30 blur-3xl"></div>
                <div class="absolute -bottom-16 left-0 h-32 w-32 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_35%)]"></div>

                <div class="relative z-10 space-y-5">
                    <div class="max-w-3xl rounded-[1.75rem] border border-white/10 bg-white/8 p-4 backdrop-blur-md sm:p-6">
                        <p class="inline-flex rounded-full bg-artisan-secondary px-3 py-1 text-[9px] font-black uppercase tracking-[0.28em] text-artisan-primary shadow-sm">Order Tracking</p>
                        <h1 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl lg:text-5xl">Lacak Pesanan Anda</h1>
                        <p class="mt-3 max-w-2xl text-[11px] font-semibold leading-relaxed text-white/80 sm:text-sm">
                            Masukkan nomor invoice untuk melihat progres pengerjaan, status pembayaran, dan informasi outlet secara real-time.
                        </p>
                    </div>

                    <form wire:submit="search" class="rounded-[1.75rem] border border-white/12 bg-white/12 p-3 backdrop-blur-xl sm:p-4">
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input
                                type="text"
                                wire:model="invoice"
                                placeholder="Contoh: INV/20260326/5/0004"
                                class="w-full flex-1 rounded-[1.4rem] border border-white/10 bg-white px-5 py-4 text-[13px] font-black text-artisan-primary outline-none transition-all placeholder:text-artisan-primary/20 focus:ring-2 focus:ring-artisan-secondary/30"
                            >
                            <button type="submit" class="inline-flex items-center justify-center gap-3 rounded-[1.4rem] bg-artisan-secondary px-6 py-4 text-[10px] font-black uppercase tracking-[0.24em] text-artisan-primary transition-all hover:bg-white active:scale-[0.98] sm:px-8">
                                Cari Invoice
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </button>
                        </div>
                    </form>

                    @if($error)
                        <div class="rounded-[1.5rem] border border-red-200 bg-red-50 px-4 py-4 text-red-700">
                            <p class="text-[10px] font-black uppercase tracking-[0.22em]">Invoice Tidak Ditemukan</p>
                            <p class="mt-2 text-sm font-semibold leading-relaxed text-red-700/80">{{ $error }}</p>
                        </div>
                    @endif
                </div>
            </section>

            @if($order)
                @php
                    $statuses = ['pending', 'processing', 'ready', 'picked_up'];
                    $labels = [
                        'pending' => 'Pesanan Masuk',
                        'processing' => 'Sedang Diproses',
                        'ready' => 'Siap Diambil',
                        'picked_up' => 'Sudah Selesai',
                    ];
                    $currentIndex = array_search($order->status, $statuses, true);
                    if ($order->status === 'completed') {
                        $currentIndex = 3;
                    }
                    if ($order->status === 'cancelled') {
                        $currentIndex = -1;
                    }
                    $statusLabel = match($order->status) {
                        'pending' => 'Menunggu Diproses',
                        'processing' => 'Sedang Diproses',
                        'ready' => 'Siap Diambil',
                        'picked_up' => 'Sudah Diambil',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($order->status),
                    };
                    $statusClasses = match($order->status) {
                        'completed', 'picked_up' => 'bg-emerald-500 text-white',
                        'cancelled' => 'bg-red-500 text-white',
                        'ready' => 'bg-blue-500 text-white',
                        default => 'bg-artisan-primary text-white',
                    };
                    $waNumber = preg_replace('/[^0-9]/', '', $order->outlet->phone ?? '');
                @endphp

                <section class="overflow-hidden rounded-[2rem] border border-artisan-outline/20 bg-white shadow-artisan-lg sm:rounded-[3rem]">
                    <div class="border-b border-artisan-outline/20 bg-artisan-surface-low/60 px-5 py-5 sm:px-8 sm:py-7 lg:px-10">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.28em] text-artisan-primary/30">Nomor Invoice</p>
                                <h2 class="mt-2 break-all text-2xl font-black italic text-artisan-primary sm:text-3xl">{{ $order->invoice_number }}</h2>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex rounded-full px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] shadow-sm {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                                <span class="inline-flex rounded-full border border-artisan-outline/20 bg-white px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-artisan-primary/60">
                                    {{ $order->paymentStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 px-5 py-5 sm:px-8 sm:py-8 lg:px-10">
                        <div class="rounded-[1.75rem] border border-artisan-outline/15 bg-artisan-surface-low/40 p-4 sm:p-5">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-[0.28em] text-artisan-secondary">Progress Pesanan</p>
                                    <p class="mt-2 text-sm font-bold text-artisan-primary/60">Status pengerjaan terbaru dari outlet.</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @foreach($statuses as $index => $status)
                                    @php
                                        $isActive = $currentIndex >= $index;
                                        $isCurrent = $currentIndex === $index;
                                    @endphp
                                    <div class="flex items-start gap-3 rounded-[1.5rem] px-3 py-3 {{ $isActive ? 'bg-white shadow-sm' : 'bg-transparent' }}">
                                        <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-sm font-black shadow-sm {{ $isActive ? 'bg-artisan-secondary text-white' : 'border border-artisan-outline/20 bg-white text-artisan-primary/20' }}">
                                            @if($currentIndex > $index)
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @elseif($isCurrent)
                                                <div class="h-2.5 w-2.5 rounded-full bg-white animate-pulse"></div>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </div>

                                        <div class="min-w-0 flex-1 pt-1">
                                            <p class="text-[11px] font-black uppercase tracking-[0.18em] {{ $isActive ? 'text-artisan-primary' : 'text-artisan-primary/35' }}">
                                                {{ $labels[$status] }}
                                            </p>
                                            <p class="mt-1 text-[11px] font-semibold leading-relaxed {{ $isActive ? 'text-artisan-primary/55' : 'text-artisan-primary/30' }}">
                                                @if($status === 'pending')
                                                    Pesanan sudah masuk dan menunggu mulai dikerjakan.
                                                @elseif($status === 'processing')
                                                    Sepatu sedang dalam proses pembersihan atau restorasi.
                                                @elseif($status === 'ready')
                                                    Pesanan selesai dikerjakan dan siap diambil atau dikirim.
                                                @else
                                                    Pesanan telah selesai diterima oleh customer.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($order->status === 'cancelled')
                                <div class="mt-4 rounded-[1.5rem] border border-red-200 bg-red-50 px-4 py-4 text-red-700">
                                    <p class="text-[10px] font-black uppercase tracking-[0.22em]">Pesanan Dibatalkan</p>
                                    <p class="mt-2 text-sm font-semibold text-red-700/80">Silakan hubungi outlet jika Anda membutuhkan bantuan lebih lanjut terkait invoice ini.</p>
                                </div>
                            @endif
                        </div>

                        <div class="grid gap-5 lg:grid-cols-[1.2fr_0.8fr]">
                            <div class="rounded-[1.75rem] border border-artisan-outline/15 p-4 sm:p-5">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-[0.28em] text-artisan-secondary">Detail Layanan</p>
                                        <p class="mt-2 text-sm font-bold text-artisan-primary/60">Ringkasan item yang ada di pesanan ini.</p>
                                    </div>
                                    <div class="rounded-full bg-artisan-surface-low px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/50">
                                        {{ $order->items->count() }} item
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    @foreach($order->items as $item)
                                        <div class="rounded-[1.5rem] bg-artisan-surface-low/50 p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-black text-artisan-primary">{{ $item->service->name }}</p>
                                                    <p class="mt-1 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-primary/35">Qty {{ $item->quantity }}</p>
                                                </div>
                                                <p class="shrink-0 text-xs font-black italic text-artisan-primary">Rp {{ number_format($item->total_price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="rounded-[1.75rem] border border-artisan-outline/15 p-4 sm:p-5">
                                    <p class="text-[9px] font-black uppercase tracking-[0.28em] text-artisan-secondary">Informasi Outlet</p>
                                    <div class="mt-4 flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-artisan-surface-low text-artisan-secondary shadow-inner">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-lg font-black italic text-artisan-primary">{{ $order->outlet->name }}</p>
                                            <p class="mt-2 text-[11px] font-semibold leading-relaxed text-artisan-primary/55">{{ $order->outlet->address ?? 'Alamat outlet belum tersedia.' }}</p>
                                            @if($order->outlet->phone)
                                                <p class="mt-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-primary/35">{{ $order->outlet->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="overflow-hidden rounded-[1.75rem] bg-artisan-primary text-white shadow-artisan">
                                    <div class="px-5 py-5">
                                        <p class="text-[9px] font-black uppercase tracking-[0.28em] text-white/45">Total Tagihan</p>
                                        <p class="mt-3 text-3xl font-black italic sm:text-4xl">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="border-t border-white/10 px-5 py-4">
                                        <p class="text-[9px] font-black uppercase tracking-[0.24em] text-white/45">Status Pembayaran</p>
                                        <p class="mt-2 text-sm font-bold text-white/80">{{ $order->paymentStatusLabel() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($order->payment_status === 'waiting_confirmation')
                            <div class="rounded-[1.75rem] border border-amber-200 bg-amber-50 px-5 py-5">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-700">Menunggu Verifikasi Pembayaran</p>
                                <p class="mt-2 text-sm font-semibold leading-relaxed text-amber-800/80">Bukti pembayaran Anda sudah masuk dan sedang diperiksa oleh outlet. Status akan berubah otomatis setelah diverifikasi.</p>
                            </div>
                        @elseif($order->payment_status === 'unpaid' && $order->payment_method === 'qris' && $order->outlet?->qris_image_path)
                            <div class="rounded-[1.75rem] border border-slate-100 bg-white p-5 shadow-sm">
                                <div class="space-y-4 text-center">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-artisan-secondary">QRIS Outlet</p>
                                        <p class="mt-2 text-sm font-semibold leading-relaxed text-artisan-primary/55">Scan QRIS berikut untuk pembayaran ke outlet {{ $order->outlet->name }}.</p>
                                    </div>
                                    <div class="rounded-[1.5rem] bg-artisan-surface-low/60 p-4">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($order->outlet->qris_image_path) }}" alt="QRIS {{ $order->outlet->name }}" class="mx-auto max-h-72 rounded-2xl bg-white object-contain">
                                    </div>
                                    <p class="text-[11px] font-semibold leading-relaxed text-artisan-primary/50">Setelah transfer, hubungi outlet atau kirim bukti pembayaran agar bisa diverifikasi lebih cepat.</p>
                                </div>
                            </div>
                        @endif

                        <div class="rounded-[1.75rem] border border-artisan-outline/15 bg-artisan-surface-low/40 p-4 sm:p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-[0.28em] text-artisan-secondary">Butuh Bantuan?</p>
                                    <p class="mt-2 text-sm font-semibold leading-relaxed text-artisan-primary/60">Tim outlet siap membantu jika Anda ingin konfirmasi pembayaran atau menanyakan progres order.</p>
                                </div>

                                @if($waNumber)
                                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="inline-flex items-center justify-center gap-3 rounded-[1.4rem] bg-artisan-primary px-5 py-4 text-[10px] font-black uppercase tracking-[0.22em] text-white transition-all hover:bg-artisan-secondary active:scale-[0.98]">
                                        Hubungi Outlet
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </main>

    <footer class="px-4 pb-10 pt-2 text-center sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <p class="text-[9px] font-black uppercase tracking-[0.35em] text-artisan-primary/20">
                &copy; {{ date('Y') }} ShoeClean Artisan Collection. All Rights Reserved.
            </p>
        </div>
    </footer>
</div>
