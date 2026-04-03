<div class="py-8">
    {{-- Survey Pop-up Modal --}}
    @if($showSurveyModal && $pendingSurvey)
        <div x-data="{ open: true }" x-show="open" x-cloak
            class="fixed inset-0 z-[60] flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-artisan-primary/50 backdrop-blur-sm"></div>
            {{-- Modal --}}
            <div class="relative bg-white rounded-[2rem] p-8 md:p-10 max-w-lg w-full shadow-2xl"
                x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-xl mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <h2 class="font-manrope font-extrabold text-2xl text-artisan-primary mb-2">Ada survei untuk Anda.</h2>
                    <p class="text-artisan-primary/50 text-sm leading-relaxed mb-2">{{ $pendingSurvey->description ?: 'Bantu kami meningkatkan platform dengan mengisi survei singkat ini.' }}</p>
                    <p class="font-manrope font-bold text-indigo-600 text-lg">"{{ $pendingSurvey->title }}"</p>
                </div>
                <div class="mt-8 space-y-3">
                    <a href="{{ route('survey.fill', $pendingSurvey) }}" class="block w-full py-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all active:scale-[0.98] text-sm text-center">
                        Isi Survei Sekarang
                    </a>
                    <button wire:click="dismissSurveyModal" @click="open = false" class="block w-full py-4 bg-gray-100 text-artisan-primary/60 font-bold rounded-2xl hover:bg-gray-200 transition-all active:scale-[0.98] text-sm">
                        Nanti saja
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 rounded-[2rem] border border-red-200 bg-red-50 p-5 text-red-800 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-red-600">Akses Fitur Dibatasi</p>
            <p class="mt-2 text-sm font-semibold leading-relaxed text-red-800/80">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Subscription Status Banner --}}
    @if(auth()->user()->isOwner())
        @if(!$orderLimitInfo['allowed'])
            {{-- Limit Reached - Red Alert --}}
            <div class="mb-8 p-6 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-[2rem] shadow-artisan flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <p class="font-manrope font-extrabold text-xl">Kuota Order Habis!</p>
                        <p class="text-white/80 text-sm">Anda telah mencapai batas {{ number_format($orderLimitInfo['limit'] ?? 100) }} order. Upgrade paket atau beli kuota untuk lanjutkan.</p>
                    </div>
                </div>
                <a href="{{ route('subscription') }}" class="px-6 py-3 bg-white text-red-600 font-bold rounded-2xl hover:bg-red-50 transition-all active:scale-95 whitespace-nowrap shadow-lg text-sm">
                    Upgrade Sekarang
                </a>
            </div>
        @elseif($currentPlan === 'free' && $orderLimitInfo['remaining'] !== null && $orderLimitInfo['remaining'] <= 20)
            {{-- Warning - Approaching Limit --}}
            <div class="mb-8 p-6 bg-gradient-to-r from-amber-400 to-orange-500 text-white rounded-[2rem] shadow-artisan flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-manrope font-extrabold">Kuota Hampir Habis!</p>
                        <p class="text-white/80 text-sm">Sisa {{ number_format($orderLimitInfo['remaining']) }} order dari batas {{ number_format($orderLimitInfo['limit'] ?? 100) }}.</p>
                    </div>
                </div>
                <a href="{{ route('subscription') }}" class="px-6 py-3 bg-white text-orange-600 font-bold rounded-2xl hover:bg-orange-50 transition-all active:scale-95 whitespace-nowrap shadow-lg text-sm">
                    Lihat Paket →
                </a>
            </div>
        @elseif(in_array($currentPlan, ['pro', 'business']))
            {{-- Active Subscription Badge --}}
            <div class="mb-8 p-4 bg-gradient-to-r {{ $currentPlan === 'pro' ? 'from-blue-50 to-indigo-50 border border-indigo-100' : 'from-purple-50 to-pink-50 border border-purple-100' }} rounded-2xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $currentPlan === 'pro' ? 'bg-indigo-500' : 'bg-purple-500' }} text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-manrope font-bold {{ $currentPlan === 'pro' ? 'text-indigo-700' : 'text-purple-700' }}">
                            Paket {{ ucfirst($currentPlan) }} Aktif
                        </span>
                        <span class="text-xs text-artisan-primary/40 ml-2">Pesanan tanpa batas</span>
                    </div>
                </div>
                <a href="{{ route('subscription') }}" class="text-xs font-bold {{ $currentPlan === 'pro' ? 'text-indigo-500' : 'text-purple-500' }} hover:underline">Kelola Paket</a>
            </div>
        @endif
    @endif

    <!-- Page Header -->
    <div class="mb-12">
        <h1 class="headline-editorial text-4xl lg:text-5xl">Dashboard</h1>
        <p class="text-artisan-secondary/60 font-medium mt-2">Selamat datang kembali di outlet, {{ auth()->user()->name }}</p>
    </div>

    @if($currentPlan === 'business')
        <div class="mb-8 overflow-hidden rounded-[2rem] bg-gradient-to-br from-artisan-primary via-[#0f2e27] to-artisan-secondary p-6 text-white shadow-artisan">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.24em] text-white/80">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                        Business Multi Outlet
                    </div>
                    <h2 class="font-manrope text-2xl font-extrabold">Monitor performa outlet dari satu dashboard</h2>
                    <p class="mt-2 text-sm font-semibold leading-relaxed text-white/75">
                        Scope aktif: {{ $activeScopeLabel }}. Anda bisa memakai outlet switcher untuk fokus ke satu cabang, atau biarkan tetap gabungan untuk melihat gambaran bisnis yang lebih luas.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 xl:min-w-[440px]">
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-white/50">Cabang Dalam Scope</p>
                        <p class="mt-2 text-3xl font-manrope font-extrabold">{{ $scopedOutlets->count() }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-white/50">Total Outlet Owner</p>
                        <p class="mt-2 text-3xl font-manrope font-extrabold">{{ $ownedOutletCount }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-white/50">Mode Dashboard</p>
                        <p class="mt-2 text-sm font-manrope font-extrabold uppercase tracking-[0.18em] text-white">
                            {{ $isCombinedOutletScope ? 'Gabungan' : 'Per Outlet' }}
                        </p>
                    </div>
                </div>
            </div>

            @if($isCombinedOutletScope && $topPerformingOutlet)
                <div class="mt-6 grid grid-cols-1 gap-4 border-t border-white/10 pt-6 lg:grid-cols-[1.3fr_1fr]">
                    <div class="rounded-[1.6rem] border border-white/10 bg-white/8 px-5 py-5 backdrop-blur-sm">
                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-emerald-200">Cabang Paling Produktif Bulan Ini</p>
                        <h3 class="mt-2 text-xl font-manrope font-extrabold">{{ $topPerformingOutlet->name }}</h3>
                        <p class="mt-2 text-sm font-semibold text-white/70">Pendapatan tertinggi dalam periode yang sedang Anda lihat.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-[1.6rem] border border-white/10 bg-white/8 px-5 py-5 backdrop-blur-sm">
                            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-white/45">Revenue</p>
                            <p class="mt-2 text-xl font-manrope font-extrabold">Rp {{ number_format($topPerformingOutlet->revenue_total, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-[1.6rem] border border-white/10 bg-white/8 px-5 py-5 backdrop-blur-sm">
                            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-white/45">Total Order</p>
                            <p class="mt-2 text-xl font-manrope font-extrabold">{{ number_format($topPerformingOutlet->orders_total) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($showBusinessUpsell)
        <div class="mb-8 rounded-[2rem] border border-purple-200 bg-gradient-to-r from-purple-50 via-fuchsia-50 to-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-500 text-white shadow-lg shadow-purple-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h5l2 3h11M5 7v10a2 2 0 002 2h10a2 2 0 002-2v-7M8 17h8" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.24em] text-purple-600">Fitur Bisnis</p>
                        <h3 class="mt-2 font-manrope text-xl font-extrabold text-artisan-primary">Laporan Gabungan Semua Cabang Tersedia di Business</h3>
                        <p class="mt-2 max-w-3xl text-sm font-semibold leading-relaxed text-artisan-primary/65">
                            Paket Pro Anda saat ini fokus ke 1 outlet aktif. Karena akun ini punya {{ $ownedOutletCount }} cabang, dashboard dan export sedang dibatasi ke outlet yang dipilih agar tetap konsisten. Upgrade ke Business untuk melihat ringkasan gabungan semua cabang sekaligus.
                        </p>
                    </div>
                </div>
                <a href="{{ route('subscription') }}" class="inline-flex items-center justify-center rounded-2xl bg-artisan-primary px-5 py-3 text-sm font-bold text-white transition hover:bg-artisan-secondary">
                    Lihat Paket Business
                </a>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-artisan-surface-low rounded-[2rem] p-8 mb-12">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-artisan-primary shadow-artisan">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold text-artisan-primary">Filter Outlet</h3>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest">
                        @if($isCombinedOutletScope)
                            Semua Cabang Aktif
                        @elseif($currentPlan === 'business' && $ownedOutletCount > 1)
                            Outlet Aktif Terpilih
                        @else
                            Pilih Periode
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="rounded-full bg-white px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-artisan-primary/60 shadow-sm">
                    Scope: {{ $activeScopeLabel }}
                </div>
                <select wire:model.live="month" class="artisan-input !w-40">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>

                <select wire:model.live="year" class="artisan-input !w-32">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>

                <button wire:click="resetFilters" class="px-6 py-3 bg-white text-artisan-primary font-bold rounded-2xl shadow-artisan hover:bg-artisan-primary hover:text-white transition-all duration-300 active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Segarkan
                </button>
            </div>
        </div>
    </div>

    @if($isCombinedOutletScope && $scopedOutlets->isNotEmpty())
        <div class="mb-12 rounded-[2rem] border border-artisan-primary/10 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.24em] text-artisan-secondary">Cabang Dalam Ringkasan</p>
                    <h3 class="mt-2 font-manrope text-xl font-extrabold text-artisan-primary">Dashboard ini sedang merangkum {{ $scopedOutlets->count() }} outlet sekaligus</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($scopedOutlets as $outlet)
                        <span class="rounded-full bg-artisan-surface-low px-4 py-2 text-xs font-bold text-artisan-primary/70">
                            {{ $outlet->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards - Responsive Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Today Orders -->
        <div class="card-artisan p-8">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 bg-artisan-primary text-white rounded-2xl flex items-center justify-center shadow-artisan">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-[0.2em] mb-1">Pesanan Hari Ini</p>
                    <p class="text-4xl font-manrope font-extrabold text-artisan-primary">{{ $todayOrders }}</p>
                </div>
            </div>
        </div>

        <!-- Today Revenue -->
        <div class="card-artisan p-8 bg-artisan-secondary text-white">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-artisan border border-white/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-white/60 font-black uppercase tracking-[0.2em] mb-1">Pendapatan Hari Ini</p>
                    <p class="text-4xl font-manrope font-extrabold">Rp {{ number_format($todayRevenue / 1000, 0) }}K</p>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="card-artisan p-8">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-[0.2em] mb-1">Menunggu Restorasi</p>
                    <p class="text-4xl font-manrope font-extrabold text-orange-600">{{ $pendingOrders }}</p>
                </div>
            </div>
        </div>

        <!-- Ready Orders -->
        <div class="card-artisan p-8">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-[0.2em] mb-1">Siap Diambil</p>
                    <p class="text-4xl font-manrope font-extrabold text-blue-600">{{ $readyOrders }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Stats - Responsive -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="card-artisan p-8 bg-artisan-primary text-white">
            <p class="text-xs text-white/40 font-black uppercase tracking-[0.2em] mb-2">Pesanan Bulanan ({{ date('M Y', mktime(0, 0, 0, $month, 1)) }})</p>
            <p class="text-5xl font-manrope font-extrabold">{{ $monthOrders }}</p>
        </div>
        <div class="card-artisan p-8 bg-artisan-secondary text-white">
            <p class="text-xs text-white/40 font-black uppercase tracking-[0.2em] mb-2">Pendapatan Bulanan</p>
            <p class="text-5xl font-manrope font-extrabold">Rp {{ number_format($monthRevenue / 1000, 0) }}K</p>
        </div>
        <div class="card-artisan p-8">
            <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-[0.2em] mb-2">Total Pelanggan Terkelola</p>
            <p class="text-5xl font-manrope font-extrabold text-artisan-primary">{{ $totalCustomers }}</p>
        </div>
    </div>

    <!-- Chart & Recent Orders - Stack on mobile -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 mb-12">
        <!-- Revenue Chart -->
        <div class="card-artisan p-5 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 md:mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 shrink-0 bg-artisan-primary text-white rounded-2xl flex items-center justify-center shadow-artisan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div>
                        <h3 class="font-manrope font-bold text-artisan-primary">Tren Pendapatan</h3>
                        <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest">{{ date('F Y', mktime(0, 0, 0, $month, 1)) }}</p>
                    </div>
                </div>
            </div>
            <div class="relative h-[250px] sm:h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card-artisan p-5 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 md:mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 shrink-0 bg-artisan-secondary text-white rounded-2xl flex items-center justify-center shadow-artisan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div>
                        <h3 class="font-manrope font-bold text-artisan-primary">Aktivitas Outlet</h3>
                        <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest">Restorasi Terbaru</p>
                    </div>
                </div>
                <a href="{{ route('orders.index') }}" class="text-[10px] w-full sm:w-auto text-center font-black uppercase tracking-widest text-artisan-secondary hover:text-white hover:bg-artisan-secondary transition-all duration-300 py-3 sm:py-2 px-4 bg-artisan-surface-low rounded-xl sm:rounded-full active:scale-95 shadow-artisan-sm">Lihat Buku Besar →</a>
            </div>
            @if($recentOrders->isEmpty())
                <div class="text-center py-12">
                    <p class="text-artisan-primary/40 font-medium italic">Outlet sedang sepi...</p>
                </div>
            @else
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-artisan-primary/10 hover:[&::-webkit-scrollbar-thumb]:bg-artisan-primary/30 [&::-webkit-scrollbar-thumb]:rounded-full transition-colors">
                    @foreach($recentOrders as $order)
                        <a href="{{ route('orders.view', $order->id) }}" class="flex items-center justify-between p-4 bg-artisan-surface-low rounded-2xl hover:bg-artisan-secondary hover:text-white transition-all duration-300 active:scale-95 group gap-2">
                            <div class="flex items-center gap-3 sm:gap-4 overflow-hidden">
                                <div class="w-10 h-10 shrink-0 bg-white rounded-xl flex items-center justify-center text-artisan-primary shadow-sm group-hover:bg-white/20 group-hover:text-white">
                                    <span class="text-xs font-black">{{ substr($order->customer->name, 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-manrope font-bold text-sm truncate">{{ $order->customer->name }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40 truncate">{{ $order->invoice_number }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-manrope font-extrabold text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                <div class="mt-0.5">
                                    <span class="text-[8px] font-black uppercase tracking-[0.2em] {{ match ($order->status) {
                                        'completed', 'picked_up' => 'text-emerald-500',
                                        'cancelled' => 'text-red-500',
                                        'ready' => 'text-blue-500',
                                        default => 'text-orange-500'
                                    } }} group-hover:text-white">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Export Section (Owner + Admin only) -->
    @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
        <div
            x-data="{
                exportLockedModal: false,
                openExportLockedModal() { this.exportLockedModal = true; },
                closeExportLockedModal() { this.exportLockedModal = false; }
            }"
            @keydown.escape.window="closeExportLockedModal()"
            class="card-artisan p-8"
        >
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-artisan-primary text-white rounded-2xl flex items-center justify-center shadow-artisan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-manrope font-bold text-artisan-primary">Arsip Outlet</h3>
                        <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest">Buat Laporan Utama</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @unless(auth()->user()->hasFeature('exports'))
                        <span class="px-4 py-2 rounded-full bg-amber-100 text-[10px] font-black uppercase tracking-widest text-amber-700">
                            Fitur Pro
                        </span>
                    @endunless
                    @if($isCombinedOutletScope)
                        <span class="px-4 py-2 rounded-full bg-purple-100 text-[10px] font-black uppercase tracking-widest text-purple-700">
                            Business Multi Outlet
                        </span>
                    @elseif($showBusinessUpsell)
                        <span class="px-4 py-2 rounded-full bg-slate-200 text-[10px] font-black uppercase tracking-widest text-slate-700">
                            Pro 1 Outlet Aktif
                        </span>
                    @endif
                    <div class="px-5 py-2 bg-artisan-surface-low rounded-full text-[10px] font-black uppercase tracking-widest text-artisan-secondary">
                        Periode Arsip: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}
                    </div>
                </div>
            </div>

            @if($showBusinessUpsell)
                <div class="mb-6 rounded-[1.75rem] border border-purple-200 bg-purple-50 px-5 py-4">
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-purple-700">Export Saat Ini Mengikuti Outlet Aktif</p>
                    <p class="mt-2 text-sm font-semibold leading-relaxed text-purple-900/75">Anda tetap bisa export laporan untuk outlet aktif. Jika ingin export gabungan semua cabang dalam satu file, fitur itu tersedia di paket Business.</p>
                </div>
            @endif
            
            @unless(auth()->user()->hasFeature('exports'))
                <div class="mb-6 rounded-[1.75rem] border border-amber-200 bg-amber-50 px-5 py-4">
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-700">Export Tersedia Mulai Pro</p>
                    <p class="mt-2 text-sm font-semibold leading-relaxed text-amber-800/80">Anda bisa melihat fitur export di sini, tetapi untuk mengunduh laporan Excel atau PDF silakan upgrade ke paket Pro atau Business.</p>
                </div>
            @endunless

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 {{ auth()->user()->hasFeature('exports') ? '' : 'opacity-80' }}">
                @php
                    $exportButtons = [
                        ['label' => 'Excel Pesanan', 'route' => route('reports.orders.excel', ['month' => $month, 'year' => $year]), 'class' => '!bg-emerald-700', 'icon' => 'sheet'],
                        ['label' => 'PDF Pesanan', 'route' => route('reports.orders.pdf', ['month' => $month, 'year' => $year]), 'class' => '!bg-red-700', 'icon' => 'file'],
                        ['label' => 'Excel Pengeluaran', 'route' => route('reports.expenses.excel', ['month' => $month, 'year' => $year]), 'class' => '!bg-emerald-700', 'icon' => 'sheet'],
                        ['label' => 'PDF Pengeluaran', 'route' => route('reports.expenses.pdf', ['month' => $month, 'year' => $year]), 'class' => '!bg-red-700', 'icon' => 'file'],
                    ];
                @endphp
                @foreach($exportButtons as $button)
                    @if(auth()->user()->hasFeature('exports'))
                        <a href="{{ $button['route'] }}"
                            class="btn-artisan-primary !py-4 flex items-center justify-center gap-2 {{ $button['class'] }} relative overflow-hidden">
                            @if($button['icon'] === 'sheet')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            @endif
                            {{ $button['label'] }}
                        </a>
                    @else
                        <button
                            type="button"
                            @click="openExportLockedModal()"
                            class="btn-artisan-primary !py-4 flex items-center justify-center gap-2 {{ $button['class'] }} relative overflow-hidden"
                        >
                            @if($button['icon'] === 'sheet')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            @endif
                            {{ $button['label'] }}
                            <span class="absolute right-2 top-2 rounded-full bg-white/20 px-2 py-0.5 text-[9px] font-black uppercase tracking-[0.18em] text-white">Pro</span>
                        </button>
                    @endif
                @endforeach
            </div>

            <div
                x-cloak
                x-show="exportLockedModal"
                class="fixed inset-0 z-[70] flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <div class="absolute inset-0 bg-artisan-primary/45 backdrop-blur-sm" @click="closeExportLockedModal()"></div>
                <div class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-white p-6 shadow-2xl sm:p-8">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 shadow-sm">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.24em] text-amber-600">Upgrade Diperlukan</p>
                    <h3 class="mt-3 text-2xl font-manrope font-extrabold text-artisan-primary">Fitur Export Terkunci</h3>
                    <p class="mt-3 text-sm font-semibold leading-relaxed text-artisan-primary/60">Laporan Excel dan PDF sudah tersedia di sistem, tetapi untuk mengunduh arsip outlet Anda perlu upgrade ke paket Pro atau Business.</p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @if(auth()->user()->isOwner())
                            <a href="{{ route('subscription') }}" class="inline-flex w-full items-center justify-center rounded-[1.4rem] bg-artisan-primary px-5 py-4 text-sm font-bold text-white transition hover:bg-artisan-secondary">
                                Lihat Paket Upgrade
                            </a>
                        @endif
                        <button type="button" @click="closeExportLockedModal()" class="inline-flex w-full items-center justify-center rounded-[1.4rem] bg-gray-100 px-5 py-4 text-sm font-bold text-artisan-primary/60 transition hover:bg-gray-200">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        const ctx = document.getElementById('revenueChart');
        let chartInstance = null;

        const initChart = (labels, data) => {
            if (chartInstance) {
                chartInstance.destroy();
            }

            if (!ctx) return;

            chartInstance = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue',
                        data: data,
                        borderColor: '#001610',
                        backgroundColor: 'rgba(58, 103, 88, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#3a6758'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    }
                                    return 'Rp ' + value;
                                },
                                font: { size: 10 }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            ticks: { font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        };

        // Initial render
        initChart(@json($chartLabels), @json($chartData));

        // Listen for updates
        Livewire.on('chart-data-updated', ({ labels, data }) => {
            initChart(labels, data);
        });
    });
</script>


</div>
