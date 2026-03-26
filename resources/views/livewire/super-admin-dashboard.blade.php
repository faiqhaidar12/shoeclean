<div class="py-8">
    <div class="mb-12">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h1 class="font-manrope font-extrabold text-4xl lg:text-5xl tracking-tight" style="color: var(--sa-primary);">
                    Command Center
                </h1>
                <p class="font-medium mt-2 opacity-50">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan platform ShoeClean.</p>
            </div>
            <a href="{{ route('superadmin.reports.marketing.pdf') }}" class="inline-flex items-center justify-center gap-3 rounded-[1.6rem] px-5 py-4 text-sm font-bold text-white shadow-lg transition-all hover:shadow-xl active:scale-[0.98]" style="background: linear-gradient(135deg, #111827, #374151);">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 17v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 9l5-5 5 5"/></svg>
                Unduh Marketing Kit v2
            </a>
        </div>
    </div>

    <div class="rounded-[2rem] p-8 mb-12" style="background-color: var(--sa-surface-low);">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg" style="color: var(--sa-secondary);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Filter Periode</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Pilih Bulan & Tahun</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <select wire:model.live="month" class="px-5 py-3 bg-white rounded-2xl border-0 shadow-sm font-bold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>

                <select wire:model.live="year" class="px-5 py-3 bg-white rounded-2xl border-0 shadow-sm font-bold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>

                <button wire:click="resetFilters" class="px-6 py-3 bg-white font-bold rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 active:scale-95 flex items-center gap-2 text-sm" style="color: var(--sa-secondary);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-shadow duration-300">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #6366f1, #818cf8); color: white;">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] mb-1 opacity-40">Total Outlet</p>
                    <p class="text-4xl font-manrope font-extrabold" style="color: var(--sa-primary);">{{ $totalOutlets }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">{{ $activeOutlets }} aktif</span>
                        @if($inactiveOutlets > 0)
                            <span class="text-[10px] font-black uppercase tracking-widest text-red-400">{{ $inactiveOutlets }} nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-shadow duration-300">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white;">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] mb-1 opacity-40">Total Owner</p>
                    <p class="text-4xl font-manrope font-extrabold" style="color: var(--sa-primary);">{{ $totalOwners }}</p>
                    <p class="text-[10px] font-black uppercase tracking-widest mt-2 opacity-30">{{ $totalUsers }} total user</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-shadow duration-300">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #ec4899, #f472b6); color: white;">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] mb-1 opacity-40">Total Pelanggan</p>
                    <p class="text-4xl font-manrope font-extrabold" style="color: var(--sa-primary);">{{ $totalCustomers }}</p>
                    <p class="text-[10px] font-black uppercase tracking-widest mt-2 opacity-30">Seluruh platform</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] p-8 shadow-lg text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
            <div class="flex flex-col gap-6">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-lg border border-white/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-white/50 font-black uppercase tracking-[0.2em] mb-1">Total Pesanan</p>
                    <p class="text-4xl font-manrope font-extrabold">{{ number_format($totalOrders) }}</p>
                    <p class="text-[10px] font-black uppercase tracking-widest mt-2 text-white/40">Sepanjang waktu</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="rounded-[2rem] p-8 shadow-lg text-white" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
            <p class="text-xs text-white/50 font-black uppercase tracking-[0.2em] mb-2">Pendapatan Hari Ini</p>
            <p class="text-4xl lg:text-5xl font-manrope font-extrabold">Rp {{ number_format($todayRevenue / 1000, 0) }}K</p>
            <p class="text-[10px] font-black uppercase tracking-widest mt-3 text-white/40">{{ $todayOrders }} pesanan hari ini</p>
        </div>
        <div class="rounded-[2rem] p-8 shadow-lg text-white" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
            <p class="text-xs text-white/50 font-black uppercase tracking-[0.2em] mb-2">Pendapatan {{ date('F', mktime(0, 0, 0, $month, 1)) }}</p>
            <p class="text-4xl lg:text-5xl font-manrope font-extrabold">Rp {{ number_format($monthRevenue / 1000, 0) }}K</p>
            <p class="text-[10px] font-black uppercase tracking-widest mt-3 text-white/40">{{ $monthOrders }} pesanan bulan ini</p>
        </div>
        <div class="bg-white rounded-[2rem] p-8 shadow-sm">
            <p class="text-xs font-black uppercase tracking-[0.2em] mb-2 opacity-40">Total Revenue Platform</p>
            <p class="text-4xl lg:text-5xl font-manrope font-extrabold" style="color: var(--sa-primary);">Rp {{ number_format($totalRevenue / 1000000, 1) }}M</p>
            <p class="text-[10px] font-black uppercase tracking-widest mt-3 opacity-30">Sepanjang waktu</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 mb-12">
        <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
            <div class="flex items-center gap-4 mb-6 md:mb-8">
                <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Tren Revenue Platform</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">6 Bulan Terakhir</p>
                </div>
            </div>
            <div class="relative h-[250px] sm:h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
            <div class="flex items-center gap-4 mb-6 md:mb-8">
                <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Pertumbuhan Outlet</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Outlet Baru / Bulan</p>
                </div>
            </div>
            <div class="relative h-[250px] sm:h-[300px]">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm mb-12">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 md:mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Performa Outlet</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Ranking Berdasarkan Revenue</p>
                </div>
            </div>
            <div class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest" style="background-color: var(--sa-surface-low); color: var(--sa-secondary);">
                {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}
            </div>
        </div>

        @if($outlets->isEmpty())
            <div class="text-center py-12"><p class="font-medium italic opacity-40">Belum ada outlet terdaftar...</p></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2" style="border-color: var(--sa-surface-low);">
                            <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">#</th>
                            <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Outlet</th>
                            <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Owner</th>
                            <th class="text-left text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Status</th>
                            <th class="text-right text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Order (Bln)</th>
                            <th class="text-right text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Revenue (Bln)</th>
                            <th class="text-right text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Total Order</th>
                            <th class="text-right text-[10px] font-black uppercase tracking-[0.2em] opacity-40 py-4 px-4">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outlets as $index => $outlet)
                            <tr class="border-b transition-colors duration-200 hover:bg-indigo-50/50" style="border-color: var(--sa-surface-low);">
                                <td class="py-4 px-4">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black {{ $index < 3 ? 'text-white' : '' }}" style="{{ $index == 0 ? 'background: linear-gradient(135deg, #f59e0b, #d97706);' : ($index == 1 ? 'background: linear-gradient(135deg, #9ca3af, #6b7280);' : ($index == 2 ? 'background: linear-gradient(135deg, #d97706, #b45309);' : 'background-color: var(--sa-surface-low); color: var(--sa-primary);')) }}">{{ $index + 1 }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="font-manrope font-bold text-sm">{{ $outlet->name }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-30">{{ $outlet->city_name ?? $outlet->address ?? '-' }}</p>
                                </td>
                                <td class="py-4 px-4"><p class="font-bold text-sm">{{ $outlet->owner->name ?? '-' }}</p></td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ ($outlet->status ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-500' }}">
                                        {{ ($outlet->status ?? 'active') === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right font-manrope font-extrabold text-sm">{{ number_format($outlet->month_orders) }}</td>
                                <td class="py-4 px-4 text-right font-manrope font-extrabold text-sm" style="color: var(--sa-secondary);">Rp {{ number_format($outlet->month_revenue, 0, ',', '.') }}</td>
                                <td class="py-4 px-4 text-right font-manrope font-extrabold text-sm">{{ number_format($outlet->orders_count) }}</td>
                                <td class="py-4 px-4 text-right font-manrope font-extrabold text-sm" style="color: var(--sa-secondary);">Rp {{ number_format($outlet->revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 md:mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Pesanan Terbaru</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Seluruh Platform</p>
                </div>
            </div>
            <a href="{{ route('superadmin.orders.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-all active:scale-95">
                Buka Order Platform
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="text-center py-12"><p class="font-medium italic opacity-40">Belum ada pesanan...</p></div>
        @else
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-indigo-200 hover:[&::-webkit-scrollbar-thumb]:bg-indigo-400 [&::-webkit-scrollbar-thumb]:rounded-full">
                @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between p-4 rounded-2xl transition-all duration-300 group gap-2 hover:shadow-md" style="background-color: var(--sa-surface-low);">
                        <div class="flex items-center gap-3 sm:gap-4 overflow-hidden">
                            <div class="w-10 h-10 shrink-0 bg-white rounded-xl flex items-center justify-center shadow-sm" style="color: var(--sa-secondary);">
                                <span class="text-xs font-black">{{ substr($order->customer->name ?? '?', 0, 1) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-manrope font-bold text-sm truncate">{{ $order->customer->name ?? '-' }}</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40 truncate">{{ $order->invoice_number }}</p>
                                    <span class="text-[8px] px-2 py-0.5 rounded-full font-black uppercase tracking-widest text-white" style="background-color: var(--sa-secondary);">{{ $order->outlet->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-manrope font-extrabold text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            <div class="mt-0.5">
                                <span class="text-[8px] font-black uppercase tracking-[0.2em] {{ match ($order->status) { 'completed', 'picked_up' => 'text-emerald-500', 'cancelled' => 'text-red-500', 'ready' => 'text-blue-500', default => 'text-orange-500' } }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let revenueChartInstance = null;
        let growthChartInstance = null;

        const initRevenueChart = (labels, data) => {
            const ctx = document.getElementById('revenueChart');
            if (revenueChartInstance) revenueChartInstance.destroy();
            if (!ctx) return;

            revenueChartInstance = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: { labels, datasets: [{ label: 'Revenue', data, borderColor: '#6366f1', backgroundColor: 'rgba(99, 102, 241, 0.1)', tension: 0.4, fill: true, pointRadius: 5, pointBackgroundColor: '#6366f1', pointBorderColor: '#fff', pointBorderWidth: 2, pointHoverRadius: 7 }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    return 'Rp ' + value;
                                },
                                font: { size: 10 },
                                color: '#a5b4fc'
                            },
                            grid: { color: 'rgba(99, 102, 241, 0.08)' }
                        },
                        x: {
                            ticks: { font: { size: 10 }, color: '#a5b4fc' },
                            grid: { display: false }
                        }
                    }
                }
            });
        };

        const initGrowthChart = (labels, data) => {
            const ctx = document.getElementById('growthChart');
            if (growthChartInstance) growthChartInstance.destroy();
            if (!ctx) return;

            growthChartInstance = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Outlet Baru', data, backgroundColor: 'rgba(139, 92, 246, 0.7)', borderColor: '#8b5cf6', borderWidth: 2, borderRadius: 12, borderSkipped: false, barPercentage: 0.5 }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 }, color: '#c4b5fd' }, grid: { color: 'rgba(139, 92, 246, 0.08)' } },
                        x: { ticks: { font: { size: 10 }, color: '#c4b5fd' }, grid: { display: false } }
                    }
                }
            });
        };

        initRevenueChart(@json($chartLabels), @json($chartData));
        initGrowthChart(@json($growthLabels), @json($growthData));

        Livewire.on('chart-data-updated', ({ labels, data, growthLabels, growthData }) => {
            initRevenueChart(labels, data);
            initGrowthChart(growthLabels, growthData);
        });
    });
</script>
