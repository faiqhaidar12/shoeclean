<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\ExpensesExport;
use App\Models\Order;
use App\Models\Expense;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected function getOutletIds()
    {
        $user = auth()->user();
        if ($user->isOwner()) {
            $outletIds = $user->ownedOutlets->pluck('id')->toArray();
            if (session('current_outlet_id')) {
                $outletIds = [session('current_outlet_id')];
            }
        } else {
            $outletIds = [$user->outlet_id];
        }
        return $outletIds;
    }

    // Orders Excel
    public function ordersExcel(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        return Excel::download(
            new OrdersExport($this->getOutletIds(), $month, $year),
            "orders-{$year}-{$month}.xlsx"
        );
    }

    // Orders PDF
    public function ordersPdf(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $outletIds = $this->getOutletIds();

        $orders = Order::whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['customer', 'outlet'])
            ->latest()
            ->get();

        $totalRevenue = $orders->sum('total_price');
        $monthName = date('F', mktime(0, 0, 0, $month, 1));

        $pdf = Pdf::loadView('exports.orders-pdf', [
            'orders' => $orders,
            'month' => $monthName,
            'year' => $year,
            'totalRevenue' => $totalRevenue,
        ]);

        return $pdf->download("orders-{$year}-{$month}.pdf");
    }

    // Expenses Excel
    public function expensesExcel(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        return Excel::download(
            new ExpensesExport($this->getOutletIds(), $month, $year),
            "expenses-{$year}-{$month}.xlsx"
        );
    }

    // Expenses PDF
    public function expensesPdf(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $outletIds = $this->getOutletIds();

        $expenses = Expense::whereIn('outlet_id', $outletIds)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->with(['outlet'])
            ->latest('expense_date')
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $monthName = date('F', mktime(0, 0, 0, $month, 1));

        $pdf = Pdf::loadView('exports.expenses-pdf', [
            'expenses' => $expenses,
            'month' => $monthName,
            'year' => $year,
            'totalExpenses' => $totalExpenses,
        ]);

        return $pdf->download("expenses-{$year}-{$month}.pdf");
    }

    // Print Invoice
    public function printInvoice(Order $order)
    {
        // Authorization
        $user = auth()->user();
        if (!$user->isOwner() && $user->outlet_id !== $order->outlet_id) {
            abort(403);
        }

        $order->load(['customer', 'items.service', 'outlet', 'payments']);

        return view('reports.invoice', compact('order'));
    }
}
