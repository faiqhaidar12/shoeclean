<div class="min-h-screen bg-slate-50/50 flex items-center justify-center py-20 px-4 selection:bg-artisan-secondary/30">
    <!-- Abstract Background Deco -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-artisan-secondary/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[30%] h-[30%] bg-artisan-primary/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-xl w-full animate-fade-in-up">
        <!-- Success Identity -->
        <div class="text-center mb-12">
            <div class="relative inline-block mb-8">
                <div class="w-24 h-24 bg-white rounded-[2rem] shadow-artisan-lg flex items-center justify-center text-emerald-500 relative z-10">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="absolute inset-0 bg-emerald-400/20 rounded-[2rem] blur-2xl animate-pulse -z-0"></div>
            </div>
            
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary">
                    <span class="w-4 h-[1px] bg-artisan-secondary"></span> Order Confirmed
                </div>
                <h1 class="headline-editorial text-4xl sm:text-5xl italic text-artisan-primary">Pesanan Terkirim</h1>
                <p class="text-[10px] text-artisan-primary/30 font-black uppercase tracking-[0.2em]">Terima kasih, koleksi Anda segera dalam penanganan ahli.</p>
            </div>
        </div>

        <!-- Master Receipt Card -->
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-artisan-lg overflow-hidden mb-12 transform hover:scale-[1.01] transition-all duration-500">
            <div class="bg-artisan-primary px-10 py-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-3xl"></div>
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-2">Invoice Identifier</p>
                <p class="headline-editorial text-2xl italic">{{ $order->invoice_number }}</p>
            </div>
            
            <div class="p-10 space-y-10">
                <!-- Outlet & Customer Info -->
                <div class="grid grid-cols-2 gap-8 pb-8 border-b border-slate-50">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20 mb-2">Artisan Branch</p>
                        <p class="text-[11px] font-black italic text-artisan-primary">{{ $outlet->name }}</p>
                        <p class="text-[9px] font-bold text-artisan-primary/40 mt-1">{{ $outlet->address }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20 mb-2">Customer Name</p>
                        <p class="text-[11px] font-black italic text-artisan-primary">{{ $order->customer->name }}</p>
                        <p class="text-[11px] font-black uppercase tracking-widest text-artisan-secondary mt-1">{{ $order->order_type }}</p>
                    </div>
                </div>

                <!-- Service List -->
                <div class="space-y-6">
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary">Service Summary</p>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center group">
                                <span class="text-[12px] font-bold text-artisan-primary/60 group-hover:text-artisan-primary transition-colors">{{ $item->service->name }} <span class="text-artisan-primary/20 mx-2">×</span> {{ $item->quantity }}</span>
                                <span class="text-[12px] font-black text-artisan-primary">Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Final Calculations -->
                <div class="pt-8 border-t border-slate-50 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/20">Metode Pembayaran</span>
                        <span class="text-[12px] font-black text-artisan-primary">{{ $order->paymentMethodLabel() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-artisan-primary/20">Status Pembayaran</span>
                        <span class="text-[12px] font-black {{ $order->payment_status === 'paid' ? 'text-emerald-600' : ($order->payment_status === 'waiting_confirmation' ? 'text-amber-500' : 'text-artisan-primary') }}">{{ strtoupper($order->paymentStatusLabel()) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Privilege Discount</span>
                            <span class="text-[12px] font-bold text-emerald-600">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-1">Total Restoration Cost</span>
                        <span class="text-3xl font-manrope font-black italic text-artisan-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Note -->
                <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100/50">
                    <p class="text-[9px] font-bold text-artisan-primary/40 leading-relaxed italic text-center">
                        <span class="text-artisan-secondary font-black tracking-widest uppercase block mb-1">Notice</span>
                        @if($order->payment_status === 'waiting_confirmation')
                            Bukti pembayaran Anda sudah terkirim. Outlet akan memverifikasi pembayaran sebelum status order dinyatakan lunas.
                        @elseif($outlet->qris_image_path)
                            Silakan scan QRIS outlet ini dan lakukan pembayaran sesuai total invoice di bawah.
                        @else
                            Pembayaran dilakukan secara tunai atau transfer di outlet saat proses restorasi selesai dan pengambilan koleksi.
                        @endif
                    </p>
                </div>

                @if($order->payment_notes)
                    <div class="bg-white rounded-[2rem] border border-slate-100 p-6">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-3">Catatan Pembayaran</p>
                        <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">{{ $order->payment_notes }}</p>
                    </div>
                @endif

                @if($outlet->qris_image_path && $order->payment_status !== 'waiting_confirmation')
                    <div class="bg-white rounded-[2rem] border border-slate-100 p-8 text-center space-y-5">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary">QRIS Outlet</p>
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($outlet->qris_image_path) }}" alt="QRIS {{ $outlet->name }}" class="mx-auto max-h-80 rounded-2xl border border-slate-100 bg-white object-contain">
                        @if($outlet->qris_notes)
                            <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">{{ $outlet->qris_notes }}</p>
                        @else
                            <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">Scan QRIS lalu bayar sesuai total restoration cost pada invoice ini.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Next Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <a href="{{ route('track') }}?invoice={{ urlencode($order->invoice_number) }}" 
                class="flex items-center justify-center gap-4 px-10 py-6 bg-artisan-primary text-white rounded-[2rem] font-manrope font-black italic text-sm hover:bg-artisan-secondary transition-all duration-500 shadow-xl shadow-artisan-primary/20 group">
                Lacak Progress
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
            </a>
            <a href="{{ route('public.order', $outlet->slug) }}" 
                class="flex items-center justify-center gap-4 px-10 py-6 bg-white border border-slate-100 rounded-[2rem] font-manrope font-black italic text-sm text-artisan-primary hover:bg-slate-50 transition-all duration-500 shadow-sm group">
                <svg class="w-5 h-5 group-hover:-rotate-45 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Restorasi Baru
            </a>
        </div>

        <!-- Helpful Support -->
        <div class="mt-16 text-center">
            <p class="text-[11px] font-bold text-artisan-primary/30 uppercase tracking-[0.2em] mb-4">Butuh Bantuan?</p>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $outlet->phone) }}" target="_blank" class="inline-flex items-center gap-3 px-6 py-3 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-emerald-100 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .004 5.408 0 12.044c0 2.123.555 4.197 1.61 6.011L0 24l6.135-1.61a11.771 11.771 0 005.911 1.583h.005c6.635 0 12.045-5.409 12.049-12.044.002-3.218-1.247-6.242-3.506-8.505"/></svg>
                WhatsApp Artisan Support
            </a>
        </div>
    </div>
</div>
