<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Order;
use App\Models\Outlet;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.superadmin')]
class ListOrders extends Component
{
    use WithPagination;

    public $search = '';
    public $orderStatus = '';
    public $paymentStatus = '';
    public $selectedOutlet = '';
    public $selectedOwner = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'orderStatus' => ['except' => ''],
        'paymentStatus' => ['except' => ''],
        'selectedOutlet' => ['except' => ''],
        'selectedOwner' => ['except' => ''],
    ];

    public function updated($property)
    {
        if (in_array($property, ['search', 'orderStatus', 'paymentStatus', 'selectedOutlet', 'selectedOwner'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->orderStatus = '';
        $this->paymentStatus = '';
        $this->selectedOutlet = '';
        $this->selectedOwner = '';
        $this->resetPage();
    }

    public function render()
    {
        $ownerOptions = User::whereHas('roles', fn($q) => $q->where('slug', 'owner'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $outletOptions = Outlet::orderBy('name')->get(['id', 'name']);

        $orders = Order::query()
            ->with(['customer', 'outlet.owner'])
            ->when($this->search, function ($query) {
                $search = trim($this->search);
                $query->where(function ($inner) use ($search) {
                    $inner->where('invoice_number', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->when($this->orderStatus, fn($query) => $query->where('status', $this->orderStatus))
            ->when($this->paymentStatus, fn($query) => $query->where('payment_status', $this->paymentStatus))
            ->when($this->selectedOutlet, fn($query) => $query->where('outlet_id', $this->selectedOutlet))
            ->when($this->selectedOwner, fn($query) => $query->whereHas('outlet', fn($q) => $q->where('owner_id', $this->selectedOwner)))
            ->latest()
            ->paginate(12);

        return view('livewire.super-admin.list-orders', [
            'orders' => $orders,
            'ownerOptions' => $ownerOptions,
            'outletOptions' => $outletOptions,
        ]);
    }
}
