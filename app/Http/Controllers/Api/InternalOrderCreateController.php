<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Promo;
use App\Models\Service;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InternalOrderCreateController
{
    public function meta(Request $request): JsonResponse
    {
        $user = $request->user();
        $allowedOutletIds = $this->visibleOutletIds($request);
        $selectedOutletId = $this->resolveSelectedOutletId($request, $allowedOutletIds);
        $subscriptionService = new SubscriptionService();
        $orderLimitInfo = $subscriptionService->checkOrderLimit($user);

        $outlets = Outlet::query()
            ->whereIn('id', $allowedOutletIds)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'pickup_fee', 'delivery_fee']);

        $services = Service::query()
            ->whereIn('outlet_id', $allowedOutletIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'outlet_id', 'name', 'unit', 'price']);

        return response()->json([
            'order_limit' => $orderLimitInfo,
            'selected_outlet_id' => $selectedOutletId,
            'outlets' => $outlets->map(fn (Outlet $outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
                'pickup_fee' => $outlet->pickup_fee,
                'delivery_fee' => $outlet->delivery_fee,
            ])->values(),
            'services' => $services->map(fn (Service $service) => [
                'id' => $service->id,
                'outlet_id' => $service->outlet_id,
                'name' => $service->name,
                'unit' => $service->unit,
                'price' => $service->price,
            ])->values(),
            'user' => [
                'id' => $user->id,
                'roles' => $user->roles()->pluck('slug')->values()->all(),
                'can_choose_outlet' => $user->isOwner() || $user->isAdmin(),
            ],
        ]);
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $term = trim((string) $request->string('search'));

        if (mb_strlen($term) < 2) {
            return response()->json(['customers' => []]);
        }

        $customers = Customer::query()
            ->with('outlet:id,name,slug')
            ->whereIn('outlet_id', $this->visibleOutletIds($request))
            ->search($term)
            ->limit(8)
            ->get();

        return response()->json([
            'customers' => $customers->map(function (Customer $customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'outlet' => $customer->outlet ? [
                        'id' => $customer->outlet->id,
                        'name' => $customer->outlet->name,
                        'slug' => $customer->outlet->slug,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    public function quickAddCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:customers,phone'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId($request, (int) $validated['outlet_id']);

        $customer = Customer::create([
            'outlet_id' => $validated['outlet_id'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $customer->load('outlet:id,name,slug');

        return response()->json([
            'message' => 'Customer berhasil ditambahkan.',
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'outlet' => $customer->outlet ? [
                    'id' => $customer->outlet->id,
                    'name' => $customer->outlet->name,
                    'slug' => $customer->outlet->slug,
                ] : null,
            ],
        ], 201);
    }

    public function validatePromo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
            'code' => ['required', 'string'],
            'subtotal' => ['required', 'integer', 'min:0'],
        ]);

        $outletId = $this->sanitizeOutletId($request, (int) $validated['outlet_id']);
        $promo = Promo::query()->where('code', strtoupper($validated['code']))->first();

        if (! $promo || ! $promo->isValid($outletId, (int) $validated['subtotal'])) {
            throw ValidationException::withMessages([
                'code' => ['Promo tidak berlaku untuk order ini.'],
            ]);
        }

        return response()->json([
            'message' => "Promo {$promo->code} diterapkan.",
            'promo' => [
                'id' => $promo->id,
                'code' => $promo->code,
                'name' => $promo->name,
                'discount_amount' => $promo->calculateDiscount((int) $validated['subtotal']),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->canCreateOrder()) {
            throw ValidationException::withMessages([
                'order' => ['Kuota order habis. Silakan upgrade paket atau beli kuota tambahan.'],
            ]);
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
            'notes' => ['nullable', 'string'],
            'order_type' => ['required', 'in:regular,pickup,delivery'],
            'pickup_address' => ['nullable', 'string'],
            'delivery_address' => ['nullable', 'string'],
            'promo_code' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $outletId = $this->sanitizeOutletId($request, (int) $validated['outlet_id']);
        $customer = Customer::query()->findOrFail((int) $validated['customer_id']);
        abort_unless(in_array((int) $customer->outlet_id, $this->visibleOutletIds($request), true), 403);

        $services = Service::query()
            ->whereIn('id', collect($validated['items'])->pluck('service_id'))
            ->where('outlet_id', $outletId)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        if ($services->count() !== count($validated['items'])) {
            throw ValidationException::withMessages([
                'items' => ['Ada layanan yang tidak valid untuk outlet terpilih.'],
            ]);
        }

        $outlet = Outlet::query()->findOrFail($outletId);
        $subtotal = 0;

        foreach ($validated['items'] as $item) {
            $service = $services[(int) $item['service_id']];
            $subtotal += $service->price * (int) $item['quantity'];
        }

        $promo = null;
        $discountAmount = 0;

        if (! empty($validated['promo_code'])) {
            $promo = Promo::query()->where('code', strtoupper($validated['promo_code']))->first();

            if (! $promo || ! $promo->isValid($outletId, $subtotal)) {
                throw ValidationException::withMessages([
                    'promo_code' => ['Promo tidak berlaku untuk order ini.'],
                ]);
            }

            $discountAmount = $promo->calculateDiscount($subtotal);
        }

        $pickupFee = $validated['order_type'] === 'pickup' ? (int) $outlet->pickup_fee : 0;
        $deliveryFee = $validated['order_type'] === 'delivery' ? (int) $outlet->delivery_fee : 0;
        $totalPrice = max(0, $subtotal + $pickupFee + $deliveryFee - $discountAmount);

        $order = DB::transaction(function () use (
            $validated,
            $outletId,
            $user,
            $pickupFee,
            $deliveryFee,
            $discountAmount,
            $totalPrice,
            $promo,
            $services
        ) {
            $date = date('Ymd');
            $count = Order::query()
                ->whereDate('created_at', today())
                ->where('outlet_id', $outletId)
                ->count() + 1;
            $sequence = str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV/{$date}/{$outletId}/{$sequence}";

            $order = Order::create([
                'outlet_id' => $outletId,
                'customer_id' => (int) $validated['customer_id'],
                'user_id' => $user->id,
                'invoice_number' => $invoiceNumber,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_price' => $totalPrice,
                'notes' => $validated['notes'] ?? null,
                'order_type' => $validated['order_type'],
                'pickup_address' => $validated['order_type'] === 'pickup' ? ($validated['pickup_address'] ?? null) : null,
                'delivery_address' => $validated['order_type'] === 'delivery' ? ($validated['delivery_address'] ?? null) : null,
                'pickup_fee' => $pickupFee,
                'delivery_fee' => $deliveryFee,
                'promo_id' => $promo?->id,
                'discount_amount' => $discountAmount,
            ]);

            foreach ($validated['items'] as $item) {
                $service = $services[(int) $item['service_id']];

                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'quantity' => (int) $item['quantity'],
                    'unit' => $service->unit,
                    'price' => $service->price,
                    'total_price' => $service->price * (int) $item['quantity'],
                ]);
            }

            if ($promo) {
                $promo->increment('used_count');
            }

            $user->useOrderSlot();

            return $order;
        });

        return response()->json([
            'message' => 'Order berhasil dibuat.',
            'order' => [
                'id' => $order->id,
                'invoice_number' => $order->invoice_number,
            ],
            'next_frontend_path' => "/dashboard/orders/{$order->id}",
        ], 201);
    }

    protected function visibleOutletIds(Request $request): array
    {
        return array_map('intval', $request->user()->allOutletIds());
    }

    protected function resolveSelectedOutletId(Request $request, array $allowedOutletIds): ?int
    {
        $requestedOutletId = $request->integer('outlet_id');
        if ($requestedOutletId && in_array($requestedOutletId, $allowedOutletIds, true)) {
            return $requestedOutletId;
        }

        $sessionOutletId = session('current_outlet_id');
        if ($sessionOutletId && in_array((int) $sessionOutletId, $allowedOutletIds, true)) {
            return (int) $sessionOutletId;
        }

        return $allowedOutletIds[0] ?? null;
    }

    protected function sanitizeOutletId(Request $request, int $outletId): int
    {
        abort_unless(in_array($outletId, $this->visibleOutletIds($request), true), 403);

        return $outletId;
    }
}
