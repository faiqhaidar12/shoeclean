<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $month;
    public $year;
    public $availableYears = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        
        // Get available years from orders
        $years = Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        // Always include current year
        if (!in_array(now()->year, $years)) {
            array_unshift($years, now()->year);
        }
        
        $this->availableYears = $years;
    }

    public function updated($property)
    {
        if ($property === 'month' || $property === 'year') {
            $this->dispatch('update-chart');
        }
    }

    public function resetFilters()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->dispatch('update-chart');
    }

    public function render()
    {
        $user = auth()->user();
        $isOwner = $user->isOwner();
        
        // Determine outlet scope
        if ($isOwner) {
            $outletIds = $user->ownedOutlets->pluck('id')->toArray();
            // If outlet switcher is active, filter to that outlet
            if (session('current_outlet_id')) {
                $outletIds = [session('current_outlet_id')];
            }
        } else {
            $outletIds = [$user->outlet_id];
        }

        // Stats
        $todayOrders = Order::whereIn('outlet_id', $outletIds)
            ->whereDate('created_at', today())
            ->count();

        // Month Stats (Filtered by selected month/year)
        $monthOrders = Order::whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->count();

        // Optimized: Using JOIN instead of whereHas for better performance
        $todayRevenue = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.outlet_id', $outletIds)
            ->where('payments.status', 'success')
            ->whereDate('payments.created_at', today())
            ->sum('payments.amount');

        // Month Revenue (Filtered by selected month/year) - Optimized with JOIN
        $monthRevenue = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.outlet_id', $outletIds)
            ->where('payments.status', 'success')
            ->whereMonth('payments.created_at', $this->month)
            ->whereYear('payments.created_at', $this->year)
            ->sum('payments.amount');

        $pendingOrders = Order::whereIn('outlet_id', $outletIds)
            ->where('status', 'pending')
            ->count();

        $readyOrders = Order::whereIn('outlet_id', $outletIds)
            ->where('status', 'ready')
            ->count();

        $totalCustomers = Customer::whereIn('outlet_id', $outletIds)->count();

        // Revenue Chart (Daily for selected month)
        $startOfMonth = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Adjust end date if viewing current month (up to today) to avoid empty future dates if preferred, 
        // but for historical it's better to show full month scale or at least filled data.
        // Let's show full month structure for selected month.
        
        // Revenue Chart Data - Optimized with JOIN
        $revenueData = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.outlet_id', $outletIds)
            ->where('payments.status', 'success')
            ->whereBetween('payments.created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(payments.created_at) as date, SUM(payments.amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartData = [];
        
        // Show all days in selected month
        $daysInMonth = $startOfMonth->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = \Carbon\Carbon::createFromDate($this->year, $this->month, $i)->format('Y-m-d');
            $label = $i; // Day number
            
            $chartLabels[] = $label;
            $chartData[] = $revenueData[$date]->total ?? 0;
        }

        // Orders by status
        $statusCounts = Order::whereIn('outlet_id', $outletIds)
            ->when($this->month, fn($q) => $q->whereMonth('created_at', $this->month))
            ->when($this->year, fn($q) => $q->whereYear('created_at', $this->year))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent orders (global latest, not filtered by month usually, but maybe user wants to see history)
        // Let's filter recent orders by selected month too if the user explicitly selected a past month.
        $recentOrders = Order::whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->with(['customer', 'outlet'])
            ->latest()
            ->limit(10)
            ->get();
            
        // Dispatch data for chart update
        $this->dispatch('chart-data-updated', labels: $chartLabels, data: $chartData);

        return view('livewire.dashboard', [
            'isOwner' => $isOwner,
            'todayOrders' => $todayOrders, // Remains today's actual live data
            'monthOrders' => $monthOrders,
            'todayRevenue' => $todayRevenue, // Remains today's actual live data
            'monthRevenue' => $monthRevenue,
            'pendingOrders' => $pendingOrders,
            'readyOrders' => $readyOrders,
            'totalCustomers' => $totalCustomers,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'statusCounts' => $statusCounts,
            'recentOrders' => $recentOrders,
        ]);
    }
}
