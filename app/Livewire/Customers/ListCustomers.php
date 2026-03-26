<?php

namespace App\Livewire\Customers;

use Livewire\Component;

use Livewire\Attributes\Layout;

use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListCustomers extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedOutletId = null;
    public $outlets = [];

    public function mount()
    {
        $this->outlets = \App\Models\Outlet::whereIn('id', auth()->user()->allOutletIds())->get();
    }

    public function selectOutlet($id)
    {
        $this->selectedOutletId = $this->selectedOutletId == $id ? null : $id;
    }

    public function delete($id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        
        // Authorization: Check if user can delete this customer
        $user = auth()->user();
        if ($user->isOwner()) {
            if (!$user->ownedOutlets->contains('id', $customer->outlet_id)) {
                abort(403);
            }
        } elseif ($customer->outlet_id !== $user->outlet_id) {
            abort(403);
        }
        
        $customer->delete();
    }

    public function render()
    {
        $user = auth()->user();
        $query = \App\Models\Customer::query()->with('outlet');

        // Filter by outlet (Scope to all outlets in the business)
        if ($this->selectedOutletId) {
            $query->where('outlet_id', $this->selectedOutletId);
        } else {
            $query->whereIn('outlet_id', $user->allOutletIds());
        }

        // For Owners, if a specific outlet is selected in session (global switcher), filter further
        // but only if we haven't manually selected an outlet card on this page.
        if (!$this->selectedOutletId && $user->isOwner() && session('current_outlet_id')) {
            $query->where('outlet_id', session('current_outlet_id'));
        }

        if ($this->search) {
            $query->search($this->search);
        }

        $customers = $query->latest()->paginate(10);

        return view('livewire.customers.list-customers', [
            'customers' => $customers
        ]);
    }
}
