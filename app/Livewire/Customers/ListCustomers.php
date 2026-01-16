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
        // Authorization? Global customers usually deletable by Admin/Owner.
        $customer->delete();
    }

    public function render()
    {
        $query = \App\Models\Customer::query()->with('outlet');

        if ($this->search) {
            $query->search($this->search);
        }

        $customers = $query->latest()->paginate(10);

        return view('livewire.customers.list-customers', [
            'customers' => $customers
        ]);
    }
}
