<?php

namespace App\Livewire\Customers;

use Livewire\Component;

use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class EditCustomer extends Component
{
    public \App\Models\Customer $customer;
    public $name;
    public $phone;
    public $address;
    public $email;
    public $outlet_id;

    public $availableOutlets = [];

    public function mount(\App\Models\Customer $customer)
    {
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->email = $customer->email;
        $this->outlet_id = $customer->outlet_id;

        if (auth()->user()->isOwner()) {
            $this->availableOutlets = auth()->user()->ownedOutlets;
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers')->ignore($this->customer->id)],
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'outlet_id' => 'required|exists:outlets,id', // Validation logic for permission? usually just exists.
        ]);

        // If admin tries to change outlet_id? 
        // Admin usually shouldn't see outlet_id field.
        // We'll enforce logic: if NOT owner, outlet_id stays same (or force it).
        if (!auth()->user()->isOwner()) {
            $this->outlet_id = $this->customer->outlet_id; // Keep original home branch
        }

        $this->customer->update([
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
        return view('livewire.customers.edit-customer');
    }
}
