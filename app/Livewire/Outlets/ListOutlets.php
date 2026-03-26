<?php

namespace App\Livewire\Outlets;

use Livewire\Component;

use Livewire\Attributes\Layout;

use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListOutlets extends Component
{
    use WithPagination;

    public function delete($id)
    {
        $outlet = \App\Models\Outlet::where('owner_id', auth()->id())->findOrFail($id);
        $outlet->delete();
    }

    public function render()
    {
        return view('livewire.outlets.list-outlets', [
            'outlets' => auth()->user()->ownedOutlets()->withCount('users')->latest()->paginate(12)
        ]);
    }
}
