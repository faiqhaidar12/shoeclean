<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\ExpensesExport;
use App\Models\Order;
use App\Models\Expense;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected function authorizePlanFeature(string $feature, string $message)
    {
        $user = auth()->user();

        if ($user && $user->hasFeature($feature)) {
            return null;
        }

        if ($user && $user->isOwner()) {
            return redirect()->route('subscription')->with('error', $message);
        }

        abort(403, $message);
    }

    protected function getOutletIds()
    {
        $user = auth()->user();

        return $user ? $user->reportOutletIds() : [];
    }

    // Orders Excel
    public function ordersExcel(Request $request)
    {
        if ($response = $this->authorizePlanFeature('exports', 'Fitur export laporan tersedia mulai paket Pro.')) {
            return $response;
        }

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
        if ($response = $this->authorizePlanFeature('exports', 'Fitur export laporan tersedia mulai paket Pro.')) {
            return $response;
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $outletIds = $this->getOutletIds();

        $orders = Order::whereIn('outlet_id', $outletIds)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['customer', 'outlet'])
            ->latest()
            ->get();

        $totalOrderValue = $orders->sum('total_price');
        $paidOrderValue = $orders->where('payment_status', 'paid')->sum('total_price');
        $waitingConfirmationCount = $orders->where('payment_status', 'waiting_confirmation')->count();
        $unpaidCount = $orders->where('payment_status', 'unpaid')->count();
        $monthName = date('F', mktime(0, 0, 0, $month, 1));

        $pdf = Pdf::loadView('exports.orders-pdf', [
            'orders' => $orders,
            'month' => $monthName,
            'year' => $year,
            'totalOrderValue' => $totalOrderValue,
            'paidOrderValue' => $paidOrderValue,
            'waitingConfirmationCount' => $waitingConfirmationCount,
            'unpaidCount' => $unpaidCount,
        ]);

        return $pdf->download("orders-{$year}-{$month}.pdf");
    }

    // Expenses Excel
    public function expensesExcel(Request $request)
    {
        if ($response = $this->authorizePlanFeature('exports', 'Fitur export laporan tersedia mulai paket Pro.')) {
            return $response;
        }

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
        if ($response = $this->authorizePlanFeature('exports', 'Fitur export laporan tersedia mulai paket Pro.')) {
            return $response;
        }

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
        $outletCount = $expenses->pluck('outlet_id')->filter()->unique()->count();
        $monthName = date('F', mktime(0, 0, 0, $month, 1));

        $pdf = Pdf::loadView('exports.expenses-pdf', [
            'expenses' => $expenses,
            'month' => $monthName,
            'year' => $year,
            'totalExpenses' => $totalExpenses,
            'outletCount' => $outletCount,
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

    // Marketing Kit PDF
    public function marketingKitPdf()
    {
        $planDetails = (new SubscriptionService())->getPlanDetails();

        $pdf = Pdf::loadView('reports.marketing-kit', [
            'planDetails' => $planDetails,
        ])->setPaper('a4');

        return $pdf->download('shoeclean-marketing-kit.pdf');
    }
}
