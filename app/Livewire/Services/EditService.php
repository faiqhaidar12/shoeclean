<?php

namespace App\Livewire\Services;

use Livewire\Component;
use App\Models\Service;
use App\Models\Outlet;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EditService extends Component
{
    public Service $service;
    public $name;
    public $unit;
    public $price;
    public $status;
    public $outlet_id;
    
    public $availableOutlets = [];

    public function mount(Service $service)
    {
        $user = auth()->user();
        $outlet = $service->outlet;
        
        // Authorization check
        $canAccess = false;
        
        if ($user->isOwner()) {
            $canAccess = $user->ownedOutlets->contains('id', $outlet->id);
            $this->availableOutlets = $user->ownedOutlets;
        } else {
            $canAccess = $user->outlet_id === $outlet->id;
            $this->availableOutlets = $user->outlet ? collect([$user->outlet]) : collect();
        }
        
        if (!$canAccess) {
            abort(403, 'You do not have permission to edit this service');
        }

        $this->service = $service;
        $this->name = $service->name;
        $this->unit = $service->unit;
        $this->price = $service->price;
        $this->status = $service->status;
        $this->outlet_id = $service->outlet_id;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|in:kg,pcs,pasang,meter',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $user = auth()->user();
        $outlet = Outlet::findOrFail($this->outlet_id);
        
        // Authorization check for new outlet
        $canAccess = false;
        
        if ($user->isOwner()) {
            $canAccess = $user->ownedOutlets->contains('id', $outlet->id);
        } else {
            $canAccess = $user->outlet_id === $outlet->id;
        }
        
        if (!$canAccess) {
            abort(403, 'You do not have permission to move service to this outlet');
        }

        $this->service->update([
            'outlet_id' => $this->outlet_id,
            'name' => $this->name,
            'unit' => $this->unit,
            'price' => $this->price,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Service berhasil diupdate!');
        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.edit-service');
    }
}
