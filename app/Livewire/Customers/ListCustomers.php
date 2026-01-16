<?php

namespace App\Livewire\Customers;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ListCustomers extends Component
{
    public $search = '';

    public function delete($id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        
        // Authorization: Check if user can delete this customer
        $user = auth()->user();
        if (!$user->isOwner() && $customer->outlet_id !== $user->outlet_id) {
            abort(403);
        }
        
        $customer->delete();
    }

    public function render()
    {
        $user = auth()->user();
        $query = \App\Models\Customer::query()->with('outlet');

        // Filter by outlet
        if ($user->isOwner()) {
            // Owner: filter by selected outlet if any
            if (session('current_outlet_id')) {
                $query->where('outlet_id', session('current_outlet_id'));
            }
        } else {
            // Admin/Staff: only see customers in their outlet
            $query->where('outlet_id', $user->outlet_id);
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
