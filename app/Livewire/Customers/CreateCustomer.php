<?php

namespace App\Livewire\Customers;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateCustomer extends Component
{
    public $name;
    public $phone;
    public $address;
    public $email;
    public $outlet_id;

    public $availableOutlets = [];

    public function mount()
    {
        if (auth()->user()->isOwner()) {
            $this->availableOutlets = auth()->user()->ownedOutlets;
            $this->outlet_id = session('current_outlet_id') ?? $this->availableOutlets->first()?->id;
        } else {
            // Admin/Staff -> Assigned Outlet
            $this->outlet_id = auth()->user()->outlet_id;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        \App\Models\Customer::create([
            'outlet_id' => $this->outlet_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
        ]);

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.create-customer');
    }
}
