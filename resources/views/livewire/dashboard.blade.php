<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <!-- Filters -->
    <div class="card mb-6 sm:mb-8 border-l-4 border-primary-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-primary-50 text-primary-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Filter Periode</h3>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative min-w-[140px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <select wire:model.live="month"
                        class="pl-9 w-full border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative min-w-[100px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <select wire:model.live="year"
                        class="pl-9 w-full border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <button wire:click="resetFilters"
                    class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Responsive Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        <!-- Today Orders -->
        <div class="card">
            <div class="flex items-center">
                <div class="hidden sm:flex p-2.5 sm:p-3 rounded-xl bg-primary-100 text-primary-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <div class="sm:ml-4">
                    <p class="text-xs sm:text-sm text-gray-500">Orders</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $todayOrders }}</p>
                    <p class="text-xs text-gray-400 hidden sm:block">Hari ini</p>
                </div>
            </div>
        </div>

        <!-- Today Revenue -->
        <div class="card">
            <div class="flex items-center">
                <div class="hidden sm:flex p-2.5 sm:p-3 rounded-xl bg-emerald-100 text-emerald-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div class="sm:ml-4">
                    <p class="text-xs sm:text-sm text-gray-500">Revenue</p>
                    <p class="text-sm sm:text-2xl font-bold text-gray-900">Rp
                        {{ number_format($todayRevenue / 1000, 0) }}K</p>
                    <p class="text-xs text-gray-400 hidden sm:block">Hari ini</p>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="card">
            <div class="flex items-center">
                <div class="hidden sm:flex p-2.5 sm:p-3 rounded-xl bg-amber-100 text-amber-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="sm:ml-4">
                    <p class="text-xs sm:text-sm text-gray-500">Pending</p>
                    <p class="text-lg sm:text-2xl font-bold text-amber-600">{{ $pendingOrders }}</p>
                    <p class="text-xs text-gray-400 hidden sm:block">Menunggu</p>
                </div>
            </div>
        </div>

        <!-- Ready Orders -->
        <div class="card">
            <div class="flex items-center">
                <div class="hidden sm:flex p-2.5 sm:p-3 rounded-xl bg-blue-100 text-blue-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="sm:ml-4">
                    <p class="text-xs sm:text-sm text-gray-500">Ready</p>
                    <p class="text-lg sm:text-2xl font-bold text-blue-600">{{ $readyOrders }}</p>
                    <p class="text-xs text-gray-400 hidden sm:block">Siap ambil</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Stats - Responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        <div class="bg-gradient-to-br from-primary-500 to-purple-600 text-white rounded-xl p-4 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between sm:block">
                <div>
                    <p class="text-primary-200 text-xs sm:text-sm">Orders {{ date('M Y', mktime(0, 0, 0, $month, 1)) }}
                    </p>
                    <p class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">{{ $monthOrders }}</p>
                </div>
                <div class="text-4xl sm:hidden">📦</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-xl p-4 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between sm:block">
                <div>
                    <p class="text-emerald-200 text-xs sm:text-sm">Revenue {{ date('M Y', mktime(0, 0, 0, $month, 1)) }}
                    </p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2">Rp
                        {{ number_format($monthRevenue / 1000, 0) }}K</p>
                </div>
                <div class="text-4xl sm:hidden">💰</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-pink-600 text-white rounded-xl p-4 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between sm:block">
                <div>
                    <p class="text-orange-200 text-xs sm:text-sm">Total Customers</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">{{ $totalCustomers }}</p>
                </div>
                <div class="text-4xl sm:hidden">👥</div>
            </div>
        </div>
    </div>

    <!-- Chart & Recent Orders - Stack on mobile -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Revenue Chart -->
        <div class="card order-2 lg:order-1">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">📈 Revenue
                {{ date('F Y', mktime(0, 0, 0, $month, 1)) }}</h3>
            <div class="relative" style="min-height: 200px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card order-1 lg:order-2">
            <!-- (Recent orders content unchanged) -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">📋 Orders Terbaru</h3>
                <a href="{{ route('orders.index') }}" class="text-primary-600 text-sm hover:underline">Lihat Semua →</a>
            </div>
            @if($recentOrders->isEmpty())
                <div class="text-center py-8">
                    <div class="text-4xl mb-2">📭</div>
                    <p class="text-gray-500">Belum ada order</p>
                </div>
            @else
                <div class="space-y-2 sm:space-y-3 max-h-80 overflow-y-auto">
                    @foreach($recentOrders as $order)
                                {{-- ... same order item ... --}}
                                <a href="{{ route('orders.view', $order->id) }}"
                                    class="block p-3 rounded-lg hover:bg-gray-50 border border-gray-100 transition-colors">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-mono text-xs sm:text-sm text-primary-600 truncate">
                                                {{ $order->invoice_number }}</p>
                                            <p class="text-xs sm:text-sm text-gray-600 truncate">{{ $order->customer->name }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="badge {{ match ($order->status) {
                            'completed', 'picked_up' => 'badge-success',
                            'cancelled' => 'badge-danger',
                            'ready' => 'badge-info',
                            default => 'badge-warning'
                        } }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            <p class="text-xs sm:text-sm font-bold text-gray-900 mt-1">Rp
                                                {{ number_format($order->total_price, 0, ',', '.') }}</p>
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
        <div class="card">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">📥 Export Reports</h3>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Periode: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</span>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">Export akan menggunakan filter periode yang dipilih di atas.</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
                <a href="{{ route('reports.orders.excel', ['month' => $month, 'year' => $year]) }}"
                    class="flex items-center justify-center gap-2 px-3 py-2.5 sm:py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">Orders</span> Excel
                </a>
                <a href="{{ route('reports.orders.pdf', ['month' => $month, 'year' => $year]) }}"
                    class="flex items-center justify-center gap-2 px-3 py-2.5 sm:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="hidden sm:inline">Orders</span> PDF
                </a>
                <a href="{{ route('reports.expenses.excel', ['month' => $month, 'year' => $year]) }}"
                    class="flex items-center justify-center gap-2 px-3 py-2.5 sm:py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">Expenses</span> Excel
                </a>
                <a href="{{ route('reports.expenses.pdf', ['month' => $month, 'year' => $year]) }}"
                    class="flex items-center justify-center gap-2 px-3 py-2.5 sm:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="hidden sm:inline">Expenses</span> PDF
                </a>
            </div>
        </div>
    @endif
</div>

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
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(99, 102, 241)'
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