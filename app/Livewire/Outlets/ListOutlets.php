<?php

namespace App\Livewire\Outlets;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ListOutlets extends Component
{
    public function delete($id)
    {
        $outlet = \App\Models\Outlet::where('owner_id', auth()->id())->findOrFail($id);
        $outlet->delete();
    }

    public function render()
    {
        return view('livewire.outlets.list-outlets', [
            'outlets' => auth()->user()->ownedOutlets()->latest()->paginate(12)
        ]);
    }
}
