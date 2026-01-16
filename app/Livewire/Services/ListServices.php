<?php

namespace App\Livewire\Services;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ListServices extends Component
{
    public function delete($id)
    {
        $service = \App\Models\Service::findOrFail($id);
        // Add authorization check
        $service->delete();
    }

    public function render()
    {
        $outletId = session('current_outlet_id');
        
        $services = \App\Models\Service::query()
            ->when($outletId, function ($query) use ($outletId) {
                return $query->where('outlet_id', $outletId);
            })
            ->when(!$outletId && auth()->user()->isOwner(), function($query) {
                // If owner and no outlet selected, show all their outlets' services? or prompt?
                // Let's show all for now.
                return $query->whereIn('outlet_id', auth()->user()->ownedOutlets->pluck('id'));
            })
            ->with('outlet')
            ->latest()
            ->paginate(12);

        return view('livewire.services.list-services', [
            'services' => $services
        ]);
    }
}
