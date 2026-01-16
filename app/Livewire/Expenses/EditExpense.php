<?php

namespace App\Livewire\Expenses;

use Livewire\Component;
use App\Models\Expense;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EditExpense extends Component
{
    public Expense $expense;
    
    public $outlet_id;
    public $category = '';
    public $amount;
    public $description = '';
    public $expense_date;

    public function mount(Expense $expense)
    {
        $this->expense = $expense;
        
        // Authorization
        $user = auth()->user();
        if (!$user->isOwner() && $user->outlet_id !== $expense->outlet_id) {
            abort(403);
        }

        $this->outlet_id = $expense->outlet_id;
        $this->category = $expense->category;
        $this->amount = $expense->amount;
        $this->description = $expense->description;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
    }

    public function save()
    {
        $this->validate([
            'category' => 'required|string|max:100',
            'amount' => 'required|integer|min:1',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $this->expense->update([
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'expense_date' => $this->expense_date,
        ]);

        return redirect()->route('expenses.index');
    }

    public function render()
    {
        return view('livewire.expenses.edit-expense', [
            'categories' => ['Bahan Cuci', 'Listrik', 'Air', 'Gaji Karyawan', 'Sewa', 'Peralatan', 'Transportasi', 'Lainnya'],
        ]);
    }
}
