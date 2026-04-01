<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerManagementController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $selectedOutletId = $request->integer('outlet_id') ?: null;
        $allowedOutletIds = $this->visibleOutletIds($request);

        $query = Customer::query()
            ->with('outlet:id,name,slug')
            ->whereIn('outlet_id', $allowedOutletIds)
            ->latest();

        if ($selectedOutletId && in_array($selectedOutletId, $allowedOutletIds, true)) {
            $query->where('outlet_id', $selectedOutletId);
        } elseif ($user->isOwner() && session('current_outlet_id')) {
            $sessionOutletId = (int) session('current_outlet_id');

            if (in_array($sessionOutletId, $allowedOutletIds, true)) {
                $query->where('outlet_id', $sessionOutletId);
            }
        }

        if ($search = trim((string) $request->string('search'))) {
            $query->search($search);
        }

        $customers = $query
            ->paginate(10)
            ->through(fn (Customer $customer) => $this->transformCustomer($customer));

        $outlets = Outlet::query()
            ->whereIn('id', $allowedOutletIds)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'outlet_id' => $selectedOutletId ?: ($user->isOwner() ? (session('current_outlet_id') ? (int) session('current_outlet_id') : null) : null),
            ],
            'outlets' => $outlets,
            'customers' => $customers,
            'meta' => [
                'can_choose_outlet' => $user->isOwner(),
            ],
        ]);
    }

    public function show(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeCustomer($request, $customer);
        $customer->load('outlet:id,name,slug');

        return response()->json([
            'customer' => $this->transformCustomer($customer, true),
            'outlets' => $this->formOutlets($request),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:customers,phone'],
            'address' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId($request, (int) $validated['outlet_id']);

        $customer = Customer::create($validated);
        $customer->load('outlet:id,name,slug');

        return response()->json([
            'message' => 'Customer berhasil ditambahkan.',
            'customer' => $this->transformCustomer($customer, true),
        ], 201);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeCustomer($request, $customer);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'address' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId($request, (int) $validated['outlet_id'], $customer->outlet_id);

        $customer->update($validated);
        $customer->load('outlet:id,name,slug');

        return response()->json([
            'message' => 'Customer berhasil diperbarui.',
            'customer' => $this->transformCustomer($customer, true),
        ]);
    }

    protected function transformCustomer(Customer $customer, bool $detailed = false): array
    {
        $base = [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'email' => $customer->email,
            'created_at' => optional($customer->created_at)->toIso8601String(),
            'outlet' => $customer->outlet ? [
                'id' => $customer->outlet->id,
                'name' => $customer->outlet->name,
                'slug' => $customer->outlet->slug,
            ] : null,
        ];

        if ($detailed) {
            $base['outlet_id'] = $customer->outlet_id;
        }

        return $base;
    }

    protected function visibleOutletIds(Request $request): array
    {
        return array_map('intval', $request->user()->allOutletIds());
    }

    protected function authorizeCustomer(Request $request, Customer $customer): void
    {
        abort_unless(in_array((int) $customer->outlet_id, $this->visibleOutletIds($request), true), 403);
    }

    protected function sanitizeOutletId(Request $request, int $outletId, ?int $fallback = null): int
    {
        $user = $request->user();
        $allowedOutletIds = $this->visibleOutletIds($request);

        if ($user->isOwner()) {
            abort_unless(in_array($outletId, $allowedOutletIds, true), 403);

            return $outletId;
        }

        return $fallback ?? (int) $user->outlet_id;
    }

    protected function formOutlets(Request $request): array
    {
        return Outlet::query()
            ->whereIn('id', $this->visibleOutletIds($request))
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
}
