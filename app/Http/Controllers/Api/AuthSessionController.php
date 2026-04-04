<?php

namespace App\Http\Controllers\Api;

use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthSessionController
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $ownerRole = Role::where('slug', 'owner')->firstOrFail();
            $user->roles()->attach($ownerRole->id);

            $baseSlug = Str::slug($validated['business_name']);
            $slug = $baseSlug ?: 'outlet';
            $counter = 2;

            while (Outlet::where('slug', $slug)->exists()) {
                $slug = ($baseSlug ?: 'outlet').'-'.$counter;
                $counter++;
            }

            $outlet = Outlet::create([
                'owner_id' => $user->id,
                'name' => $validated['business_name'],
                'slug' => $slug,
                'address' => '-',
                'phone' => '-',
                'status' => 'active',
            ]);

            $user->forceFill([
                'outlet_id' => $outlet->id,
            ])->save();

            event(new Registered($user));

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Akun berhasil dibuat.',
            'user' => $this->transformUser($request->user()),
        ], 201);
    }

    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $this->transformUser($request->user()),
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'authenticated' => true,
            'user' => $this->transformUser($request->user()),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    protected function transformUser($user): array
    {
        $user->loadMissing(['roles:id,slug,name', 'outlet:id,name,slug']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => (bool) $user->email_verified_at,
            'roles' => $user->roles->pluck('slug')->values()->all(),
            'primary_role' => $user->roles->pluck('slug')->first(),
            'is_owner' => $user->isOwner(),
            'is_admin' => $user->isAdmin(),
            'is_staff' => $user->isStaff(),
            'is_superadmin' => $user->isSuperAdmin(),
            'current_plan' => $user->currentPlan(),
            'remaining_orders' => $user->remainingOrders(),
            'features' => [
                'team_management' => $user->hasFeature('team_management'),
                'promos' => $user->hasFeature('promos'),
                'exports' => $user->hasFeature('exports'),
                'multi_outlet_reports' => $user->hasFeature('multi_outlet_reports'),
            ],
            'outlet' => $user->outlet
                ? [
                    'id' => $user->outlet->id,
                    'name' => $user->outlet->name,
                    'slug' => $user->outlet->slug,
                ]
                : null,
        ];
    }
}
