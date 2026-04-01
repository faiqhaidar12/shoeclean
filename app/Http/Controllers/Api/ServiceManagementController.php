<?php

namespace App\Http\Controllers\Api;

use App\Models\Outlet;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceManagementController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $allowedOutletIds = $this->visibleOutletIds($request);
        $selectedOutletId = $request->integer('outlet_id') ?: null;

        $query = Service::query()
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
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $services = $query
            ->paginate(12)
            ->through(fn (Service $service) => $this->transformService($service));

        $outlets = Outlet::query()
            ->whereIn('id', $allowedOutletIds)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'status' => $status ?: '',
                'outlet_id' => $selectedOutletId ?: ($user->isOwner() ? (session('current_outlet_id') ? (int) session('current_outlet_id') : null) : null),
            ],
            'outlets' => $outlets->map(fn (Outlet $outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
            ])->values(),
            'services' => $services,
        ]);
    }

    public function show(Request $request, Service $service): JsonResponse
    {
        $this->authorizeService($request, $service);
        $service->load('outlet:id,name,slug');

        return response()->json([
            'service' => $this->transformService($service, true),
            'outlets' => $this->formOutlets($request),
            'units' => ['kg', 'pcs', 'pasang', 'meter'],
            'statuses' => ['active', 'inactive'],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'in:kg,pcs,pasang,meter'],
            'price' => ['required', 'numeric', 'min:0'],
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId($request, (int) $validated['outlet_id']);
        $validated['status'] = 'active';

        $service = Service::create($validated);
        $service->load('outlet:id,name,slug');

        return response()->json([
            'message' => 'Layanan berhasil ditambahkan.',
            'service' => $this->transformService($service, true),
        ], 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $this->authorizeService($request, $service);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'in:kg,pcs,pasang,meter'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId($request, (int) $validated['outlet_id']);

        $service->update($validated);
        $service->load('outlet:id,name,slug');

        return response()->json([
            'message' => 'Layanan berhasil diperbarui.',
            'service' => $this->transformService($service, true),
        ]);
    }

    protected function transformService(Service $service, bool $detailed = false): array
    {
        $base = [
            'id' => $service->id,
            'name' => $service->name,
            'unit' => $service->unit,
            'price' => $service->price,
            'status' => $service->status,
            'created_at' => optional($service->created_at)->toIso8601String(),
            'outlet' => $service->outlet ? [
                'id' => $service->outlet->id,
                'name' => $service->outlet->name,
                'slug' => $service->outlet->slug,
            ] : null,
        ];

        if ($detailed) {
            $base['outlet_id'] = $service->outlet_id;
        }

        return $base;
    }

    protected function visibleOutletIds(Request $request): array
    {
        return array_map('intval', $request->user()->allOutletIds());
    }

    protected function authorizeService(Request $request, Service $service): void
    {
        abort_unless(in_array((int) $service->outlet_id, $this->visibleOutletIds($request), true), 403);
    }

    protected function sanitizeOutletId(Request $request, int $outletId): int
    {
        abort_unless(in_array($outletId, $this->visibleOutletIds($request), true), 403);

        return $outletId;
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
