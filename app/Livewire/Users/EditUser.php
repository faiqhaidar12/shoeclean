<?php

namespace App\Livewire\Users;

use Livewire\Component;

use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class EditUser extends Component
{
    public \App\Models\User $user;
    public $name;
    public $email;
    public $password; // optional
    public $role; // slug
    public $outlet_id;

    public $availableRoles = [];
    public $availableOutlets = [];

    public function mount(\App\Models\User $user)
    {
        // Authorization
        if (auth()->user()->isOwner()) {
            $this->availableOutlets = auth()->user()->ownedOutlets;
            $this->availableRoles = \App\Models\Role::whereIn('slug', ['admin', 'staff'])->get();
        } elseif (auth()->user()->isAdmin()) {
            // Admin can only edit staff in their outlet.
            // Also cannot edit themselves here? (Profile handles self-edit usually)
            if ($user->outlet_id !== auth()->user()->outlet_id || $user->hasRole('owner') || $user->hasRole('admin')) {
                // Allow admin to edit staff in their outlet.
                // If user is admin (themself), maybe specific fields? 
                // Let's assume Profile Edit handles self-edit. Here is "User Management".
                // So block editing self or other admins/owners.
                if ($user->id === auth()->id()) {
                     // Maybe redirect to profile? Or allow? user management usually implies managing OTHERS.
                }
                if ($user->hasRole('owner') || ($user->hasRole('admin') && $user->id !== auth()->id())) {
                    abort(403);
                }
            }
            $this->availableOutlets = [];
            $this->availableRoles = \App\Models\Role::where('slug', 'staff')->get();
        } else {
            abort(403);
        }

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles->first()?->slug;
        $this->outlet_id = $user->outlet_id;
    }

    public function update()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'role' => ['required', 'exists:roles,slug'],
        ];

        if ($this->password) {
            $rules['password'] = ['required', 'string', Rules\Password::defaults()];
        }

        if (auth()->user()->isOwner()) {
            $rules['outlet_id'] = ['nullable', 'exists:outlets,id'];
        }

        // Validate Role assignment auth
        if (!auth()->user()->isOwner() && $this->role !== 'staff') {
            abort(403, 'Unauthorized role assignment');
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];
        
        if ($this->password) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        if (auth()->user()->isOwner()) {
             $data['outlet_id'] = $this->outlet_id;
        }

        $this->user->update($data);

        // Update Role
        $roleModel = \App\Models\Role::where('slug', $this->role)->first();
        if (!$this->user->roles->contains($roleModel->id)) {
            $this->user->roles()->sync([$roleModel->id]);
        }

        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.users.edit-user');
    }
}
