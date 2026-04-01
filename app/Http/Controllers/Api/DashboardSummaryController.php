<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardSummaryController
{
    public function show(Request $request): JsonResponse
    {
        $user = auth()->user();
        $outletIds = $user->reportOutletIds();
        $isOwner = $user->isOwner();
        $today = today();
        $month = max(1, min(12, (int) $request->integer('month', now()->month)));
        $year = max(2020, (int) $request->integer('year', now()->year));

        $scopedOutlets = Outlet::query()
            ->whereIn('id', $outletIds)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
        $ownedOutletCount = $isOwner ? $user->ownedOutlets()->count() : $scopedOutlets->count();
        $isCombinedOutletScope = $isOwner && count($outletIds) > 1;
        $activeScopeLabel = count($outletIds) > 1 ? 'Semua cabang aktif' : ($scopedOutlets->first()?->name ?? 'Outlet aktif');

        $todayOrders = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereDate('created_at', $today)
            ->count();

        $monthOrders = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $pendingOrders = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->where('status', 'pending')
            ->count();

        $readyOrders = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->where('status', 'ready')
            ->count();

        $todayRevenue = Payment::query()
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.outlet_id', $outletIds)
            ->where('payments.status', 'success')
            ->whereDate('payments.created_at', $today)
            ->sum('payments.amount');

        $monthRevenue = Payment::query()
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.outlet_id', $outletIds)
            ->where('payments.status', 'success')
            ->whereMonth('payments.created_at', $month)
            ->whereYear('payments.created_at', $year)
            ->sum('payments.amount');

        $totalCustomers = Customer::query()
            ->whereIn('outlet_id', $outletIds)
            ->count();

        $daysInMonth = now()->setYear($year)->setMonth($month)->startOfMonth()->daysInMonth;
        $startOfMonth = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endOfMonth = (clone $startOfMonth)->endOfMonth();

        $revenueByDay = Payment::query()
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.outlet_id', $outletIds)
            ->where('payments.status', 'success')
            ->whereBetween('payments.created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(payments.created_at) as payment_date, SUM(payments.amount) as total')
            ->groupBy('payment_date')
            ->orderBy('payment_date')
            ->get()
            ->keyBy('payment_date');

        $chartLabels = [];
        $chartData = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = now()->setYear($year)->setMonth($month)->setDay($day)->format('Y-m-d');
            $chartLabels[] = (string) $day;
            $chartData[] = (int) ($revenueByDay[$date]->total ?? 0);
        }

        $topPerformingOutlet = null;

        if ($isCombinedOutletScope) {
            $topPerformingOutlet = Payment::query()
                ->join('orders', 'payments.order_id', '=', 'orders.id')
                ->join('outlets', 'orders.outlet_id', '=', 'outlets.id')
                ->whereIn('orders.outlet_id', $outletIds)
                ->where('payments.status', 'success')
                ->whereMonth('payments.created_at', $month)
                ->whereYear('payments.created_at', $year)
                ->selectRaw('outlets.id, outlets.name, outlets.slug, SUM(payments.amount) as revenue_total, COUNT(DISTINCT orders.id) as orders_total')
                ->groupBy('outlets.id', 'outlets.name', 'outlets.slug')
                ->orderByDesc('revenue_total')
                ->first();
        }

        $outletPerformance = $scopedOutlets->map(function (Outlet $outlet) use ($month, $year) {
            $ordersTotal = Order::query()
                ->where('outlet_id', $outlet->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();

            $pendingTotal = Order::query()
                ->where('outlet_id', $outlet->id)
                ->where('status', 'pending')
                ->count();

            $readyTotal = Order::query()
                ->where('outlet_id', $outlet->id)
                ->where('status', 'ready')
                ->count();

            $revenueTotal = Payment::query()
                ->join('orders', 'payments.order_id', '=', 'orders.id')
                ->where('orders.outlet_id', $outlet->id)
                ->where('payments.status', 'success')
                ->whereMonth('payments.created_at', $month)
                ->whereYear('payments.created_at', $year)
                ->sum('payments.amount');

            return [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
                'orders_total' => $ordersTotal,
                'pending_total' => $pendingTotal,
                'ready_total' => $readyTotal,
                'revenue_total' => (int) $revenueTotal,
            ];
        })->sortByDesc('revenue_total')->values();

        $recentOrders = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['customer:id,name,phone', 'outlet:id,name,slug'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_price' => $order->total_price,
                    'created_at' => optional($order->created_at)->toIso8601String(),
                    'customer' => $order->customer
                        ? [
                            'name' => $order->customer->name,
                            'phone' => $order->customer->phone,
                        ]
                        : null,
                    'outlet' => $order->outlet
                        ? [
                            'name' => $order->outlet->name,
                            'slug' => $order->outlet->slug,
                        ]
                        : null,
                ];
            })
            ->values();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles()->pluck('slug')->values()->all(),
                'current_plan' => $user->currentPlan(),
                'remaining_orders' => $user->remainingOrders(),
                'is_owner' => $isOwner,
                'is_superadmin' => $user->isSuperAdmin(),
            ],
            'scope' => [
                'outlet_ids' => $outletIds,
                'outlets' => $scopedOutlets,
                'active_label' => $activeScopeLabel,
            ],
            'metrics' => [
                'today_orders' => $todayOrders,
                'month_orders' => $monthOrders,
                'today_revenue' => (int) $todayRevenue,
                'month_revenue' => (int) $monthRevenue,
                'pending_orders' => $pendingOrders,
                'ready_orders' => $readyOrders,
                'total_customers' => $totalCustomers,
            ],
            'filters' => [
                'month' => $month,
                'year' => $year,
                'available_years' => Order::query()
                    ->selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year')
                    ->prepend(now()->year)
                    ->unique()
                    ->values(),
            ],
            'multi_outlet' => [
                'owned_outlet_count' => $ownedOutletCount,
                'is_combined_scope' => $isCombinedOutletScope,
                'show_business_upsell' => $isOwner
                    && $user->currentPlan() === 'pro'
                    && $ownedOutletCount > 1
                    && ! $user->canAccessMultiOutletReports(),
                'top_performing_outlet' => $topPerformingOutlet
                    ? [
                        'id' => $topPerformingOutlet->id,
                        'name' => $topPerformingOutlet->name,
                        'slug' => $topPerformingOutlet->slug,
                        'revenue_total' => (int) $topPerformingOutlet->revenue_total,
                        'orders_total' => (int) $topPerformingOutlet->orders_total,
                    ]
                    : null,
                'outlet_performance' => $outletPerformance,
            ],
            'charts' => [
                'revenue' => [
                    'labels' => $chartLabels,
                    'data' => $chartData,
                    'period_label' => $startOfMonth->translatedFormat('F Y'),
                ],
            ],
            'recent_orders' => $recentOrders,
        ]);
    }
}
