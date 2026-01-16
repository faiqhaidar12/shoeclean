<?php

namespace App\Livewire\Expenses;

use Livewire\Component;
use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListExpenses extends Component
{
    use WithPagination;

    public $month;
    public $year;
    public $availableYears = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        
        // Get available years from expenses
        $years = Expense::selectRaw('YEAR(expense_date) as year')
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

    public function delete($id)
    {
        $expense = Expense::findOrFail($id);
        
        // Authorization
        $user = auth()->user();
        if (!$user->isOwner() && $user->outlet_id !== $expense->outlet_id) {
            abort(403);
        }

        $expense->delete();
    }

    public function render()
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

        // Optimized: Use base query with clone to avoid duplicate WHERE conditions
        $baseQuery = Expense::whereIn('outlet_id', $outletIds)
            ->whereMonth('expense_date', $this->month)
            ->whereYear('expense_date', $this->year);

        // Clone for sum calculation (lightweight query)
        $totalExpenses = (clone $baseQuery)->sum('amount');

        // Main query with eager loading and pagination
        $expenses = $baseQuery
            ->with(['outlet', 'user'])
            ->latest('expense_date')
            ->paginate(15);

        return view('livewire.expenses.list-expenses', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
        ]);
    }
}
