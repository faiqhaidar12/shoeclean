<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.superadmin')]
class SuperAdminDashboard extends Component
{
    public $month;
    public $year;
    public $availableYears = [];

    public function mount()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $this->month = now()->month;
        $this->year = now()->year;

        $years = Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (!in_array(now()->year, $years, true)) {
            array_unshift($years, now()->year);
        }

        $this->availableYears = $years;
    }

    public function updated($property)
    {
        if (in_array($property, ['month', 'year'], true)) {
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
        $totalOutlets = Outlet::count();
        $activeOutlets = Outlet::where('status', 'active')->count();
        $inactiveOutlets = $totalOutlets - $activeOutlets;
        $totalOwners = User::whereHas('roles', fn($q) => $q->where('slug', 'owner'))->count();
        $totalUsers = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['owner', 'admin', 'staff']))->count();
        $totalCustomers = Customer::count();
        $totalServices = Service::count();

        $todayOrders = Order::whereDate('created_at', today())->count();
        $monthOrders = Order::whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->count();
        $totalOrders = Order::count();

        $todayRevenue = Payment::where('status', 'success')
            ->whereDate('created_at', today())
            ->sum('amount');

        $monthRevenue = Payment::where('status', 'success')
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->sum('amount');

        $totalRevenue = Payment::where('status', 'success')->sum('amount');

        $outlets = Outlet::withCount(['orders'])
            ->with(['owner:id,name'])
            ->get()
            ->map(function ($outlet) {
                $outlet->revenue = Payment::where('status', 'success')
                    ->whereHas('order', fn($q) => $q->where('outlet_id', $outlet->id))
                    ->sum('amount');

                $outlet->month_orders = Order::where('outlet_id', $outlet->id)
                    ->whereMonth('created_at', $this->month)
                    ->whereYear('created_at', $this->year)
                    ->count();

                $outlet->month_revenue = Payment::where('status', 'success')
                    ->whereHas('order', fn($q) => $q->where('outlet_id', $outlet->id))
                    ->whereMonth('created_at', $this->month)
                    ->whereYear('created_at', $this->year)
                    ->sum('amount');

                return $outlet;
            })
            ->sortByDesc('revenue');

        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->format('M Y');
            $chartData[] = Payment::where('status', 'success')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount');
        }

        $growthLabels = [];
        $growthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $growthLabels[] = $date->format('M Y');
            $growthData[] = Outlet::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        $recentOrders = Order::with(['customer', 'outlet'])
            ->latest()
            ->limit(10)
            ->get();

        $this->dispatch(
            'chart-data-updated',
            labels: $chartLabels,
            data: $chartData,
            growthLabels: $growthLabels,
            growthData: $growthData
        );

        return view('livewire.super-admin-dashboard', [
            'totalOutlets' => $totalOutlets,
            'activeOutlets' => $activeOutlets,
            'inactiveOutlets' => $inactiveOutlets,
            'totalOwners' => $totalOwners,
            'totalUsers' => $totalUsers,
            'totalCustomers' => $totalCustomers,
            'totalServices' => $totalServices,
            'todayOrders' => $todayOrders,
            'monthOrders' => $monthOrders,
            'totalOrders' => $totalOrders,
            'todayRevenue' => $todayRevenue,
            'monthRevenue' => $monthRevenue,
            'totalRevenue' => $totalRevenue,
            'outlets' => $outlets,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'growthLabels' => $growthLabels,
            'growthData' => $growthData,
            'recentOrders' => $recentOrders,
        ]);
    }
}
