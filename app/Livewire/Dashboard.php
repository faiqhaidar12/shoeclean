<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Feedback;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $month;
    public $year;
    public $availableYears = [];

    // Feedback form
    public $feedbackCategory = 'saran';
    public $feedbackMessage = '';
    public $feedbackSent = false;

    // Survey modal
    public $showSurveyModal = false;
    public $pendingSurvey = null;

    public function mount()
    {
        // Redirect super admin to their own dashboard
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

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

        // Check for pending platform survey
        $this->checkPendingSurvey();
    }

    protected function checkPendingSurvey(): void
    {
        // Skip if already dismissed this session
        if (session('survey_modal_dismissed')) {
            return;
        }

        $user = auth()->user();
        $activeSurvey = Survey::platform()->active()->latest()->first();

        if (!$activeSurvey) {
            return;
        }

        // Check if user filled any platform survey in last 30 days
        $recentResponse = SurveyResponse::whereHas('survey', function ($q) {
                $q->where('type', 'platform');
            })
            ->where(function ($q) use ($user) {
                $q->where('respondent_name', $user->name)
                  ->orWhere('respondent_phone', $user->email);
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();

        if (!$recentResponse) {
            $this->showSurveyModal = true;
            $this->pendingSurvey = $activeSurvey;
        }
    }

    public function dismissSurveyModal(): void
    {
        $this->showSurveyModal = false;
        session(['survey_modal_dismissed' => true]);
    }

    public function submitFeedback(): void
    {
        $this->validate([
            'feedbackCategory' => 'required|in:keluhan,ide,saran',
            'feedbackMessage' => 'required|min:10|max:2000',
        ], [
            'feedbackMessage.required' => 'Pesan tidak boleh kosong.',
            'feedbackMessage.min' => 'Pesan minimal 10 karakter.',
        ]);

        $user = auth()->user();

        Feedback::create([
            'user_id' => $user->id,
            'outlet_id' => $user->outlet_id,
            'category' => $this->feedbackCategory,
            'message' => $this->feedbackMessage,
        ]);

        $this->feedbackMessage = '';
        $this->feedbackCategory = 'saran';
        $this->feedbackSent = true;
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
        $outletIds = $user->reportOutletIds();
        $ownedOutletCount = $isOwner ? $user->ownedOutlets()->count() : 1;
        $isCombinedOutletScope = $isOwner && count($outletIds) > 1;
        $scopedOutlets = Outlet::whereIn('id', $outletIds)
            ->orderBy('name')
            ->get(['id', 'name']);
        $activeScopeLabel = $isCombinedOutletScope
            ? 'Semua cabang aktif'
            : ($scopedOutlets->first()?->name ?? 'Outlet aktif');

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

        // Subscription info
        $subscriptionService = new SubscriptionService();
        $orderLimitInfo = $subscriptionService->checkOrderLimit($user);
        $currentPlan = $user->currentPlan();
        $showBusinessUpsell = $isOwner
            && $currentPlan === 'pro'
            && $ownedOutletCount > 1
            && !$user->canAccessMultiOutletReports();
        $topPerformingOutlet = null;

        if ($isCombinedOutletScope) {
            $topPerformingOutlet = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
                ->join('outlets', 'orders.outlet_id', '=', 'outlets.id')
                ->whereIn('orders.outlet_id', $outletIds)
                ->where('payments.status', 'success')
                ->whereMonth('payments.created_at', $this->month)
                ->whereYear('payments.created_at', $this->year)
                ->selectRaw('outlets.name, SUM(payments.amount) as revenue_total, COUNT(DISTINCT orders.id) as orders_total')
                ->groupBy('outlets.id', 'outlets.name')
                ->orderByDesc('revenue_total')
                ->first();
        }

        return view('livewire.dashboard', [
            'isOwner' => $isOwner,
            'todayOrders' => $todayOrders,
            'monthOrders' => $monthOrders,
            'todayRevenue' => $todayRevenue,
            'monthRevenue' => $monthRevenue,
            'pendingOrders' => $pendingOrders,
            'readyOrders' => $readyOrders,
            'totalCustomers' => $totalCustomers,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'statusCounts' => $statusCounts,
            'recentOrders' => $recentOrders,
            'orderLimitInfo' => $orderLimitInfo,
            'currentPlan' => $currentPlan,
            'ownedOutletCount' => $ownedOutletCount,
            'isCombinedOutletScope' => $isCombinedOutletScope,
            'showBusinessUpsell' => $showBusinessUpsell,
            'activeScopeLabel' => $activeScopeLabel,
            'scopedOutlets' => $scopedOutlets,
            'topPerformingOutlet' => $topPerformingOutlet,
        ]);
    }
}
