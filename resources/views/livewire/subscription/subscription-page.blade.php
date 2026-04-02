<div class="py-6 sm:py-8">
    <!-- Page Header -->
    <div class="relative mb-8 overflow-hidden rounded-[2rem] bg-gradient-to-br from-artisan-primary via-artisan-primary to-[#153f41] px-5 py-6 text-white shadow-artisan-lg sm:mb-12 sm:rounded-[2.5rem] sm:px-8 sm:py-10 lg:px-10">
        <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-artisan-secondary/25 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-28 w-28 rounded-full bg-white/10 blur-3xl"></div>
        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[10px] font-black uppercase tracking-[0.28em] text-artisan-secondary">Subscription</p>
                <h1 class="headline-editorial mt-3 text-3xl text-white sm:text-4xl lg:text-5xl">Langganan</h1>
                <p class="mt-3 max-w-xl text-sm font-semibold leading-relaxed text-white/75">Pilih paket yang paling sesuai dengan tahap bisnis Anda, lalu kelola order, outlet, dan kuota dari satu tempat.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap">
                <div class="rounded-[1.5rem] border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-xl">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/45">Tier Saat Ini</p>
                    <p class="mt-2 text-sm font-black text-white">{{ $planDetails[$currentPlan]['name'] ?? 'Free' }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-xl">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/45">Status Order</p>
                    <p class="mt-2 text-sm font-black text-white">{{ $orderLimitInfo['remaining'] === null ? 'Unlimited' : 'Terbatas' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:mb-8 sm:gap-4 sm:p-6">
            <div class="w-12 h-12 bg-emerald-500 text-white rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-emerald-800 font-bold">{{ session('success') }}</p>
        </div>
    @endif

    @if($errorMessage)
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 sm:mb-8 sm:gap-4 sm:p-6">
            <div class="w-12 h-12 bg-red-500 text-white rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-red-800 font-bold">{{ $errorMessage }}</p>
        </div>
    @endif

    <!-- Current Plan Status -->
    <div class="card-artisan mb-8 overflow-hidden border-2 p-5 sm:mb-12 sm:p-8 {{ $currentPlan === 'free' ? 'border-orange-200 bg-gradient-to-br from-orange-50 to-white' : 'border-emerald-200 bg-gradient-to-br from-emerald-50 to-white' }}">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between lg:gap-8">
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl shadow-artisan sm:h-16 sm:w-16
                    {{ $currentPlan === 'free' ? 'bg-gray-100 text-gray-600' : ($currentPlan === 'pro' ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white' : 'bg-gradient-to-br from-purple-500 to-pink-600 text-white') }}">
                    @if($currentPlan === 'free')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    @elseif($currentPlan === 'pro')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-[0.2em] mb-1">Paket Saat Ini</p>
                    <h2 class="font-manrope text-xl font-extrabold text-artisan-primary sm:text-2xl">
                        {{ $planDetails[$currentPlan]['name'] ?? 'Free' }}
                    </h2>
                    @if(isset($planDetails[$currentPlan]['subtitle']))
                        <p class="text-sm text-artisan-primary/40 mt-1">{{ $planDetails[$currentPlan]['subtitle'] }}</p>
                    @endif
                    @if($activeSubscription)
                        <p class="mt-1 text-sm text-artisan-primary/50">
                            Aktif hingga {{ $activeSubscription->expires_at?->format('d M Y') ?? 'Selamanya' }}
                            @if($activeSubscription->daysRemaining() !== null)
                                <span class="text-xs font-bold {{ $activeSubscription->daysRemaining() <= 7 ? 'text-red-500' : 'text-emerald-500' }}">
                                    ({{ $activeSubscription->daysRemaining() }} hari lagi)
                                </span>
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-artisan-primary/50 mt-1">Paket gratis aktif tanpa batas waktu.</p>
                    @endif
                </div>
            </div>

            <!-- Order Usage -->
            <div class="rounded-[1.75rem] bg-white/80 px-4 py-4 shadow-sm sm:px-5 sm:py-5 lg:min-w-[18rem] lg:text-right">
                <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-[0.2em] mb-2">Penggunaan Order</p>
                @if($orderLimitInfo['remaining'] === null)
                    <p class="text-xl font-manrope font-extrabold text-emerald-600 sm:text-2xl">Unlimited</p>
                    <p class="text-sm text-artisan-primary/50">{{ number_format($orderLimitInfo['total_orders']) }} order dibuat</p>
                @else
                    <p class="text-xl font-manrope font-extrabold {{ $orderLimitInfo['remaining'] <= 20 ? 'text-red-600' : 'text-artisan-primary' }} sm:text-2xl">
                        {{ number_format($orderLimitInfo['total_orders']) }} / {{ number_format($orderLimitInfo['limit']) }}
                    </p>
                    <!-- Progress Bar -->
                    @php $percentage = $orderLimitInfo['limit'] > 0 ? min(100, ($orderLimitInfo['total_orders'] / $orderLimitInfo['limit']) * 100) : 0; @endphp
                    <div class="w-full lg:w-64 h-3 bg-gray-200 rounded-full mt-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $percentage >= 80 ? 'bg-gradient-to-r from-red-500 to-orange-500' : 'bg-gradient-to-r from-emerald-500 to-teal-500' }}"
                            style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-xs text-artisan-primary/40 mt-1">
                        Sisa {{ number_format($orderLimitInfo['remaining']) }} order
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Pricing Plans -->
    <div class="mb-8">
        <div class="mb-6 flex items-center gap-3 sm:mb-8 sm:gap-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-artisan-primary text-white shadow-artisan sm:h-12 sm:w-12">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h3 class="font-manrope font-bold text-artisan-primary text-xl">Pilih Paket</h3>
                <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest">Berlangganan Bulanan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3 xl:gap-6">
            <!-- Free Plan -->
            <div class="card-artisan relative flex h-full flex-col p-5 sm:p-8 {{ $currentPlan === 'free' ? 'ring-2 ring-artisan-primary' : '' }}">
                @if($currentPlan === 'free')
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-artisan-primary text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                        Paket Anda
                    </div>
                @endif
                <div class="mb-6 text-center sm:mb-8">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 sm:h-16 sm:w-16">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-artisan-primary/35">{{ $planDetails['free']['subtitle'] }}</p>
                    <h3 class="font-manrope font-extrabold text-xl text-artisan-primary mt-2">{{ $planDetails['free']['name'] }}</h3>
                    <p class="mt-2 text-3xl font-manrope font-extrabold text-artisan-primary sm:text-4xl">Gratis</p>
                    <p class="text-sm text-artisan-primary/40 mt-1">Selamanya</p>
                    <p class="text-sm text-artisan-primary/55 leading-relaxed mt-4">{{ $planDetails['free']['description'] }}</p>
                </div>
                <ul class="mb-8 space-y-3">
                    @foreach($planDetails['free']['features'] as $feature)
                        <li class="flex items-center gap-3 rounded-2xl bg-artisan-surface-low/60 px-4 py-3 text-sm text-artisan-primary/70">
                            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                <div class="mt-auto">
                @if($currentPlan === 'free')
                    <button disabled class="w-full py-4 bg-gray-100 text-gray-400 font-bold rounded-2xl cursor-not-allowed text-sm">
                        Paket Saat Ini
                    </button>
                @else
                    <button disabled class="w-full py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl cursor-not-allowed text-sm">
                        {{ $planDetails['free']['cta'] }}
                    </button>
                @endif
                </div>
            </div>

            <!-- Pro Plan -->
            <div class="card-artisan relative flex h-full flex-col border-2 border-indigo-100 bg-gradient-to-br from-blue-50 to-indigo-50 p-5 sm:p-8 {{ $currentPlan === 'pro' ? 'ring-2 ring-indigo-500' : '' }}">
                @if($currentPlan === 'pro')
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                        Paket Anda
                    </div>
                @elseif($currentPlan === 'free')
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg">
                        Paling Cocok
                    </div>
                @endif
                <div class="mb-6 text-center sm:mb-8">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-indigo-200 sm:h-16 sm:w-16">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-indigo-500">{{ $planDetails['pro']['subtitle'] }}</p>
                    <h3 class="font-manrope font-extrabold text-xl text-indigo-700 mt-2">{{ $planDetails['pro']['name'] }}</h3>
                    <p class="mt-2 text-3xl font-manrope font-extrabold text-artisan-primary sm:text-4xl">{{ $planDetails['pro']['price_label'] }}</p>
                    <p class="text-sm text-artisan-primary/40 mt-1">{{ ($planDetails['pro']['is_published'] ?? true) ? 'per bulan' : 'Segera tersedia' }}</p>
                    <p class="text-sm text-artisan-primary/55 leading-relaxed mt-4">{{ $planDetails['pro']['description'] }}</p>
                </div>
                <ul class="mb-8 space-y-3">
                    @foreach($planDetails['pro']['features'] as $feature)
                        <li class="flex items-center gap-3 rounded-2xl bg-white/70 px-4 py-3 text-sm text-artisan-primary/70 shadow-sm">
                            <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                <div class="mt-auto">
                @if($currentPlan === 'pro')
                    <button disabled class="w-full py-4 bg-indigo-100 text-indigo-400 font-bold rounded-2xl cursor-not-allowed text-sm">
                        Paket Saat Ini
                    </button>
                @else
                    <button @if(!($planDetails['pro']['is_published'] ?? true)) disabled @endif wire:click="subscribePlan('pro')" wire:loading.attr="disabled"
                        class="w-full py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:shadow-xl hover:shadow-indigo-300 transition-all active:scale-[0.98] text-sm disabled:opacity-50">
                        <span wire:loading.remove wire:target="subscribePlan('pro')">
                            {{ !($planDetails['pro']['is_published'] ?? true) ? 'Coming Soon' : ($currentPlan === 'business' ? 'Turun ke Pro' : $planDetails['pro']['cta']) }}
                        </span>
                        <span wire:loading wire:target="subscribePlan('pro')">
                            <svg class="w-5 h-5 mx-auto animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        </span>
                    </button>
                @endif
                </div>
            </div>

            <!-- Business Plan -->
            <div class="card-artisan relative flex h-full flex-col border-2 border-purple-100 bg-gradient-to-br from-purple-50 to-pink-50 p-5 sm:p-8 {{ $currentPlan === 'business' ? 'ring-2 ring-purple-500' : '' }}">
                @if($currentPlan === 'business')
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-purple-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                        Paket Anda
                    </div>
                @endif
                <div class="mb-6 text-center sm:mb-8">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg shadow-purple-200 sm:h-16 sm:w-16">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-purple-500">{{ $planDetails['business']['subtitle'] }}</p>
                    <h3 class="font-manrope font-extrabold text-xl text-purple-700 mt-2">{{ $planDetails['business']['name'] }}</h3>
                    <p class="mt-2 text-3xl font-manrope font-extrabold text-artisan-primary sm:text-4xl">{{ $planDetails['business']['price_label'] }}</p>
                    <p class="text-sm text-artisan-primary/40 mt-1">{{ ($planDetails['business']['is_published'] ?? true) ? 'per bulan' : 'Segera tersedia' }}</p>
                    <p class="text-sm text-artisan-primary/55 leading-relaxed mt-4">{{ $planDetails['business']['description'] }}</p>
                </div>
                <ul class="mb-8 space-y-3">
                    @foreach($planDetails['business']['features'] as $feature)
                        <li class="flex items-center gap-3 rounded-2xl bg-white/70 px-4 py-3 text-sm text-artisan-primary/70 shadow-sm">
                            <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                <div class="mt-auto">
                @if($currentPlan === 'business')
                    <button disabled class="w-full py-4 bg-purple-100 text-purple-400 font-bold rounded-2xl cursor-not-allowed text-sm">
                        Paket Saat Ini
                    </button>
                @else
                    <button @if(!($planDetails['business']['is_published'] ?? true)) disabled @endif wire:click="subscribePlan('business')" wire:loading.attr="disabled"
                        class="w-full py-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold rounded-2xl shadow-lg shadow-purple-200 hover:shadow-xl hover:shadow-purple-300 transition-all active:scale-[0.98] text-sm disabled:opacity-50">
                        <span wire:loading.remove wire:target="subscribePlan('business')">{{ !($planDetails['business']['is_published'] ?? true) ? 'Coming Soon' : $planDetails['business']['cta'] }}</span>
                        <span wire:loading wire:target="subscribePlan('business')">
                            <svg class="w-5 h-5 mx-auto animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        </span>
                    </button>
                @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top-up Section -->
    <div class="card-artisan border-2 border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-5 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between lg:gap-8">
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-200 sm:h-16 sm:w-16">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-600">{{ $planDetails['topup']['subtitle'] }}</p>
                    <h3 class="font-manrope font-extrabold text-xl text-artisan-primary mt-1">{{ $planDetails['topup']['name'] }} Order</h3>
                    <p class="text-artisan-primary/60 mt-2">{{ $planDetails['topup']['description'] }}</p>
                    <p class="text-sm text-artisan-primary/40 mt-2">Beli kuota <strong>{{ number_format($planDetails['topup']['quota']) }} order</strong> seharga <strong>{{ $planDetails['topup']['price_label'] }}</strong>.</p>
                    @if(auth()->user()->availableQuota() > 0)
                        <p class="text-sm font-bold text-emerald-600 mt-2">
                            Sisa kuota top-up: {{ number_format(auth()->user()->availableQuota()) }} order
                        </p>
                    @endif
                </div>
            </div>
            <div>
                    <button @if(!($planDetails['topup']['is_published'] ?? true)) disabled @endif wire:click="buyQuota" wire:loading.attr="disabled"
                        class="w-full px-6 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-bold rounded-2xl shadow-lg shadow-amber-200 hover:shadow-xl hover:shadow-amber-300 transition-all active:scale-[0.98] text-sm disabled:opacity-50 sm:w-auto sm:px-8 whitespace-nowrap">
                    <span wire:loading.remove wire:target="buyQuota">{{ !($planDetails['topup']['is_published'] ?? true) ? 'Coming Soon' : ($planDetails['topup']['cta'] . ' - ' . $planDetails['topup']['price_label']) }}</span>
                    <span wire:loading wire:target="buyQuota">
                        <svg class="w-5 h-5 mx-auto animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Subscription History -->
    @if(auth()->user()->subscriptions->count() > 0)
        <div class="card-artisan mt-8 p-5 sm:p-8">
            <div class="mb-6 flex items-center gap-3 sm:gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-artisan-primary text-white shadow-artisan sm:h-12 sm:w-12">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold text-artisan-primary">Riwayat Langganan</h3>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest">Histori Paket</p>
                </div>
            </div>
            <div class="space-y-3">
                @foreach(auth()->user()->subscriptions()->latest()->take(10)->get() as $sub)
                    <div class="flex flex-col gap-4 rounded-2xl bg-artisan-surface-low p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                {{ $sub->plan === 'pro' ? 'bg-indigo-100 text-indigo-600' : ($sub->plan === 'business' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600') }}">
                                {{ strtoupper(substr($sub->plan, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-manrope font-bold text-sm text-artisan-primary">{{ ucfirst($sub->plan) }}</p>
                                <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">
                                    {{ $sub->started_at->format('d M Y') }} — {{ $sub->expires_at?->format('d M Y') ?? 'Selamanya' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 sm:gap-4">
                            @if($sub->gateway_transaction_id || $sub->mayar_transaction_id)
                                <button wire:click="showReceipt({{ $sub->id }})" class="text-xs font-bold text-artisan-primary/60 hover:text-artisan-primary transition-colors flex items-center gap-1 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Nota
                                </button>
                            @endif
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] px-3 py-1 rounded-full
                                {{ $sub->isActive() ? 'bg-emerald-100 text-emerald-600' : ($sub->status === 'expired' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600') }}">
                                {{ $sub->isActive() ? 'Aktif' : ($sub->status === 'expired' ? 'Kadaluarsa' : 'Dibatalkan') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Top-up History -->
    @if(auth()->user()->orderQuotas()->whereNotNull('gateway_transaction_id')->orWhereNotNull('mayar_transaction_id')->count() > 0)
        <div class="card-artisan mt-8 p-5 sm:p-8">
            <div class="mb-6 flex items-center gap-3 sm:gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200 sm:h-12 sm:w-12">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold text-artisan-primary">Riwayat Top-up Kuota</h3>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest">Histori Pembelian Kuota</p>
                </div>
            </div>
            <div class="space-y-3">
                @foreach(auth()->user()->orderQuotas()->where(function ($query) { $query->whereNotNull('gateway_transaction_id')->orWhereNotNull('mayar_transaction_id'); })->latest()->take(10)->get() as $quota)
                    <div class="flex flex-col gap-4 rounded-2xl bg-artisan-surface-low p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-100 text-amber-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <p class="font-manrope font-bold text-sm text-artisan-primary">{{ number_format($quota->quota_total) }} Order</p>
                                <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">
                                    {{ $quota->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 sm:gap-4">
                            <button wire:click="showTopupReceipt({{ $quota->id }})" class="text-xs font-bold text-artisan-primary/60 hover:text-artisan-primary transition-colors flex items-center gap-1 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Nota
                            </button>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] px-3 py-1 rounded-full bg-emerald-100 text-emerald-600">
                                Masuk
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Receipt Modal -->
    @if($showReceiptModal && $receiptData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-artisan-primary/50 backdrop-blur-sm" wire:click.self="closeReceipt">
            <div class="bg-white max-w-sm w-full rounded-3xl shadow-2xl overflow-hidden relative animate-bounce-in">
                <!-- Close button -->
                <button wire:click="closeReceipt" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-500 rounded-full hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                
                <div class="p-8 text-center border-b border-dashed border-gray-200 bg-gradient-to-br from-gray-50 to-white">
                    <div class="w-16 h-16 mx-auto bg-artisan-primary text-white rounded-2xl flex items-center justify-center shadow-artisan mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="font-manrope font-extrabold text-2xl text-artisan-primary mb-1">Nota Tagihan</h2>
                    <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-[0.2em]">Semiclon Master Artisan</p>
                </div>
                
                <div class="p-8 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-artisan-primary/60">Tanggal</span>
                        <span class="font-bold text-artisan-primary">{{ $receiptData['date'] }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-artisan-primary/60">No. Transaksi</span>
                        <span class="font-bold text-artisan-primary" title="{{ $receiptData['transaction_id'] }}">{{ substr($receiptData['transaction_id'], 0, 13) }}...</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-artisan-primary/60">Pelanggan</span>
                        <span class="font-bold text-artisan-primary">{{ $receiptData['customer_name'] }}</span>
                    </div>
                    
                    <div class="w-full border-t border-dashed border-gray-200 my-4"></div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-bold text-artisan-primary max-w-[60%] leading-tight">{{ $receiptData['item'] }}</span>
                            <span class="font-bold text-artisan-primary">Rp{{ number_format($receiptData['amount'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="w-full border-t border-dashed border-gray-200 my-4"></div>
                    
                    <div class="flex justify-between items-center bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                        <span class="font-bold text-emerald-800 text-sm uppercase tracking-wider">Total Dibayar</span>
                        <span class="font-manrope font-extrabold text-xl text-emerald-600">Rp{{ number_format($receiptData['amount'], 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-center mt-6">
                        <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-black uppercase tracking-[0.2em] rounded-full border border-emerald-200 shadow-sm flex items-center gap-1">
                            LUNAS
                        </span>
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 text-center border-t border-gray-100">
                    <p class="text-[10px] text-artisan-primary/40 font-medium">Terima kasih atas pembayaran Anda</p>
                </div>
            </div>
        </div>
    @endif
</div>
