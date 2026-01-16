<?php

namespace App\Livewire\Users;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ListUsers extends Component
{
    public $search = '';

    public function delete($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Authorization check
        if (auth()->user()->isOwner()) {
            // Owner can delete anyone except themselves (though they can technically, but let's prevent self-delete)
            if ($user->id === auth()->id()) return;
        } elseif (auth()->user()->isAdmin()) {
            // Admin can only delete staff in their outlet
            if ($user->outlet_id !== auth()->user()->outlet_id || $user->hasRole('owner') || $user->hasRole('admin')) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $user->delete();
    }

    public function render()
    {
        $query = \App\Models\User::query()->with(['roles', 'outlet']);

        if (auth()->user()->isOwner()) {
            // Owner sees all users
        } else {
            // Admin sees only users in their outlet (and maybe not other admins?)
            // Let's restrict to users in same outlet
            $query->where('outlet_id', auth()->user()->outlet_id);
            // And prevent seeing Owner?
            // Usually simpler: owner sees all. admin sees their staff/team.
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->latest()->paginate(12);

        return view('livewire.users.list-users', [
            'users' => $users
        ]);
    }
}
