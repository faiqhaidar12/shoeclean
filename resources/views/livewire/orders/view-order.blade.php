<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12">
        <div>
            <a href="{{ route('orders.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary flex items-center gap-2 mb-6 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">{{ $order->invoice_number }}</h1>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest mt-4">
                Dibuat pada {{ $order->created_at->format('M d, Y') }} pukul {{ $order->created_at->format('H:i') }} • {{ $order->outlet->name }}
            </p>
        </div>
        <a href="{{ route('orders.print', $order->id) }}" target="_blank" class="btn-artisan-secondary flex items-center justify-center gap-3 !py-4 shadow-artisan">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Struk
        </a>
    </div>

    <!-- Status Milestone -->
    <div class="bg-artisan-surface-low rounded-[2.5rem] p-10 mb-12 relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-8">
                <div class="w-20 h-20 rounded-[1.5rem] flex items-center justify-center shadow-artisan-lg {{ match($order->status) {
                    'completed', 'picked_up' => 'bg-emerald-500 text-white',
                    'cancelled' => 'bg-red-500 text-white',
                    'ready' => 'bg-blue-500 text-white',
                    'processing' => 'bg-artisan-secondary text-white',
                    default => 'bg-white text-artisan-primary'
                } }}">
                    @switch($order->status)
                        @case('completed')
                        @case('picked_up')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @break
                        @case('cancelled')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                            @break
                        @case('ready')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @break
                        @case('processing')
                            <svg class="w-8 h-8 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @break
                        @default
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endswitch
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-primary/30 mb-2">Fase Saat Ini</p>
                    <h2 class="text-3xl font-manrope font-black text-artisan-primary">{{ strtoupper($order->status) }}</h2>
                    <div class="flex items-center gap-3 mt-4">
                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($order->payment_status === 'waiting_confirmation' ? 'bg-amber-50 text-amber-700' : 'bg-artisan-primary text-white') }}">
                            {{ $order->payment_status === 'paid' ? 'Pembayaran Lunas' : ($order->payment_status === 'waiting_confirmation' ? 'Menunggu Verifikasi Pembayaran' : 'Menunggu Pembayaran') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-primary/30 mb-2">Total Tagihan</p>
                <p class="text-4xl font-manrope font-black text-artisan-primary italic">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
        <!-- Main Content (Protocol & Client) -->
        <div class="lg:col-span-2 space-y-12">
            <!-- Restoration Protocol -->
            <div class="card-artisan p-10">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <h3 class="text-xl font-manrope font-black text-artisan-primary">Detail Layanan</h3>
                </div>
                
                <div class="overflow-x-auto artisan-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-artisan-surface-low/50">
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Layanan</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Jumlah</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Harga</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-artisan-outline/30">
                            @foreach($order->items as $item)
                            <tr class="group hover:bg-artisan-surface-low/50 transition-colors">
                                <td class="px-6 py-6">
                                    <p class="text-sm font-manrope font-bold text-artisan-primary">{{ $item->service->name }}</p>
                                    <p class="text-[10px] text-artisan-primary/30 font-black uppercase tracking-widest mt-1">Perawatan Kelas Profesional</p>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="text-sm font-bold text-artisan-primary">{{ $item->quantity }}</span>
                                    <span class="text-[10px] text-artisan-primary/40 ml-1 font-black uppercase">{{ $item->unit }}</span>
                                </td>
                                <td class="px-6 py-6 text-right text-sm font-medium text-artisan-primary/60">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-6 text-right font-manrope font-black text-artisan-primary">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-[3px] border-artisan-primary/10">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Subtotal</td>
                                <td class="px-6 py-4 text-right font-manrope font-bold text-artisan-primary">Rp {{ number_format($order->items->sum('total_price'), 0, ',', '.') }}</td>
                            </tr>
                            @if($order->pickup_fee > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Biaya Penjemputan</td>
                                <td class="px-6 py-2 text-right font-manrope font-bold text-artisan-primary">Rp {{ number_format($order->pickup_fee, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($order->delivery_fee > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Biaya Pengantaran</td>
                                <td class="px-6 py-2 text-right font-manrope font-bold text-artisan-primary">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2 text-right text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600">Diskon {{ $order->promo ? '(' . $order->promo->code . ')' : '' }}</td>
                                <td class="px-6 py-2 text-right font-manrope font-bold text-emerald-600">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr class="bg-artisan-primary">
                                <td colspan="3" class="px-6 py-6 text-right text-[10px] font-black uppercase tracking-[0.3em] text-white/60">Total Tagihan</td>
                                <td class="px-6 py-6 text-right text-xl font-manrope font-black text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Client Identification -->
            <div class="card-artisan p-10">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-artisan-surface-low rounded-2xl flex items-center justify-center text-artisan-secondary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                    </div>
                    <h3 class="text-xl font-manrope font-black text-artisan-primary">Data Pelanggan</h3>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <div class="w-24 h-24 bg-artisan-surface-low rounded-3xl flex items-center justify-center text-artisan-primary font-manrope font-black text-3xl shadow-sm italic">
                        {{ substr($order->customer->name, 0, 1) }}
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <p class="text-2xl font-manrope font-black text-artisan-primary mb-1">{{ $order->customer->name }}</p>
                        <p class="text-artisan-secondary font-black text-[10px] uppercase tracking-widest mb-4">{{ $order->customer->phone }}</p>
                        
                        @if($order->customer->address)
                            <div class="inline-flex items-center gap-2 p-3 bg-artisan-surface-low rounded-xl text-artisan-primary/60 text-xs font-medium italic">
                                <svg class="w-4 h-4 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $order->customer->address }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Subtle WhatsApp Communication -->
                    @php
                        $phone = preg_replace('/[^0-9]/', '', $order->customer->phone);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        $waMessage = urlencode("Halo {$order->customer->name}, update untuk pesanan {$order->invoice_number}...");
                    @endphp
                    <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank"
                        class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm group" title="Engage on WhatsApp">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                </div>
                
                @if($order->notes)
                    <div class="mt-8 pt-8 border-t border-artisan-outline/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/30 mb-3">Catatan Pesanan / Perhatian Khusus</p>
                        <p class="text-sm font-medium text-artisan-primary/80 italic">"{{ $order->notes }}"</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar (Milestones & Finance) -->
        <div class="lg:col-span-1 space-y-12">
            <!-- Strategic Milestones -->
            <div class="card-artisan p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-lg font-manrope font-black text-artisan-primary">Aksi Outlet</h3>
                </div>
                
                <div class="space-y-4">
                    @if($order->status === 'pending')
                        <button wire:click="updateStatus('processing')" class="w-full btn-artisan-primary !rounded-2xl !py-5 shadow-artisan">
                            Proses Pesanan
                        </button>
                        <button wire:click="updateStatus('cancelled')" class="w-full py-4 text-[10px] font-black uppercase tracking-widest text-red-600/40 hover:text-red-600 transition-colors">
                            Batalkan Pesanan
                        </button>
                    @endif

                    @if($order->status === 'processing')
                        <button wire:click="updateStatus('ready')" class="w-full btn-artisan-primary !rounded-2xl !py-5 !bg-blue-600 shadow-artisan">
                            Tandai Selesai Cuci
                        </button>
                    @endif

                    @if($order->status === 'ready')
                        <button wire:click="updateStatus('picked_up')" class="w-full btn-artisan-primary !rounded-2xl !py-5 !bg-emerald-600 shadow-artisan">
                            Tandai Diambil
                        </button>
                    @endif

                    @if(in_array($order->status, ['completed', 'picked_up', 'cancelled']))
                        <div class="p-6 bg-artisan-surface-low rounded-2xl text-center">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Pesanan Selesai / Ditutup</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Ledger -->
            <div class="card-artisan p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-manrope font-black text-artisan-primary">Pembayaran</h3>
                </div>
                
                <div class="p-6 rounded-2xl mb-8 {{ $order->payment_status === 'paid' ? 'bg-emerald-50' : 'bg-artisan-surface-low' }}">
                    <p class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/30 mb-2">Status Pembayaran</p>
                    <p class="text-xl font-manrope font-black {{ $order->payment_status === 'paid' ? 'text-emerald-700' : ($order->payment_status === 'waiting_confirmation' ? 'text-amber-600' : 'text-artisan-primary') }}">
                        {{ strtoupper($order->paymentStatusLabel()) }}
                    </p>
                    <p class="text-[10px] font-bold text-artisan-primary/40 mt-3 uppercase tracking-widest">Metode: {{ $order->paymentMethodLabel() }}</p>
                </div>

                <!-- Strategic Payments -->
                @if($order->payments->isNotEmpty())
                    <div class="space-y-4 mb-8">
                        @foreach($order->payments as $payment)
                            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-artisan-outline/30">
                                <span class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/40">{{ $payment->method === 'cash' ? 'Tunai / Konfirmasi Manual' : $payment->method }}</span>
                                <span class="text-sm font-manrope font-black {{ $payment->status === 'success' ? 'text-emerald-600' : 'text-artisan-primary/40' }}">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($order->payment_proof_path)
                    <div x-data="{ showPaymentProofModal: false }" class="rounded-[2rem] border border-artisan-outline/30 bg-white p-5 space-y-4 mb-8">
                        <p class="text-[10px] font-black uppercase tracking-widest text-artisan-secondary">Bukti Pembayaran</p>
                        <button type="button" x-on:click="showPaymentProofModal = true" class="block w-full rounded-2xl transition-transform hover:scale-[1.01]">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($order->payment_proof_path) }}" alt="Bukti pembayaran {{ $order->invoice_number }}" class="mx-auto max-h-80 rounded-2xl bg-artisan-surface-low object-contain shadow-sm">
                        </button>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-artisan-primary/30">Klik gambar untuk memperbesar</p>
                        @if($order->payment_proof_uploaded_at)
                            <p class="text-[10px] font-bold text-artisan-primary/40">Diupload pada {{ $order->payment_proof_uploaded_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($order->payment_notes)
                            <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">{{ $order->payment_notes }}</p>
                        @endif
                        @if($order->payment_verified_at && $order->paymentVerifier)
                            <p class="text-[10px] font-bold text-emerald-600">Diverifikasi oleh {{ $order->paymentVerifier->name }} pada {{ $order->payment_verified_at->format('d/m/Y H:i') }}</p>
                        @endif

                        <div
                            x-show="showPaymentProofModal"
                            x-transition.opacity
                            x-on:keydown.escape.window="showPaymentProofModal = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4"
                            style="display: none;"
                        >
                            <div class="absolute inset-0" x-on:click="showPaymentProofModal = false"></div>
                            <div class="relative z-10 w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/10 bg-white shadow-2xl">
                                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary">Bukti Pembayaran</p>
                                        <p class="mt-2 text-sm font-black text-artisan-primary">{{ $order->invoice_number }}</p>
                                    </div>
                                    <button type="button" x-on:click="showPaymentProofModal = false" class="rounded-2xl bg-slate-100 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-artisan-primary/60 transition-colors hover:bg-slate-200 hover:text-artisan-primary">Tutup</button>
                                </div>
                                <div class="bg-slate-50 p-4 sm:p-6">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($order->payment_proof_path) }}" alt="Bukti pembayaran besar {{ $order->invoice_number }}" class="mx-auto max-h-[80vh] w-auto rounded-2xl object-contain">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(in_array($order->payment_status, ['unpaid', 'waiting_confirmation']) && $order->status !== 'cancelled')
                    <div class="space-y-4">
                        @if($order->payment_status === 'unpaid')
                            <button wire:click="markPaid" class="w-full py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-artisan hover:bg-emerald-700 transition-all">
                                Tandai Sudah Dibayar
                            </button>
                        @endif

                        @if($order->payment_status === 'waiting_confirmation')
                            <button wire:click="verifyPayment" class="w-full py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-artisan hover:bg-emerald-700 transition-all">
                                Verifikasi Pembayaran
                            </button>
                            <button wire:click="resetPaymentToUnpaid" class="w-full py-4 bg-amber-100 text-amber-700 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-amber-200 transition-all">
                                Kembalikan ke Belum Lunas
                            </button>
                        @endif

                        @if($order->outlet?->qris_image_path)
                            <div class="rounded-[2rem] border border-artisan-outline/30 bg-white p-5 space-y-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-artisan-secondary">QRIS Outlet</p>
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($order->outlet->qris_image_path) }}" alt="QRIS {{ $order->outlet->name }}" class="mx-auto max-h-72 rounded-2xl bg-artisan-surface-low object-contain">
                                @if($order->outlet->qris_notes)
                                    <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">{{ $order->outlet->qris_notes }}</p>
                                @else
                                    <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">Gunakan QRIS outlet ini bila pelanggan ingin membayar non-tunai secara manual.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
