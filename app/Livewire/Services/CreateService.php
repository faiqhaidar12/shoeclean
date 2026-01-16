<?php

namespace App\Livewire\Services;

use Livewire\Component;
use App\Models\Service;
use App\Models\Outlet;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateService extends Component
{
    public $name;
    public $unit = 'pasang'; // default for shoe cleaning
    public $price;
    public $outlet_id;
    
    public $availableOutlets = [];

    public function mount()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $this->availableOutlets = $user->ownedOutlets;
            // Default to session or first outlet
            $this->outlet_id = session('current_outlet_id') ?? $this->availableOutlets->first()?->id;
        } else {
            // Staff -> Assigned Outlet
            $this->outlet_id = $user->outlet_id;
            $this->availableOutlets = $user->outlet ? collect([$user->outlet]) : collect();
            
            if (!$this->outlet_id) {
                abort(403, 'No outlet assigned');
            }
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|in:kg,pcs,pasang,meter',
            'price' => 'required|numeric|min:0',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $user = auth()->user();
        $outlet = Outlet::findOrFail($this->outlet_id);
        
        // Authorization check: ensure user owns the outlet or is assigned to it
        $canAccess = false;
        
        if ($user->isOwner()) {
            // Owner can only create services for their owned outlets
            $canAccess = $user->ownedOutlets->contains('id', $outlet->id);
        } else {
            // Staff can only create services for their assigned outlet
            $canAccess = $user->outlet_id === $outlet->id;
        }
        
        if (!$canAccess) {
            abort(403, 'You do not have permission to create services for this outlet');
        }

        Service::create([
            'outlet_id' => $this->outlet_id,
            'name' => $this->name,
            'unit' => $this->unit,
            'price' => $this->price,
            'status' => 'active',
        ]);

        session()->flash('success', 'Service berhasil ditambahkan!');
        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.create-service');
    }
}
