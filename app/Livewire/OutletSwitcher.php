<?php

namespace App\Livewire;

use Livewire\Component;

class OutletSwitcher extends Component
{
    public $currentOutletId;

    public function mount()
    {
        $this->currentOutletId = session('current_outlet_id');
    }

    public function switchOutlet($outletId)
    {
        if ($outletId === 'all') {
            session()->forget('current_outlet_id');
            $this->currentOutletId = null;
        } else {
            // Verify ownership/access
            $outlet = \App\Models\Outlet::find($outletId);
            if ($outlet && ($outlet->owner_id === auth()->id() || auth()->user()->outlet_id === $outlet->id)) {
                session(['current_outlet_id' => $outletId]);
                $this->currentOutletId = $outletId;
            }
        }

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $outlets = auth()->user()->ownedOutlets;
        // If staff/admin, they might only see their assigned outlet? 
        // For now owner sees all.
        
        return view('livewire.outlet-switcher', [
            'outlets' => $outlets
        ]);
    }
}
