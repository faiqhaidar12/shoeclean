<?php

namespace App\Livewire\Outlets;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EditOutlet extends Component
{
    public \App\Models\Outlet $outlet;
    public $name;
    public $address;
    public $phone;
    public $status;
    public $search = '';
    public $searchResults = [];

    public function mount(\App\Models\Outlet $outlet)
    {
        // Ensure user owns this outlet
        if ($outlet->owner_id !== auth()->id()) {
            abort(403);
        }

        $this->outlet = $outlet;
        $this->name = $outlet->name;
        $this->address = $outlet->address;
        $this->phone = $outlet->phone;
        $this->status = $outlet->status;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = \App\Models\User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->take(5)
            ->get();
    }

    public function assignAdmin($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        // Remove existing admin role if any? Or just add?
        // Let's ensure user has admin role
        $adminRole = \App\Models\Role::where('slug', 'admin')->first();
        if (!$user->roles->contains($adminRole->id)) {
            $user->roles()->attach($adminRole);
        }

        // Update outlet_id
        $user->update(['outlet_id' => $this->outlet->id]);

        $this->mount($this->outlet); // Refresh
        $this->search = '';
        $this->searchResults = [];
    }

    public function removeAdmin($userId)
    {
        $user = \App\Models\User::where('outlet_id', $this->outlet->id)->findOrFail($userId);
        $user->update(['outlet_id' => null]);
        
        // Optionally remove admin role? Maybe safer to keep it.
        
        $this->mount($this->outlet); // Refresh
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $this->outlet->update([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => $this->status,
        ]);

        return redirect()->route('outlets.index');
    }

    public function render()
    {
        return view('livewire.outlets.edit-outlet');
    }
}
