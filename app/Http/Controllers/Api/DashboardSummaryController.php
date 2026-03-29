<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class DashboardSummaryController
{
    public function show(): JsonResponse
    {
        $user = auth()->user();
        $outletIds = $user->reportOutletIds();
        $isOwner = $user->isOwner();
        $today = today();
        $month = now()->month;
        $year = now()->year;

        $scopedOutlets = Outlet::query()
            ->whereIn('id', $outletIds)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

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

        $recentOrders = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->with(['customer:id,name,phone', 'outlet:id,name,slug'])
            ->latest()
            ->limit(6)
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
                'active_label' => count($outletIds) > 1 ? 'Semua cabang aktif' : ($scopedOutlets->first()?->name ?? 'Outlet aktif'),
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
            'recent_orders' => $recentOrders,
        ]);
    }
}
