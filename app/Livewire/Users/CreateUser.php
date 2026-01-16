<?php

namespace App\Livewire\Users;

use Livewire\Component;

use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules;

#[Layout('layouts.app')]
class CreateUser extends Component
{
    public $name;
    public $email;
    public $password;
    public $role; // slug
    public $outlet_id;

    public $availableRoles = [];
    public $availableOutlets = [];

    public function mount()
    {
        // Setup initial options
        if (auth()->user()->isOwner()) {
            $this->availableOutlets = auth()->user()->ownedOutlets;
            // Owner can create admin or staff.
            $this->availableRoles = \App\Models\Role::whereIn('slug', ['admin', 'staff'])->get();
        } else { // Admin
            $this->availableOutlets = [];
            // Admin can only create staff
            $this->availableRoles = \App\Models\Role::where('slug', 'staff')->get();
            $this->role = 'staff';
            $this->outlet_id = auth()->user()->outlet_id;
        }
    }

    public function save()
    {
        // Validation
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,slug'],
        ];

        // Authorization for Role
        if (!auth()->user()->isOwner() && $this->role !== 'staff') {
            abort(403, 'Unauthorized role assignment');
        }

        // Validate outlet if owner and role is 'admin' or 'staff'? 
        // Admin assignment usually implies they manage that outlet.
        if (auth()->user()->isOwner()) {
            $rules['outlet_id'] = ['nullable', 'exists:outlets,id'];
        }

        $this->validate($rules);

        $user = \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => \Illuminate\Support\Facades\Hash::make($this->password),
            'outlet_id' => $this->outlet_id,
        ]);

        $roleModel = \App\Models\Role::where('slug', $this->role)->first();
        $user->roles()->attach($roleModel);

        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.users.create-user');
    }
}
