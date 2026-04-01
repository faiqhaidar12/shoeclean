<?php

namespace App\Http\Controllers\Api;

use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController
{
    public function index(Request $request): JsonResponse
    {
        $actor = $request->user();
        $this->ensureFeatureAccess($actor);

        $query = User::query()->with(['roles:id,slug,name', 'outlet:id,name,slug']);

        if ($actor->isOwner()) {
            $ownedOutletIds = $actor->ownedOutlets->pluck('id');
            $query->where(function ($builder) use ($ownedOutletIds, $actor) {
                $builder->whereIn('outlet_id', $ownedOutletIds)
                    ->orWhere('id', $actor->id);
            });
        } else {
            $query->where('outlet_id', $actor->outlet_id);
        }

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($outletId = $request->integer('outlet_id')) {
            if (in_array($outletId, $this->allowedOutletIds($actor), true)) {
                $query->where('outlet_id', $outletId);
            }
        }

        $users = $query
            ->latest()
            ->paginate(12)
            ->through(fn (User $user) => $this->transformUser($user, $actor));

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'outlet_id' => $request->integer('outlet_id') ?: null,
            ],
            'users' => $users,
            'outlets' => $this->formOutlets($actor),
            'roles' => $this->formRoles($actor),
            'meta' => [
                'can_manage_team' => true,
                'actor_role' => $actor->roles()->pluck('slug')->first(),
            ],
        ]);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();
        $this->ensureFeatureAccess($actor);
        $this->authorizeManagedUser($actor, $user, false);
        $user->load(['roles:id,slug,name', 'outlet:id,name,slug']);

        return response()->json([
            'user' => $this->transformUser($user, $actor, true),
            'outlets' => $this->formOutlets($actor),
            'roles' => $this->formRoles($actor),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $actor = $request->user();
        $this->ensureFeatureAccess($actor);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults()],
            'role' => ['required', 'exists:roles,slug'],
            'outlet_id' => ['nullable', 'integer', 'exists:outlets,id'],
        ];

        $validated = $request->validate($rules);
        $this->assertRoleAllowed($actor, $validated['role']);

        if ($actor->isOwner()) {
            if (empty($validated['outlet_id']) || !in_array((int) $validated['outlet_id'], $this->allowedOutletIds($actor), true)) {
                abort(403, 'Outlet tidak valid untuk user ini.');
            }
        } else {
            $validated['outlet_id'] = $actor->outlet_id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'outlet_id' => $validated['outlet_id'],
        ]);

        $role = Role::query()->where('slug', $validated['role'])->firstOrFail();
        $user->roles()->attach($role);
        $user->load(['roles:id,slug,name', 'outlet:id,name,slug']);

        return response()->json([
            'message' => 'User team berhasil ditambahkan.',
            'user' => $this->transformUser($user, $actor, true),
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();
        $this->ensureFeatureAccess($actor);
        $this->authorizeManagedUser($actor, $user, true);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::defaults()],
            'role' => ['required', 'exists:roles,slug'],
            'outlet_id' => ['nullable', 'integer', 'exists:outlets,id'],
        ];

        $validated = $request->validate($rules);
        $this->assertRoleAllowed($actor, $validated['role']);

        if ($actor->isOwner()) {
            if (empty($validated['outlet_id']) || !in_array((int) $validated['outlet_id'], $this->allowedOutletIds($actor), true)) {
                abort(403, 'Outlet tidak valid untuk user ini.');
            }
        } else {
            $validated['outlet_id'] = $user->outlet_id;
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'outlet_id' => $validated['outlet_id'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        $role = Role::query()->where('slug', $validated['role'])->firstOrFail();
        $user->roles()->sync([$role->id]);
        $user->load(['roles:id,slug,name', 'outlet:id,name,slug']);

        return response()->json([
            'message' => 'User team berhasil diperbarui.',
            'user' => $this->transformUser($user, $actor, true),
        ]);
    }

    protected function transformUser(User $user, User $actor, bool $detailed = false): array
    {
        $user->loadMissing(['roles:id,slug,name', 'outlet:id,name,slug']);

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => (bool) $user->email_verified_at,
            'role' => $user->roles->pluck('slug')->first(),
            'role_label' => $user->roles->pluck('name')->first(),
            'outlet' => $user->outlet ? [
                'id' => $user->outlet->id,
                'name' => $user->outlet->name,
                'slug' => $user->outlet->slug,
            ] : null,
            'created_at' => optional($user->created_at)->toIso8601String(),
            'permissions' => [
                'can_edit' => $this->canManageUser($actor, $user),
            ],
        ];

        if ($detailed) {
            $data['outlet_id'] = $user->outlet_id;
        }

        return $data;
    }

    protected function ensureFeatureAccess(User $actor): void
    {
        abort_unless($actor->hasFeature('team_management'), 403, 'Kelola admin dan staff tersedia mulai paket Pro.');
        abort_unless($actor->isOwner() || $actor->isAdmin(), 403);
    }

    protected function canManageUser(User $actor, User $user): bool
    {
        if ($actor->isOwner()) {
            if ($user->id === $actor->id) {
                return false;
            }

            return $actor->ownedOutlets->contains('id', $user->outlet_id);
        }

        if ($actor->isAdmin()) {
            return $user->outlet_id === $actor->outlet_id
                && !$user->hasRole('owner')
                && !$user->hasRole('admin');
        }

        return false;
    }

    protected function authorizeManagedUser(User $actor, User $user, bool $updating): void
    {
        abort_unless($this->canManageUser($actor, $user), 403);

        if ($updating && $actor->isAdmin() && $user->id === $actor->id) {
            abort(403);
        }
    }

    protected function assertRoleAllowed(User $actor, string $role): void
    {
        if ($actor->isOwner()) {
            abort_unless(in_array($role, ['admin', 'staff'], true), 403);
            return;
        }

        abort_unless($role === 'staff', 403);
    }

    protected function allowedOutletIds(User $actor): array
    {
        if ($actor->isOwner()) {
            return $actor->ownedOutlets->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $actor->outlet_id ? [(int) $actor->outlet_id] : [];
    }

    protected function formOutlets(User $actor): array
    {
        return Outlet::query()
            ->whereIn('id', $this->allowedOutletIds($actor))
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Outlet $outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
            ])
            ->values()
            ->all();
    }

    protected function formRoles(User $actor): array
    {
        $allowedRoles = $actor->isOwner() ? ['admin', 'staff'] : ['staff'];

        return Role::query()
            ->whereIn('slug', $allowedRoles)
            ->orderBy('id')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ])
            ->values()
            ->all();
    }
}
