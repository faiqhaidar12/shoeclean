<?php

namespace App\Livewire\Services;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateService extends Component
{
    public $name;
    public $unit = 'kg'; // default
    public $price;
    public $outlet_id;
    
    public $availableOutlets = [];

    public function mount()
    {
        if (auth()->user()->isOwner()) {
            $this->availableOutlets = auth()->user()->ownedOutlets;
            // Default to session or first outlet
            $this->outlet_id = session('current_outlet_id') ?? $this->availableOutlets->first()?->id;
        } else {
            // Admin/Staff -> Assigned Outlet
            $this->outlet_id = auth()->user()->outlet_id;
            if (!$this->outlet_id) {
                abort(403, 'No outlet assigned');
            }
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|in:kg,pcs,meter',
            'price' => 'required|numeric|min:0',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        // Authorization check: ensure user owns the outlet or belongs to it
        $outlet = \App\Models\Outlet::findOrFail($this->outlet_id);
        if ($outlet->owner_id !== auth()->id() && auth()->user()->outlet_id !== $outlet->id) {
             abort(403);
        }

        \App\Models\Service::create([
            'outlet_id' => $this->outlet_id,
            'name' => $this->name,
            'unit' => $this->unit,
            'price' => $this->price,
            'status' => 'active',
        ]);

        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.create-service');
    }
}
