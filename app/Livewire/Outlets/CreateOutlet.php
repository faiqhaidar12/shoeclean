<?php

namespace App\Livewire\Outlets;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateOutlet extends Component
{
    public $name;
    public $address;
    public $phone;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        \App\Models\Outlet::create([
            'owner_id' => auth()->id(),
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => 'active',
        ]);

        return redirect()->route('outlets.index');
    }

    public function render()
    {
        return view('livewire.outlets.create-outlet');
    }
}
