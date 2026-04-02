<?php

namespace App\Http\Controllers\Api;

use App\Models\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OutletManagementController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Outlet::query()
            ->withCount(['users', 'services', 'orders'])
            ->latest();

        if ($user->isOwner()) {
            $query->where('owner_id', $user->id);
        } else {
            abort_unless($user->isAdmin() && $user->outlet_id, 403);
            $query->whereKey($user->outlet_id);
        }

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        if ($status = trim((string) $request->string('status'))) {
            $query->where('status', $status);
        }

        $outlets = $query
            ->paginate(12)
            ->through(fn (Outlet $outlet) => $this->transformOutlet($outlet));

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'status' => $status ?? '',
            ],
            'summary' => [
                'total_outlets' => $outlets->total(),
                'active_outlets' => $user->isOwner()
                    ? $user->ownedOutlets()->where('status', 'active')->count()
                    : Outlet::query()->whereKey($user->outlet_id)->where('status', 'active')->count(),
            ],
            'outlets' => $outlets,
            'statuses' => ['active', 'inactive'],
            'meta' => [
                'is_owner' => $user->isOwner(),
                'can_create' => $user->isOwner() && $user->canCreateOutlet(),
                'max_outlets' => $user->maxOutlets(),
                'current_outlets' => $user->isOwner() ? $user->ownedOutlets()->count() : 1,
            ],
        ]);
    }

    public function show(Request $request, Outlet $outlet): JsonResponse
    {
        $this->authorizeOutlet($request, $outlet);
        $outlet->loadCount(['users', 'services', 'orders']);

        return response()->json([
            'outlet' => $this->transformOutlet($outlet, true),
            'statuses' => ['active', 'inactive'],
            'meta' => [
                'is_owner' => $request->user()->isOwner(),
                'can_edit_status' => $request->user()->isOwner() || $request->user()->isAdmin(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isOwner(), 403);
        abort_unless($user->canCreateOutlet(), 403, 'Limit outlet Anda telah habis.');

        $validated = $this->validatePayload($request);
        $qris = $this->storeQrisImage($request->file('qris_image'));

        $outlet = Outlet::create([
            'owner_id' => $user->id,
            'name' => $validated['name'],
            'slug' => $this->makeUniqueSlug($validated['slug'] ?? $validated['name']),
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'pickup_fee' => $validated['pickup_fee'],
            'delivery_fee' => $validated['delivery_fee'],
            'status' => $validated['status'] ?? 'active',
            'province_id' => $validated['province_id'],
            'province_name' => $validated['province_name'],
            'city_id' => $validated['city_id'],
            'city_name' => $validated['city_name'],
            'district_id' => $validated['district_id'] ?? null,
            'district_name' => $validated['district_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'qris_image_path' => $qris['path'],
            'qris_image_original_name' => $qris['original_name'],
            'qris_notes' => $validated['qris_notes'] ?? null,
        ]);

        $outlet->loadCount(['users', 'services', 'orders']);

        return response()->json([
            'message' => 'Outlet berhasil dibuat.',
            'outlet' => $this->transformOutlet($outlet, true),
        ], 201);
    }

    public function update(Request $request, Outlet $outlet): JsonResponse
    {
        $this->authorizeOutlet($request, $outlet);

        $validated = $this->validatePayload($request, $outlet);
        $qrisPath = $outlet->qris_image_path;
        $qrisOriginalName = $outlet->qris_image_original_name;

        if ($request->boolean('remove_qris')) {
            if ($qrisPath) {
                Storage::disk('public')->delete($qrisPath);
            }

            $qrisPath = null;
            $qrisOriginalName = null;
        }

        if ($request->hasFile('qris_image')) {
            if ($qrisPath && !$request->boolean('remove_qris')) {
                Storage::disk('public')->delete($qrisPath);
            }

            $stored = $this->storeQrisImage($request->file('qris_image'));
            $qrisPath = $stored['path'];
            $qrisOriginalName = $stored['original_name'];
        }

        $outlet->update([
            'name' => $validated['name'],
            'slug' => $this->makeUniqueSlug($validated['slug'] ?? $validated['name'], $outlet->id),
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'pickup_fee' => $validated['pickup_fee'],
            'delivery_fee' => $validated['delivery_fee'],
            'status' => $validated['status'],
            'province_id' => $validated['province_id'],
            'province_name' => $validated['province_name'],
            'city_id' => $validated['city_id'],
            'city_name' => $validated['city_name'],
            'district_id' => $validated['district_id'] ?? null,
            'district_name' => $validated['district_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'qris_image_path' => $qrisPath,
            'qris_image_original_name' => $qrisOriginalName,
            'qris_notes' => $validated['qris_notes'] ?? null,
        ]);

        $outlet->loadCount(['users', 'services', 'orders']);

        return response()->json([
            'message' => 'Outlet berhasil diperbarui.',
            'outlet' => $this->transformOutlet($outlet, true),
        ]);
    }

    protected function validatePayload(Request $request, ?Outlet $outlet = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('outlets', 'slug')->ignore($outlet?->id),
            ],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'pickup_fee' => ['required', 'numeric', 'min:0'],
            'delivery_fee' => ['required', 'numeric', 'min:0'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'qris_notes' => ['nullable', 'string', 'max:1000'],
            'remove_qris' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,inactive'],
            'province_id' => ['required', 'string'],
            'province_name' => ['required', 'string'],
            'city_id' => ['required', 'string'],
            'city_name' => ['required', 'string'],
            'district_id' => ['nullable', 'string'],
            'district_name' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
    }

    protected function authorizeOutlet(Request $request, Outlet $outlet): void
    {
        $user = $request->user();

        if ($user->isOwner()) {
            abort_unless((int) $outlet->owner_id === (int) $user->id, 403);

            return;
        }

        abort_unless($user->isAdmin() && (int) $user->outlet_id === (int) $outlet->id, 403);
    }

    protected function transformOutlet(Outlet $outlet, bool $detailed = false): array
    {
        $base = [
            'id' => $outlet->id,
            'name' => $outlet->name,
            'slug' => $outlet->slug,
            'address' => $outlet->address,
            'phone' => $outlet->phone,
            'status' => $outlet->status,
            'pickup_fee' => (int) $outlet->pickup_fee,
            'delivery_fee' => (int) $outlet->delivery_fee,
            'province_id' => $outlet->province_id,
            'province_name' => $outlet->province_name,
            'city_id' => $outlet->city_id,
            'city_name' => $outlet->city_name,
            'district_id' => $outlet->district_id,
            'district_name' => $outlet->district_name,
            'latitude' => $outlet->latitude,
            'longitude' => $outlet->longitude,
            'qris_image_url' => $outlet->qris_image_path ? url(Storage::url($outlet->qris_image_path)) : null,
            'qris_image_original_name' => $outlet->qris_image_original_name,
            'qris_notes' => $outlet->qris_notes,
            'created_at' => optional($outlet->created_at)->toIso8601String(),
            'counts' => [
                'users' => (int) ($outlet->users_count ?? 0),
                'services' => (int) ($outlet->services_count ?? 0),
                'orders' => (int) ($outlet->orders_count ?? 0),
            ],
        ];

        if ($detailed) {
            $base['owner_id'] = $outlet->owner_id;
        }

        return $base;
    }

    protected function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'outlet';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Outlet::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function storeQrisImage(?UploadedFile $file): array
    {
        if (!$file) {
            return [
                'path' => null,
                'original_name' => null,
            ];
        }

        return [
            'path' => $file->store('qris', 'public'),
            'original_name' => $file->getClientOriginalName(),
        ];
    }
}
