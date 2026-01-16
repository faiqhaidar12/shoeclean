<?php

namespace App\Livewire\Expenses;

use Livewire\Component;
use App\Models\Expense;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateExpense extends Component
{
    public $outlet_id;
    public $category = '';
    public $amount;
    public $description = '';
    public $expense_date;

    public function mount()
    {
        $user = auth()->user();
        if ($user->isOwner()) {
            $this->outlet_id = session('current_outlet_id') ?? $user->ownedOutlets->first()?->id;
        } else {
            $this->outlet_id = $user->outlet_id;
        }
        $this->expense_date = today()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'category' => 'required|string|max:100',
            'amount' => 'required|integer|min:1',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'outlet_id' => $this->outlet_id,
            'user_id' => auth()->id(),
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'expense_date' => $this->expense_date,
        ]);

        return redirect()->route('expenses.index');
    }

    public function render()
    {
        $user = auth()->user();
        $outlets = $user->isOwner() ? $user->ownedOutlets : collect();

        return view('livewire.expenses.create-expense', [
            'outlets' => $outlets,
            'categories' => ['Bahan Cuci', 'Listrik', 'Air', 'Gaji Karyawan', 'Sewa', 'Peralatan', 'Transportasi', 'Lainnya'],
        ]);
    }
}
