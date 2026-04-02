<?php

namespace App\Livewire\SuperAdmin;

use App\Models\PaymentTransaction;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.superadmin')]
class PaymentTransactions extends Component
{
    use WithPagination;

    public $search = '';
    public $kindFilter = '';
    public $statusFilter = '';
    public $selectedOwner = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'kindFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'selectedOwner' => ['except' => ''],
    ];

    public function updated($property)
    {
        if (in_array($property, ['search', 'kindFilter', 'statusFilter', 'selectedOwner'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->kindFilter = '';
        $this->statusFilter = '';
        $this->selectedOwner = '';
        $this->resetPage();
    }

    public function render()
    {
        $ownerOptions = User::whereHas('roles', fn ($q) => $q->where('slug', 'owner'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $transactions = PaymentTransaction::query()
            ->with(['user:id,name,email', 'billable'])
            ->when($this->search, function ($query) {
                $search = trim($this->search);
                $query->where(function ($inner) use ($search) {
                    $inner->where('merchant_order_id', 'like', '%' . $search . '%')
                        ->orWhere('reference', 'like', '%' . $search . '%')
                        ->orWhere('customer_email', 'like', '%' . $search . '%')
                        ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->when($this->kindFilter, fn ($query) => $query->where('kind', $this->kindFilter))
            ->when($this->statusFilter, fn ($query) => $query->where('status_code', $this->statusFilter))
            ->when($this->selectedOwner, fn ($query) => $query->where('user_id', $this->selectedOwner))
            ->latest()
            ->paginate(12);

        $summary = [
            'total' => PaymentTransaction::count(),
            'success' => PaymentTransaction::where('status_code', '00')->count(),
            'pending' => PaymentTransaction::where('status_code', '01')->count(),
            'failed' => PaymentTransaction::whereIn('status_code', ['02', '03', '01'])->where('paid_at', null)->count(),
        ];

        return view('livewire.super-admin.payment-transactions', [
            'ownerOptions' => $ownerOptions,
            'transactions' => $transactions,
            'summary' => $summary,
        ]);
    }
}
