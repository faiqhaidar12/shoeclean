<?php

namespace App\Livewire\Users;

use Livewire\Component;

use Livewire\Attributes\Layout;

use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListUsers extends Component
{
    use WithPagination;

    public $search = '';

    public function mount()
    {
        if (!auth()->user()->hasFeature('team_management')) {
            session()->flash('error', 'Kelola admin dan staff tersedia mulai paket Pro.');
            $this->redirectRoute(auth()->user()->isOwner() ? 'subscription' : 'dashboard', navigate: true);
            return;
        }
    }

    public function delete($id)
    {
        if (!auth()->user()->hasFeature('team_management')) {
            abort(403, 'Kelola admin dan staff tersedia mulai paket Pro.');
        }

        $user = \App\Models\User::findOrFail($id);
        
        // Authorization check
        if (auth()->user()->isOwner()) {
            // Owner can delete anyone in their outlets except themselves
            if ($user->id === auth()->id()) return;
            // Verify user belongs to owner's outlets
            if (!auth()->user()->ownedOutlets->contains('id', $user->outlet_id)) {
                abort(403);
            }
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
        if (!auth()->user()->hasFeature('team_management')) {
            abort(403, 'Kelola admin dan staff tersedia mulai paket Pro.');
        }

        $query = \App\Models\User::query()->with(['roles', 'outlet']);

        if (auth()->user()->isOwner()) {
            // Owner sees only users in their owned outlets + themselves
            $ownedOutletIds = auth()->user()->ownedOutlets->pluck('id');
            $query->where(function($q) use ($ownedOutletIds) {
                $q->whereIn('outlet_id', $ownedOutletIds)
                  ->orWhere('id', auth()->id());
            });
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
