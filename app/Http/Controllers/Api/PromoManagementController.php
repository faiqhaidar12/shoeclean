<?php

namespace App\Http\Controllers\Api;

use App\Models\Outlet;
use App\Models\Promo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoManagementController
{
    public function index(Request $request): JsonResponse
    {
        $this->ensurePromoFeature($request);

        $user = $request->user();
        $allowedOutletIds = $this->visibleOutletIds($request);
        $selectedOutletId = $request->integer('outlet_id') ?: null;
        $status = trim((string) $request->string('status'));
        $type = trim((string) $request->string('type'));

        $query = Promo::query()
            ->with('outlet:id,name,slug')
            ->where(function ($builder) use ($allowedOutletIds) {
                $builder
                    ->whereIn('outlet_id', $allowedOutletIds)
                    ->orWhereNull('outlet_id');
            })
            ->latest();

        if ($selectedOutletId && in_array($selectedOutletId, $allowedOutletIds, true)) {
            $query->where('outlet_id', $selectedOutletId);
        } elseif ($user->isOwner() && session('current_outlet_id')) {
            $sessionOutletId = (int) session('current_outlet_id');
            if (in_array($sessionOutletId, $allowedOutletIds, true)) {
                $query->where(function ($builder) use ($sessionOutletId) {
                    $builder
                        ->where('outlet_id', $sessionOutletId)
                        ->orWhereNull('outlet_id');
                });
                $selectedOutletId = $sessionOutletId;
            }
        }

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('code', 'like', '%' . strtoupper($search) . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        if (in_array($type, ['percentage', 'fixed'], true)) {
            $query->where('type', $type);
        }

        if ($status !== '') {
            $query->getQuery()->wheres = $query->getQuery()->wheres ?? [];
            $query->where(function ($builder) use ($status) {
                $today = now()->toDateString();

                match ($status) {
                    'running' => $builder
                        ->where('is_active', true)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where(function ($nested) {
                            $nested
                                ->whereNull('max_uses')
                                ->orWhereColumn('used_count', '<', 'max_uses');
                        }),
                    'scheduled' => $builder
                        ->where('is_active', true)
                        ->whereDate('start_date', '>', $today),
                    'expired' => $builder
                        ->where(function ($nested) use ($today) {
                            $nested
                                ->whereDate('end_date', '<', $today)
                                ->orWhere(function ($limit) {
                                    $limit
                                        ->whereNotNull('max_uses')
                                        ->whereColumn('used_count', '>=', 'max_uses');
                                });
                        }),
                    'inactive' => $builder->where('is_active', false),
                    default => null,
                };
            });
        }

        $promos = $query
            ->paginate(12)
            ->through(fn (Promo $promo) => $this->transformPromo($promo));

        $activePromos = Promo::query()
            ->where(function ($builder) use ($allowedOutletIds) {
                $builder
                    ->whereIn('outlet_id', $allowedOutletIds)
                    ->orWhereNull('outlet_id');
            })
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->where(function ($builder) {
                $builder
                    ->whereNull('max_uses')
                    ->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->count();

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'status' => $status,
                'type' => $type,
                'outlet_id' => $selectedOutletId ?: null,
            ],
            'summary' => [
                'total_promos' => $promos->total(),
                'active_promos' => $activePromos,
            ],
            'outlets' => $this->formOutlets($request),
            'types' => ['percentage', 'fixed'],
            'statuses' => ['running', 'scheduled', 'expired', 'inactive'],
            'promos' => $promos,
            'meta' => [
                'can_manage_global' => $user->isOwner(),
                'feature_plan' => $user->currentPlan(),
            ],
        ]);
    }

    public function show(Request $request, Promo $promo): JsonResponse
    {
        $this->ensurePromoFeature($request);
        $this->authorizePromo($request, $promo);
        $promo->load('outlet:id,name,slug');

        return response()->json([
            'promo' => $this->transformPromo($promo, true),
            'outlets' => $this->formOutlets($request),
            'types' => ['percentage', 'fixed'],
            'statuses' => ['running', 'scheduled', 'expired', 'inactive'],
            'meta' => [
                'can_manage_global' => $request->user()->isOwner(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensurePromoFeature($request);

        $validated = $request->validate([
            'outlet_id' => ['nullable', 'integer', 'exists:outlets,id'],
            'code' => ['required', 'string', 'max:20', 'unique:promos,code'],
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'integer', 'min:1'],
            'min_order' => ['nullable', 'integer', 'min:0'],
            'max_discount' => ['nullable', 'integer', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId(
            $request,
            isset($validated['outlet_id']) ? (int) $validated['outlet_id'] : null
        );
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order'] = (int) ($validated['min_order'] ?? 0);
        $validated['max_discount'] = $validated['type'] === 'percentage'
            ? ($validated['max_discount'] ?? null)
            : null;
        $validated['max_uses'] = $validated['max_uses'] ?? null;
        $validated['used_count'] = 0;

        $promo = Promo::create($validated);
        $promo->load('outlet:id,name,slug');

        return response()->json([
            'message' => 'Promo berhasil dibuat.',
            'promo' => $this->transformPromo($promo, true),
        ], 201);
    }

    public function update(Request $request, Promo $promo): JsonResponse
    {
        $this->ensurePromoFeature($request);
        $this->authorizePromo($request, $promo);

        $validated = $request->validate([
            'outlet_id' => ['nullable', 'integer', 'exists:outlets,id'],
            'code' => ['required', 'string', 'max:20', 'unique:promos,code,' . $promo->id],
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'integer', 'min:1'],
            'min_order' => ['nullable', 'integer', 'min:0'],
            'max_discount' => ['nullable', 'integer', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId(
            $request,
            isset($validated['outlet_id']) ? (int) $validated['outlet_id'] : null
        );
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order'] = (int) ($validated['min_order'] ?? 0);
        $validated['max_discount'] = $validated['type'] === 'percentage'
            ? ($validated['max_discount'] ?? null)
            : null;
        $validated['max_uses'] = $validated['max_uses'] ?? null;

        $promo->update($validated);
        $promo->load('outlet:id,name,slug');

        return response()->json([
            'message' => 'Promo berhasil diperbarui.',
            'promo' => $this->transformPromo($promo, true),
        ]);
    }

    protected function transformPromo(Promo $promo, bool $detailed = false): array
    {
        $status = $this->availabilityStatus($promo);

        $base = [
            'id' => $promo->id,
            'code' => $promo->code,
            'name' => $promo->name,
            'type' => $promo->type,
            'value' => $promo->value,
            'min_order' => $promo->min_order,
            'max_discount' => $promo->max_discount,
            'max_uses' => $promo->max_uses,
            'used_count' => $promo->used_count,
            'start_date' => optional($promo->start_date)->format('Y-m-d'),
            'end_date' => optional($promo->end_date)->format('Y-m-d'),
            'is_active' => (bool) $promo->is_active,
            'availability_status' => $status,
            'availability_label' => match ($status) {
                'running' => 'Sedang aktif',
                'scheduled' => 'Terjadwal',
                'expired' => 'Berakhir',
                'inactive' => 'Nonaktif',
                default => 'Tidak diketahui',
            },
            'created_at' => optional($promo->created_at)->toIso8601String(),
            'outlet' => $promo->outlet ? [
                'id' => $promo->outlet->id,
                'name' => $promo->outlet->name,
                'slug' => $promo->outlet->slug,
            ] : null,
        ];

        if ($detailed) {
            $base['outlet_id'] = $promo->outlet_id;
        }

        return $base;
    }

    protected function ensurePromoFeature(Request $request): void
    {
        abort_unless($request->user()->hasFeature('promos'), 403, 'Fitur promo tersedia mulai paket Pro.');
    }

    protected function visibleOutletIds(Request $request): array
    {
        return array_map('intval', $request->user()->allOutletIds());
    }

    protected function authorizePromo(Request $request, Promo $promo): void
    {
        if (is_null($promo->outlet_id)) {
            abort_unless($request->user()->isOwner(), 403);

            return;
        }

        abort_unless(in_array((int) $promo->outlet_id, $this->visibleOutletIds($request), true), 403);
    }

    protected function sanitizeOutletId(Request $request, ?int $outletId): ?int
    {
        if (is_null($outletId)) {
            abort_unless($request->user()->isOwner(), 403, 'Promo global hanya bisa dibuat owner.');

            return null;
        }

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

    protected function availabilityStatus(Promo $promo): string
    {
        $today = now()->startOfDay();

        if (!$promo->is_active) {
            return 'inactive';
        }

        if ($today->lt($promo->start_date)) {
            return 'scheduled';
        }

        if ($today->gt($promo->end_date)) {
            return 'expired';
        }

        if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
            return 'expired';
        }

        return 'running';
    }
}
