<?php

namespace App\Livewire\Services;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EditService extends Component
{
    public \App\Models\Service $service;
    public $name;
    public $unit;
    public $price;
    public $status;

    public function mount(\App\Models\Service $service)
    {
        // Authorization check
        $outlet = $service->outlet;
        // Allow if owner owns the outlet OR if user is admin/staff assigned to this outlet
        if ($outlet->owner_id !== auth()->id() && auth()->user()->outlet_id !== $outlet->id) {
             abort(403);
        }

        $this->service = $service;
        $this->name = $service->name;
        $this->unit = $service->unit;
        $this->price = $service->price;
        $this->status = $service->status;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|in:kg,pcs,meter',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $this->service->update([
            'name' => $this->name,
            'unit' => $this->unit,
            'price' => $this->price,
            'status' => $this->status,
        ]);

        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.edit-service');
    }
}
