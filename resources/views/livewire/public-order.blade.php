<div class="min-h-screen pb-20 selection:bg-artisan-secondary/30">
    <!-- Header Section -->
    <div class="relative overflow-hidden bg-artisan-primary pb-14 pt-8 text-white sm:pb-20 sm:pt-12">
        <!-- Mesh Gradient Background (Subtle) -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[500px] h-[500px] bg-artisan-secondary/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-[400px] h-[400px] bg-white/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between sm:gap-8">
                <div class="flex items-start gap-4 sm:items-center sm:gap-6">
                    <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] border border-white/10 bg-white/5 text-2xl font-manrope font-black italic text-artisan-secondary shadow-2xl backdrop-blur-xl sm:h-16 sm:w-16 sm:rounded-[1.5rem] sm:text-3xl">
                        {{ substr($outlet->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-artisan-secondary/10 px-3 py-1 text-[8px] font-black uppercase tracking-[0.2em] text-artisan-secondary sm:text-[9px]">
                             Artisan Outlet
                        </div>
                        <h1 class="headline-editorial text-2xl italic leading-tight text-white sm:text-4xl">{{ $outlet->name }}</h1>
                    </div>
                </div>
                
                <div class="grid gap-2 sm:pr-2">
                    <p class="flex items-start gap-2 text-[9px] font-black uppercase tracking-[0.2em] text-white/40 sm:justify-end sm:text-[10px]">
                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $outlet->address }}
                    </p>
                    <p class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.2em] text-white/40 sm:justify-end sm:text-[10px]">
                        <svg class="h-3.5 w-3.5 shrink-0 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $outlet->phone }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Step Progress Bar (Premium) -->
    @if($step >= 1)
    <div class="relative z-20 mx-auto -mt-6 max-w-4xl px-4 sm:px-6 lg:px-8 sm:-mt-8">
        <div class="rounded-[2rem] border border-white bg-white/90 p-3 shadow-artisan-lg backdrop-blur-2xl sm:rounded-[2.5rem] sm:p-6">
            <div class="grid grid-cols-3 gap-2 sm:flex sm:items-center sm:justify-between sm:gap-4">
                @foreach([1 => 'Layanan', 2 => 'Pelanggan', 3 => 'Konfirmasi'] as $num => $label)
                    <div class="flex flex-col items-center gap-2 text-center sm:flex-row sm:gap-3 {{ $num < 3 ? 'sm:flex-1' : '' }}">
                        <div class="relative">
                            <div class="relative z-10 flex h-10 w-10 items-center justify-center rounded-2xl text-xs font-black transition-all duration-500
                                {{ $step >= $num ? 'bg-artisan-primary text-white shadow-lg shadow-artisan-primary/20 scale-110' : 'bg-slate-50 text-slate-300' }}">
                                @if($step > $num)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $num }}
                                @endif
                            </div>
                            @if($step == $num)
                                <div class="absolute inset-0 bg-artisan-secondary/40 rounded-2xl blur-lg animate-pulse -z-0"></div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] {{ $step >= $num ? 'text-artisan-primary' : 'text-slate-300' }}">Tahap {{ $num }}</span>
                            <span class="text-[10px] font-bold {{ $step >= $num ? 'text-artisan-primary' : 'text-artisan-primary/30' }}">{{ $label }}</span>
                        </div>

                        @if($num < 3)
                            <div class="mx-2 hidden h-1 flex-1 rounded-full sm:block">
                                <div class="h-full rounded-full transition-all duration-700 bg-gradient-to-r {{ $step > $num ? 'from-artisan-primary to-artisan-secondary w-full' : 'bg-slate-50 w-0' }}"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Step 0: Branch Selection (Premium) -->
        @if($step === 0)
            <div class="space-y-10 py-12 animate-fade-in">
                <div class="text-center max-w-xl mx-auto">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-artisan-secondary/5 rounded-full mb-6">
                        <svg class="w-10 h-10 text-artisan-secondary animate-bounce-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <h2 class="headline-editorial text-3xl sm:text-4xl italic text-artisan-primary mb-3">Tentukan Lokasi</h2>
                    <p class="text-[10px] text-artisan-primary/30 font-black uppercase tracking-[0.2em] leading-relaxed">Pilih cabang artisan terdekat untuk kualitas restorasi yang seragam di seluruh jaringan kami.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    @foreach($siblingOutlets as $branch)
                        <button wire:click="selectBranch({{ $branch->id }})" 
                            class="group relative bg-white rounded-[2.5rem] p-10 border-2 text-left transition-all duration-500 hover:-translate-y-2 flex flex-col items-center text-center
                                {{ $outlet->id === $branch->id ? 'border-artisan-secondary shadow-artisan-lg shadow-artisan-secondary/10' : 'border-slate-50 shadow-artisan' }}">
                            
                            <div class="w-16 h-16 bg-artisan-primary text-white rounded-[1.5rem] flex items-center justify-center text-2xl font-manrope font-black italic mb-6 transition-all duration-500 group-hover:bg-artisan-secondary group-hover:scale-110">
                                {{ substr($branch->name, 0, 1) }}
                            </div>
                            
                            <h3 class="headline-editorial text-2xl italic text-artisan-primary group-hover:text-artisan-secondary transition-colors mb-4">{{ $branch->name }}</h3>
                            <div class="space-y-3">
                                <p class="text-[10px] text-artisan-primary/40 font-bold flex items-center justify-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $branch->address }}
                                </p>
                                <p class="text-[10px] text-artisan-primary/30 font-bold uppercase tracking-widest">{{ $branch->phone }}</p>
                            </div>

                            @if($outlet->id === $branch->id)
                                <div class="absolute top-6 right-6">
                                    <span class="w-3 h-3 bg-artisan-secondary rounded-full block animate-ping"></span>
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Step 1: Select Services (Premium) -->
        @if($step === 1)
            <div class="space-y-8 py-6 animate-fade-in-up sm:space-y-12 sm:py-10">
                <div class="flex flex-col gap-4 sm:gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-3">
                            <span class="w-4 h-[1px] bg-artisan-secondary"></span> Curated Menu
                        </div>
                        <h2 class="headline-editorial text-3xl italic text-artisan-primary sm:text-4xl mb-2">Layanan Restorasi</h2>
                        <p class="text-[10px] leading-relaxed text-artisan-primary/30 font-black uppercase tracking-[0.2em]">Pilih layanan yang dibutuhkan, lalu atur jumlah pasang langsung dari keranjang di bawah.</p>
                    </div>
                </div>

                <!-- Showcase Service Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                    @foreach($availableServices as $service)
                        <div class="bg-white/80 backdrop-blur-sm rounded-[2rem] p-5 border border-slate-100 flex flex-col justify-between shadow-artisan group sm:p-6">
                            <div class="mb-6">
                                <div class="mb-4 inline-flex items-center rounded-full bg-artisan-secondary/5 px-3 py-1 text-[8px] font-black uppercase tracking-[0.24em] text-artisan-secondary">Per {{ $service->unit }}</div>
                                <h3 class="headline-editorial text-lg italic text-artisan-primary sm:text-xl mb-2">{{ $service->name }}</h3>
                                <p class="text-[10px] leading-relaxed text-artisan-primary/45 font-bold">Pilih layanan ini bila Anda ingin hasil pengerjaan premium untuk pasangan yang sedang Anda order.</p>
                            </div>
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-[8px] font-black uppercase tracking-[0.24em] text-artisan-primary/25">Harga Dasar</p>
                                    <p class="mt-2 text-xl font-manrope font-black text-artisan-primary italic sm:text-2xl">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex h-10 w-10 rounded-full bg-artisan-secondary/5 items-center justify-center text-artisan-secondary group-hover:bg-artisan-secondary group-hover:text-white transition-all duration-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Interface (Exclusive Look) -->
                <div class="bg-white rounded-[2.25rem] sm:rounded-[3rem] shadow-artisan-lg border border-slate-100 overflow-hidden">
                    <div class="px-5 py-5 bg-artisan-primary text-white flex items-center justify-between gap-4 sm:px-10 sm:py-8">
                        <div>
                            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-1">Selections</h3>
                            <p class="headline-editorial text-xl italic sm:text-2xl">Keranjang Pesanan</p>
                        </div>
                        <div class="flex items-center gap-3">
                             <div class="w-11 h-11 rounded-2xl bg-white/10 flex items-center justify-center text-lg font-manrope font-black italic sm:w-12 sm:h-12 sm:text-xl">
                                {{ collect($items)->whereNotNull('service_id')->count() }}
                             </div>
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-4 sm:p-8 sm:space-y-8 md:p-10">
                        @foreach($items as $index => $item)
                            <div class="flex flex-col gap-5 rounded-[2rem] border border-slate-100 bg-slate-50/55 p-4 sm:p-6 lg:flex-row lg:items-start lg:gap-6 {{ !$loop->first ? 'sm:pt-8' : '' }}">
                                <div class="flex-1 w-full group">
                                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-3 ml-2">Pilih Layanan</label>
                                    <div class="relative">
                                        <select wire:model.live="items.{{ $index }}.service_id" 
                                            class="w-full pl-5 pr-12 py-4 bg-white border border-slate-100 rounded-2xl text-[13px] font-bold text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none appearance-none">
                                            <option value="">Silakan tentukan...</option>
                                            @foreach($availableServices as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }})</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none text-artisan-primary/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full lg:w-40 flex flex-col items-start">
                                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-3">Jumlah</label>
                                    <div class="flex items-center w-full bg-white border border-slate-100 rounded-2xl p-1">
                                        <button wire:click="$set('items.{{ $index }}.quantity', Math.max(1, {{ $item['quantity'] }} - 1))" class="w-11 h-11 flex items-center justify-center text-artisan-primary/30 hover:text-artisan-primary transition-colors">-</button>
                                        <input wire:model.live="items.{{ $index }}.quantity" type="number" min="1" class="flex-1 bg-transparent border-none text-center text-sm font-black text-artisan-primary outline-none py-3 focus:ring-0">
                                        <button wire:click="$set('items.{{ $index }}.quantity', {{ $item['quantity'] }} + 1)" class="w-11 h-11 flex items-center justify-center text-artisan-primary/30 hover:text-artisan-primary transition-colors">+</button>
                                    </div>
                                </div>
                                <div class="w-full lg:w-48 lg:text-right flex flex-col lg:items-end">
                                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-3">Harga Akhir</label>
                                    <div class="min-h-[52px] flex items-center">
                                         <p class="text-xl font-manrope font-black italic text-artisan-primary sm:text-2xl">
                                            Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                @if(count($items) > 1)
                                    <button wire:click="removeItem({{ $index }})" class="lg:mt-8 p-4 bg-white text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all shadow-sm border border-slate-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach

                        <button wire:click="addItem" class="w-full py-5 sm:py-6 border-2 border-dashed border-slate-200 rounded-[2rem] text-[10px] sm:text-[11px] font-black uppercase tracking-[0.3em] text-artisan-primary/30 hover:text-artisan-secondary hover:border-artisan-secondary/20 hover:bg-artisan-secondary/5 transition-all flex items-center justify-center gap-4">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Tambahkan Sepatu Lainnya
                        </button>
                    </div>
                </div>

                <!-- Action Footer (Floating Style) -->
                <div class="sticky bottom-3 z-30 flex flex-col gap-4 rounded-[2rem] border border-white bg-white/95 p-4 shadow-artisan-lg backdrop-blur-2xl sm:static sm:flex-row sm:items-center sm:justify-between sm:gap-10 sm:rounded-[3rem] sm:bg-white/70 sm:p-8">
                    <div class="text-center sm:text-left">
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-2">Total Estimasi</p>
                        <p class="text-3xl font-manrope font-black italic text-artisan-primary leading-none sm:text-4xl">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 w-full sm:w-auto">
                        @if(count($siblingOutlets) > 1)
                            <button wire:click="backToBranchSelection" class="w-full sm:w-auto text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 hover:text-artisan-secondary transition-colors">
                                Ganti Cabang
                            </button>
                        @endif
                        <button wire:click="nextStep" class="w-full sm:w-auto px-8 sm:px-12 py-5 sm:py-6 bg-artisan-primary text-white rounded-[2rem] font-manrope font-black italic text-sm hover:bg-artisan-secondary transition-all duration-500 shadow-xl shadow-artisan-primary/20 flex items-center justify-center gap-4 active:scale-[0.98]">
                            Lengkapi Data
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 2: Customer Info (Premium) -->
        @if($step === 2)
            <div class="space-y-8 py-6 animate-fade-in-right sm:space-y-12 sm:py-10">
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-3">
                        <span class="w-4 h-[1px] bg-artisan-secondary"></span> Guest Profile
                    </div>
                    <h2 class="headline-editorial text-3xl italic text-artisan-primary sm:text-4xl mb-2">Data Pelanggan</h2>
                    <p class="text-[10px] leading-relaxed text-artisan-primary/30 font-black uppercase tracking-[0.2em]">Isi kontak aktif dan pilih cara layanan agar outlet bisa memproses order tanpa perlu chat ulang.</p>
                </div>

                <div class="bg-white rounded-[2.25rem] sm:rounded-[3rem] p-5 sm:p-8 lg:p-10 border border-slate-100 shadow-artisan-lg space-y-8 sm:space-y-10">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-10">
                        <div class="group">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-3 ml-2 group-focus-within:text-artisan-secondary transition-colors">Nama Lengkap *</label>
                            <input wire:model="customer_name" type="text" placeholder="Tulis nama Anda..." 
                                class="w-full px-5 py-4 sm:px-6 sm:py-5 bg-slate-50/60 border border-slate-100 rounded-2xl text-[13px] font-bold text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none placeholder:text-artisan-primary/10">
                            @error('customer_name') <p class="text-red-500 text-[10px] font-bold mt-2 ml-2 italic uppercase tracking-wider">{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-3 ml-2 group-focus-within:text-artisan-secondary transition-colors">WhatsApp / HP *</label>
                            <input wire:model="customer_phone" type="tel" placeholder="08xxxxxxxxxx" 
                                class="w-full px-5 py-4 sm:px-6 sm:py-5 bg-slate-50/60 border border-slate-100 rounded-2xl text-[13px] font-bold text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none placeholder:text-artisan-primary/10">
                            @error('customer_phone') <p class="text-red-500 text-[10px] font-bold mt-2 ml-2 italic uppercase tracking-wider">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Order Type (Modern Choice) -->
                    <div class="space-y-5">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 ml-2">Metode Layanan</label>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-6">
                            @foreach(['regular' => ['Reguler', 'Drop at Outlet'], 'pickup' => ['Pickup', 'Artisan Fetch'], 'delivery' => ['delivery', 'Home Courier']] as $type => [$label, $desc])
                                <label class="relative cursor-pointer group">
                                    <input wire:model.live="order_type" type="radio" value="{{ $type }}" class="peer sr-only">
                                    <div class="h-full p-5 sm:p-7 border-2 rounded-[2rem] text-center transition-all duration-500 
                                        peer-checked:border-artisan-secondary peer-checked:bg-artisan-secondary/5 peer-checked:shadow-artisan
                                        border-slate-50 bg-white group-hover:border-artisan-secondary/20">
                                        <p class="headline-editorial text-lg italic text-artisan-primary mb-1">{{ $label }}</p>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20">{{ $desc }}</p>
                                    </div>
                                    @if($order_type === $type)
                                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-artisan-secondary text-white rounded-full flex items-center justify-center shadow-lg transform transition-all animate-scale-in">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Conditional Details -->
                    @if($order_type === 'pickup' || $order_type === 'delivery')
                        <div class="space-y-6 animate-fade-in-up">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 ml-2">{{ $order_type === 'pickup' ? 'Alamat Penjemputan' : 'Alamat Pengantaran' }}</label>
                            <div class="relative group">
                                <textarea wire:model="{{ $order_type === 'pickup' ? 'pickup_address' : 'delivery_address' }}" rows="3" 
                                    placeholder="Tuliskan alamat lengkap..." 
                                    class="w-full px-5 py-5 sm:px-6 sm:py-6 bg-slate-50/60 border border-slate-100 rounded-[2rem] text-[13px] font-bold text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none placeholder:text-artisan-primary/10 resize-none"></textarea>
                                <div class="mt-3 text-[9px] font-black uppercase tracking-widest text-artisan-secondary">
                                    Extra Surcharge: Rp {{ number_format($order_type === 'pickup' ? $pickup_fee : $delivery_fee, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Promo & Notes Section -->
                    <div class="grid grid-cols-1 gap-6 pt-2 lg:grid-cols-2 lg:gap-10">
                        <div class="space-y-6">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 ml-2">Catatan Pesanan</label>
                            <textarea wire:model="notes" rows="3" placeholder="Contoh: Titip di satpam, sepatu kotor sekali..." 
                                class="w-full px-5 py-5 sm:px-6 sm:py-6 bg-slate-50/60 border border-slate-100 rounded-[2rem] text-[13px] font-bold text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none placeholder:text-artisan-primary/10 resize-none"></textarea>
                        </div>
                        
                        <div class="space-y-6">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 ml-2">Privilege Code</label>
                            <div class="bg-slate-50/60 rounded-[2rem] p-4">
                                @if($applied_promo)
                                    <div class="flex items-center justify-between p-6 bg-white rounded-2xl shadow-sm border border-emerald-50">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-black uppercase tracking-widest text-emerald-600 leading-none mb-1">{{ $applied_promo->code }}</p>
                                                <p class="text-[10px] font-bold text-artisan-primary/40">Hemat Rp {{ number_format($discount_amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <button wire:click="removePromo" class="text-[9px] font-black uppercase tracking-widest text-red-400 hover:text-red-600 transition-colors">Discard</button>
                                    </div>
                                @else
                                    <div class="flex flex-col gap-3 sm:flex-row">
                                        <input wire:model="promo_code" type="text" placeholder="Masukkan kode..." 
                                            class="flex-1 px-5 py-4 bg-white border border-slate-100 rounded-2xl text-[12px] font-bold text-artisan-primary uppercase focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none placeholder:normal-case placeholder:text-artisan-primary/10">
                                        <button wire:click="applyPromo" class="w-full sm:w-auto px-8 py-4 bg-artisan-primary text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-artisan-secondary transition-all active:scale-95 shadow-lg shadow-artisan-primary/10">Apply</button>
                                    </div>
                                    @if(session('promo_error')) <p class="text-red-500 text-[9px] font-bold mt-3 ml-2 italic uppercase tracking-wider">{{ session('promo_error') }}</p> @endif
                                    @if(session('promo_success')) <p class="text-emerald-600 text-[9px] font-bold mt-3 ml-2 italic uppercase tracking-wider">{{ session('promo_success') }}</p> @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="sticky bottom-3 z-30 flex flex-col gap-4 rounded-[2rem] border border-white bg-white/95 p-4 shadow-artisan-lg backdrop-blur-2xl sm:static sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
                    <button wire:click="prevStep" class="group flex items-center gap-4 text-[10px] font-black uppercase tracking-[0.3em] text-artisan-primary/30 hover:text-artisan-secondary transition-colors">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18"/></svg>
                        Kembali
                    </button>
                    <button wire:click="nextStep" class="w-full sm:w-auto px-8 sm:px-14 py-5 sm:py-6 bg-artisan-primary text-white rounded-[2.5rem] font-manrope font-black italic text-sm sm:text-base hover:bg-artisan-secondary transition-all duration-500 shadow-2xl shadow-artisan-primary/20 flex items-center justify-center gap-4 sm:gap-6 active:scale-[0.98]">
                        Review Pesanan
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Step 3: Review (Premium) -->
        @if($step === 3)
            <div class="space-y-8 py-6 animate-fade-in-up sm:space-y-10 sm:py-10">
                <div class="relative overflow-hidden rounded-[2.5rem] bg-artisan-primary px-5 py-7 text-white shadow-artisan-lg sm:rounded-[3rem] sm:px-10 sm:py-10">
                    <div class="absolute -top-16 right-0 h-52 w-52 rounded-full bg-artisan-secondary/20 blur-3xl"></div>
                    <div class="absolute -bottom-20 left-8 h-36 w-36 rounded-full bg-white/5 blur-3xl"></div>
                    <div class="relative z-10 max-w-3xl">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-4">
                            <span class="w-4 h-[1px] bg-artisan-secondary"></span> Final Review
                        </div>
                        <h2 class="headline-editorial text-3xl italic text-white sm:text-5xl">Konfirmasi Pesanan</h2>
                        <p class="mt-4 text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Periksa kembali detail layanan, total tagihan, dan metode pembayaran sebelum pesanan dikirim ke outlet.</p>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6 lg:gap-8 xl:gap-10">
                    <!-- Left: Details -->
                    <div class="col-span-12 lg:col-span-7 xl:col-span-8 space-y-6 sm:space-y-8 lg:space-y-10">
                        <!-- Summary Card -->
                        <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] p-5 sm:p-8 lg:p-10 border border-slate-100 shadow-artisan-lg">
                            <div class="mb-8 flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-2 ml-2">Detail Identitas</h3>
                                    <p class="text-[10px] font-bold text-artisan-primary/40 ml-2">Pastikan data pelanggan sudah benar sebelum order dikirim.</p>
                                </div>
                                <div class="flex h-14 w-14 items-center justify-center rounded-[1.5rem] bg-artisan-primary text-xl font-black italic text-white shadow-artisan">
                                    {{ strtoupper(substr($customer_name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20 mb-1">Nama Pemesan</p>
                                    <div class="rounded-[2rem] bg-slate-50/70 p-5">
                                        <p class="text-[13px] font-black italic text-artisan-primary">{{ $customer_name }}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20 mb-1">Nomor WhatsApp</p>
                                    <div class="rounded-[2rem] bg-slate-50/70 p-5">
                                        <p class="text-[13px] font-black italic text-artisan-primary">{{ $customer_phone }}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20 mb-1">Metode Layanan</p>
                                    <div class="rounded-[2rem] bg-slate-50/70 p-5">
                                        <p class="text-[11px] font-black uppercase tracking-widest text-artisan-secondary">{{ $order_type }}</p>
                                    </div>
                                </div>
                                @if($notes)
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20 mb-1">Catatan Khusus</p>
                                        <div class="rounded-[2rem] bg-slate-50/70 p-5">
                                            <p class="text-[11px] font-bold text-artisan-primary/60 italic">"{{ $notes }}"</p>
                                        </div>
                                    </div>
                                @endif
                                @if($order_type !== 'regular')
                                    <div class="md:col-span-2 pt-2">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20 mb-2">Alamat {{ $order_type === 'pickup' ? 'Penjemputan' : 'Pengantaran' }}</p>
                                        <div class="rounded-[2rem] bg-slate-50/70 p-5">
                                            <p class="text-[11px] font-bold text-artisan-primary/60 leading-relaxed">{{ $order_type === 'pickup' ? $pickup_address : $delivery_address }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Services Card -->
                        <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-artisan-lg overflow-hidden">
                            <div class="px-5 py-5 bg-slate-50/50 sm:px-8 sm:py-7 lg:px-10">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary">Curated Services</h3>
                                        <p class="mt-2 text-[10px] font-bold text-artisan-primary/40">Semua layanan yang akan diproses pada order ini.</p>
                                    </div>
                                    <div class="rounded-[1.5rem] bg-white px-4 py-3 text-center shadow-sm">
                                        <p class="text-[8px] font-black uppercase tracking-[0.3em] text-artisan-primary/20">Item</p>
                                        <p class="mt-2 text-[12px] font-black text-artisan-primary">{{ collect($items)->whereNotNull('service_id')->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4 p-4 sm:p-6 lg:p-8">
                                @foreach($items as $item)
                                    @if(!empty($item['service_id']))
                                        @php $service = $availableServices->find($item['service_id']); @endphp
                                        @if($service)
                                            <div class="rounded-[2rem] border border-slate-100 bg-white p-4 sm:p-5 transition-colors hover:bg-slate-50/50">
                                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                                    <div>
                                                        <p class="headline-editorial text-lg italic text-artisan-primary mb-1">{{ $service->name }}</p>
                                                    <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/20">{{ $item['quantity'] }} Unit • Rp {{ number_format($service->price, 0, ',', '.') }} / Unit</p>
                                                </div>
                                                    <div class="rounded-[1.5rem] bg-artisan-primary px-5 py-4 text-right text-white shadow-artisan">
                                                        <p class="text-[8px] font-black uppercase tracking-[0.3em] text-white/50">Subtotal</p>
                                                        <p class="mt-2 text-lg font-black italic">Rp {{ number_format($service->price * $item['quantity'], 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right: Summary & Action -->
                    <div class="col-span-12 lg:col-span-5 xl:col-span-4">
                        <div class="space-y-5 lg:space-y-6 xl:sticky xl:top-28">
                        <!-- Premium Receipt Card -->
                        <div class="bg-white rounded-[2.5rem] sm:rounded-[3rem] p-5 sm:p-8 lg:p-10 border border-slate-100 shadow-artisan-lg relative overflow-hidden">
                            <!-- Subtle decorative elements -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-artisan-secondary/5 rounded-bl-[4rem]"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-artisan-primary/5 rounded-tr-[3rem]"></div>
                            
                            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-8 whitespace-nowrap">Ringkasan Biaya</h3>
                            
                            <div class="space-y-4 relative z-10">
                                <div class="flex justify-between items-center text-artisan-primary/70">
                                    <span class="text-[10px] font-black tracking-widest uppercase">Subtotal</span>
                                    <span class="text-[12px] font-black italic text-artisan-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($pickupFee > 0)
                                    <div class="flex justify-between items-center text-artisan-primary/70">
                                        <span class="text-[10px] font-black tracking-widest uppercase">Biaya Penjemputan</span>
                                        <span class="text-[12px] font-black italic text-artisan-primary">Rp {{ number_format($pickupFee, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if($deliveryFee > 0)
                                    <div class="flex justify-between items-center text-artisan-primary/70">
                                        <span class="text-[10px] font-black tracking-widest uppercase">Biaya Pengantaran</span>
                                        <span class="text-[12px] font-black italic text-artisan-primary">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if($discount_amount > 0)
                                    <div class="flex justify-between items-center text-artisan-secondary">
                                        <span class="text-[10px] font-black tracking-widest uppercase">Diskon</span>
                                        <span class="text-[12px] font-black italic">-Rp {{ number_format($discount_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Total Section stacked to prevent text wrapping -->
                            <div class="mt-8 pt-6 border-t border-slate-100 relative z-10">
                                <div class="rounded-[2rem] bg-slate-50/70 p-5">
                                    <span class="text-[9px] font-black uppercase tracking-[0.3em] text-artisan-secondary">Total Estimasi</span>
                                    <span class="mt-3 block text-3xl font-manrope font-black italic text-artisan-primary truncate">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2.5rem] sm:rounded-[3rem] p-5 sm:p-8 lg:p-10 border border-slate-100 shadow-artisan-lg space-y-6">
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-2">Metode Pembayaran</h3>
                                <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">Pilih cara pembayaran yang paling nyaman. Jika sudah transfer via QRIS sekarang, unggah bukti agar outlet bisa memverifikasi lebih cepat.</p>
                            </div>

                            <div class="space-y-4">
                                <label class="flex items-start gap-4 rounded-[2rem] border p-5 cursor-pointer transition-all {{ $payment_method === 'pay_at_store' ? 'border-artisan-primary bg-artisan-primary text-white shadow-artisan' : 'border-slate-100 hover:border-artisan-secondary/30 hover:bg-slate-50/50' }}">
                                    <input type="radio" wire:model.live="payment_method" value="pay_at_store" class="mt-1 text-artisan-primary focus:ring-artisan-secondary">
                                    <div>
                                        <p class="text-[11px] font-black uppercase tracking-widest {{ $payment_method === 'pay_at_store' ? 'text-artisan-secondary' : 'text-artisan-primary' }}">Bayar di Toko</p>
                                        <p class="text-[10px] font-bold mt-2 leading-relaxed {{ $payment_method === 'pay_at_store' ? 'text-white/70' : 'text-artisan-primary/50' }}">Pesanan dibuat sekarang, pembayaran dilakukan saat datang ke outlet atau saat penyerahan sepatu.</p>
                                    </div>
                                </label>

                                @if($outlet->qris_image_path)
                                    <label class="flex items-start gap-4 rounded-[2rem] border p-5 cursor-pointer transition-all {{ $payment_method === 'qris' ? 'border-artisan-primary bg-artisan-primary text-white shadow-artisan' : 'border-slate-100 hover:border-artisan-secondary/30 hover:bg-slate-50/50' }}">
                                        <input type="radio" wire:model.live="payment_method" value="qris" class="mt-1 text-artisan-primary focus:ring-artisan-secondary">
                                        <div>
                                            <p class="text-[11px] font-black uppercase tracking-widest {{ $payment_method === 'qris' ? 'text-artisan-secondary' : 'text-artisan-primary' }}">Bayar via QRIS Outlet</p>
                                            <p class="text-[10px] font-bold mt-2 leading-relaxed {{ $payment_method === 'qris' ? 'text-white/70' : 'text-artisan-primary/50' }}">Scan QRIS cabang ini dan unggah bukti pembayaran. Status akan berubah menjadi menunggu verifikasi outlet.</p>
                                        </div>
                                    </label>
                                @endif
                            </div>

                            @if($payment_method === 'qris' && $outlet->qris_image_path)
                                <div class="space-y-5 rounded-[2rem] border border-slate-100 bg-slate-50/60 p-4 sm:p-5">
                                    <div class="rounded-[1.5rem] border border-slate-100 bg-white p-4 shadow-sm">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($outlet->qris_image_path) }}" alt="QRIS {{ $outlet->name }}" class="mx-auto max-h-72 rounded-2xl object-contain">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-artisan-primary/40 mb-3">Upload Bukti Bayar</label>
                                        <input type="file" wire:model="payment_proof" accept=".jpg,.jpeg,.png,.webp" class="w-full rounded-2xl bg-white text-[10px] font-bold text-artisan-primary/60">
                                        @error('payment_proof') <p class="text-red-500 text-[10px] mt-2 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                    </div>

                                    @if($payment_proof)
                                        <div class="rounded-[1.5rem] border border-slate-100 bg-white p-4">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-artisan-secondary mb-3">Preview Bukti Bayar</p>
                                            <img src="{{ $payment_proof->temporaryUrl() }}" alt="Preview bukti pembayaran" class="mx-auto max-h-72 rounded-2xl object-contain">
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-artisan-primary/40 mb-3">Catatan Pembayaran</label>
                                        <textarea wire:model="payment_notes" rows="3" placeholder="Contoh: Sudah transfer via QRIS atas nama Andi." class="w-full px-6 py-4 bg-white border-none rounded-[2rem] text-[12px] font-bold text-artisan-primary focus:ring-2 focus:ring-artisan-secondary/20 transition-all outline-none placeholder:text-artisan-primary/20 resize-none"></textarea>
                                        @error('payment_notes') <p class="text-red-500 text-[10px] mt-2 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                        <p class="text-[9px] text-artisan-primary/30 mt-2 font-bold uppercase tracking-widest">Jika belum transfer sekarang, Anda tetap bisa lanjut membuat order tanpa upload bukti.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Notice Card -->
                        <div class="p-6 bg-slate-50/60 rounded-3xl border border-slate-100 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 w-6 h-6 rounded-full bg-artisan-primary/5 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-artisan-primary/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-[9px] font-black uppercase tracking-widest text-artisan-primary mb-1">Informasi</h4>
                                    <p class="text-[10px] font-medium text-artisan-primary/60 leading-relaxed">
                                        @if($outlet->qris_image_path)
                                            QRIS outlet ini tersedia. Anda bisa scan setelah pesanan dibuat atau gunakan instruksi di bawah untuk menyiapkan pembayaran.
                                        @else
                                            Pembayaran dilakukan secara tunai/transfer saat restorasi selesai di outlet kami.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($outlet->qris_image_path && $payment_method !== 'qris')
                            <div class="bg-white rounded-[2.5rem] sm:rounded-[3rem] p-5 sm:p-8 lg:p-10 border border-slate-100 shadow-artisan-lg space-y-6">
                                <div>
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-2">QRIS Outlet</h3>
                                    <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">
                                        QRIS yang ditampilkan mengikuti cabang yang Anda pilih: {{ $outlet->name }}.
                                    </p>
                                </div>

                                <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-5">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($outlet->qris_image_path) }}" alt="QRIS {{ $outlet->name }}" class="mx-auto max-h-80 rounded-2xl bg-white object-contain">
                                </div>

                                @if($outlet->qris_notes)
                                    <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">{{ $outlet->qris_notes }}</p>
                                @else
                                    <p class="text-[10px] font-bold text-artisan-primary/50 leading-relaxed">Scan QRIS ini untuk pembayaran outlet {{ $outlet->name }} sesuai total estimasi di samping.</p>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="sticky bottom-3 z-30 space-y-4 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 bg-white/95 p-4 sm:p-6 shadow-artisan-lg backdrop-blur-2xl lg:static lg:bg-white">
                            <button wire:click="save" wire:loading.attr="disabled" 
                                class="w-full py-5 sm:py-7 bg-emerald-600 text-white rounded-[2.5rem] font-manrope font-black italic text-base sm:text-lg hover:bg-emerald-700 transition-all duration-500 shadow-xl shadow-emerald-900/10 flex items-center justify-center gap-4 active:scale-[0.98] disabled:opacity-50">
                                <span wire:loading.remove wire:target="save" class="flex items-center gap-4">
                                    Buat Pesanan Sekarang
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                </span>
                                <span wire:loading wire:target="save" class="flex items-center gap-4">
                                    <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memproses Pesanan...
                                </span>
                            </button>
                            <button wire:click="prevStep" class="w-full rounded-[2rem] border border-slate-100 p-4 text-[10px] font-black uppercase tracking-[0.3em] text-artisan-primary/20 hover:text-artisan-secondary transition-colors">
                                Kembali ke Data Sebelumnya
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Enhanced Global Footer -->
    <div class="relative bg-artisan-primary overflow-hidden pt-20 pb-12 mt-12">
        <!-- Decoration -->
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-artisan-secondary/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12 text-center md:text-left">
                <div class="space-y-4">
                    <h4 class="headline-editorial text-2xl italic text-white uppercase tracking-widest">{{ $outlet->name }}</h4>
                    <p class="text-[10px] text-white/30 font-bold uppercase tracking-[0.2em]">Master Artisan Shoecare Network — Established Excellence</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-10">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $outlet->phone) }}" target="_blank" 
                        class="group flex flex-col items-center md:items-start gap-1">
                        <span class="text-[9px] font-black uppercase tracking-widest text-artisan-secondary animate-pulse">Contact Artisan</span>
                        <span class="text-[13px] font-black italic text-white group-hover:text-artisan-secondary transition-colors">{{ $outlet->phone }}</span>
                    </a>
                    
                    <a href="{{ route('track') }}" 
                        class="px-10 py-5 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] text-white hover:bg-white/10 hover:border-white/20 transition-all shadow-xl">
                        Track Restoration
                    </a>
                </div>
            </div>
            
            <div class="mt-20 pt-10 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-6">
                <p class="text-[9px] font-black uppercase tracking-widest text-white/10">&copy; {{ date('Y') }} Shoeclean Artisan. All Rights Reserved.</p>
                <div class="flex items-center gap-6">
                    <div class="w-2 h-2 bg-artisan-secondary rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-white/10 leading-none">Status: Ready for Restoration</span>
                </div>
            </div>
        </div>
    </div>
</div>
