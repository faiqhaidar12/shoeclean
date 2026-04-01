<?php

namespace App\Http\Controllers\Api;

use App\Exports\ExpensesExport;
use App\Exports\OrdersExport;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportManagementController
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->ensureExportsFeature($user);

        [$month, $year] = $this->resolvePeriod($request);
        $outletIds = $user->reportOutletIds();

        $ordersQuery = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);

        $expensesQuery = Expense::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year);

        $successfulPaymentsQuery = Payment::query()
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.outlet_id', $outletIds)
            ->where('payments.status', 'success')
            ->whereMonth('payments.created_at', $month)
            ->whereYear('payments.created_at', $year);

        $orders = (clone $ordersQuery)
            ->with(['outlet:id,name,slug', 'customer:id,name'])
            ->latest()
            ->get();

        $expenses = (clone $expensesQuery)
            ->with(['outlet:id,name,slug', 'user:id,name'])
            ->latest('expense_date')
            ->get();

        $scopedOutlets = Outlet::query()
            ->whereIn('id', $outletIds)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $topOutlets = $scopedOutlets
            ->map(function (Outlet $outlet) use ($orders, $expenses) {
                $outletOrders = $orders->where('outlet_id', $outlet->id);
                $outletExpenses = $expenses->where('outlet_id', $outlet->id);

                return [
                    'id' => $outlet->id,
                    'name' => $outlet->name,
                    'slug' => $outlet->slug,
                    'orders_count' => $outletOrders->count(),
                    'order_value' => (int) $outletOrders->sum('total_price'),
                    'expense_total' => (int) $outletExpenses->sum('amount'),
                ];
            })
            ->sortByDesc('order_value')
            ->values()
            ->take(5)
            ->values();

        $expenseCategories = $expenses
            ->groupBy('category')
            ->map(fn ($items, $category) => [
                'category' => $category,
                'total_amount' => (int) $items->sum('amount'),
                'count' => $items->count(),
            ])
            ->sortByDesc('total_amount')
            ->values()
            ->take(6)
            ->values();

        $recentOrders = $orders
            ->take(6)
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_price' => (int) $order->total_price,
                    'created_at' => optional($order->created_at)->toIso8601String(),
                    'customer' => $order->customer ? [
                        'name' => $order->customer->name,
                    ] : null,
                    'outlet' => $order->outlet ? [
                        'name' => $order->outlet->name,
                        'slug' => $order->outlet->slug,
                    ] : null,
                ];
            })
            ->values();

        $recentExpenses = $expenses
            ->take(6)
            ->map(function (Expense $expense) {
                return [
                    'id' => $expense->id,
                    'category' => $expense->category,
                    'amount' => (int) $expense->amount,
                    'expense_date' => optional($expense->expense_date)->format('Y-m-d'),
                    'outlet' => $expense->outlet ? [
                        'name' => $expense->outlet->name,
                        'slug' => $expense->outlet->slug,
                    ] : null,
                    'user' => $expense->user ? [
                        'name' => $expense->user->name,
                    ] : null,
                ];
            })
            ->values();

        return response()->json([
            'filters' => [
                'month' => $month,
                'year' => $year,
            ],
            'scope' => [
                'outlet_ids' => $outletIds,
                'outlets' => $scopedOutlets->map(fn (Outlet $outlet) => [
                    'id' => $outlet->id,
                    'name' => $outlet->name,
                    'slug' => $outlet->slug,
                ])->values(),
                'label' => count($outletIds) > 1
                    ? 'Semua cabang pada scope aktif'
                    : ($scopedOutlets->first()?->name ?? 'Outlet aktif'),
            ],
            'metrics' => [
                'orders_count' => $orders->count(),
                'gross_order_value' => (int) $orders->sum('total_price'),
                'successful_payment_value' => (int) $successfulPaymentsQuery->sum('payments.amount'),
                'paid_orders_count' => $orders->where('payment_status', 'paid')->count(),
                'waiting_confirmation_count' => $orders->where('payment_status', 'waiting_confirmation')->count(),
                'unpaid_count' => $orders->where('payment_status', 'unpaid')->count(),
                'expenses_total' => (int) $expenses->sum('amount'),
                'net_cashflow' => (int) $successfulPaymentsQuery->sum('payments.amount') - (int) $expenses->sum('amount'),
            ],
            'top_outlets' => $topOutlets,
            'expense_categories' => $expenseCategories,
            'recent_orders' => $recentOrders,
            'recent_expenses' => $recentExpenses,
            'exports' => [
                'orders_excel_url' => url("/api/reports/orders/export?format=excel&month={$month}&year={$year}"),
                'orders_pdf_url' => url("/api/reports/orders/export?format=pdf&month={$month}&year={$year}"),
                'expenses_excel_url' => url("/api/reports/expenses/export?format=excel&month={$month}&year={$year}"),
                'expenses_pdf_url' => url("/api/reports/expenses/export?format=pdf&month={$month}&year={$year}"),
            ],
        ]);
    }

    public function exportOrders(Request $request)
    {
        $user = $request->user();
        $this->ensureExportsFeature($user);

        [$month, $year] = $this->resolvePeriod($request);
        $format = $this->resolveFormat($request);
        $outletIds = $user->reportOutletIds();

        if ($format === 'excel') {
            return Excel::download(
                new OrdersExport($outletIds, $month, $year),
                "orders-{$year}-{$month}.xlsx"
            );
        }

        $orders = Order::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['customer', 'outlet'])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('exports.orders-pdf', [
            'orders' => $orders,
            'month' => date('F', mktime(0, 0, 0, $month, 1)),
            'year' => $year,
            'totalOrderValue' => $orders->sum('total_price'),
            'paidOrderValue' => $orders->where('payment_status', 'paid')->sum('total_price'),
            'waitingConfirmationCount' => $orders->where('payment_status', 'waiting_confirmation')->count(),
            'unpaidCount' => $orders->where('payment_status', 'unpaid')->count(),
        ]);

        return $pdf->download("orders-{$year}-{$month}.pdf");
    }

    public function exportExpenses(Request $request)
    {
        $user = $request->user();
        $this->ensureExportsFeature($user);

        [$month, $year] = $this->resolvePeriod($request);
        $format = $this->resolveFormat($request);
        $outletIds = $user->reportOutletIds();

        if ($format === 'excel') {
            return Excel::download(
                new ExpensesExport($outletIds, $month, $year),
                "expenses-{$year}-{$month}.xlsx"
            );
        }

        $expenses = Expense::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->with(['outlet'])
            ->latest('expense_date')
            ->get();

        $pdf = Pdf::loadView('exports.expenses-pdf', [
            'expenses' => $expenses,
            'month' => date('F', mktime(0, 0, 0, $month, 1)),
            'year' => $year,
            'totalExpenses' => $expenses->sum('amount'),
            'outletCount' => $expenses->pluck('outlet_id')->filter()->unique()->count(),
        ]);

        return $pdf->download("expenses-{$year}-{$month}.pdf");
    }

    protected function ensureExportsFeature($user): void
    {
        abort_unless($user && $user->hasFeature('exports'), 403, 'Fitur export laporan tersedia mulai paket Pro.');
    }

    protected function resolvePeriod(Request $request): array
    {
        $month = max(1, min(12, (int) $request->integer('month', now()->month)));
        $year = max(2020, min(2100, (int) $request->integer('year', now()->year)));

        return [$month, $year];
    }

    protected function resolveFormat(Request $request): string
    {
        $format = strtolower((string) $request->string('format', 'excel'));

        abort_unless(in_array($format, ['excel', 'pdf'], true), 422, 'Format export tidak valid.');

        return $format;
    }
}
