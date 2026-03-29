<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Promo;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PublicStorefrontController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $provinceId = $request->query('province_id');
        $cityId = $request->query('city_id');

        $provinces = Outlet::query()
            ->where('status', 'active')
            ->whereNotNull('province_id')
            ->select('province_id', 'province_name')
            ->distinct()
            ->orderBy('province_name')
            ->get()
            ->map(fn ($province) => [
                'id' => $province->province_id,
                'name' => $province->province_name,
            ])
            ->values();

        $citiesQuery = Outlet::query()
            ->where('status', 'active')
            ->whereNotNull('city_id');

        if ($provinceId) {
            $citiesQuery->where('province_id', $provinceId);
        }

        $cities = $citiesQuery
            ->select('city_id', 'city_name')
            ->distinct()
            ->orderBy('city_name')
            ->get()
            ->map(fn ($city) => [
                'id' => $city->city_id,
                'name' => $city->city_name,
            ])
            ->values();

        $outlets = Outlet::query()
            ->where('status', 'active')
            ->withCount('services')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($provinceId, fn ($query) => $query->where('province_id', $provinceId))
            ->when($cityId, fn ($query) => $query->where('city_id', $cityId))
            ->orderBy('name')
            ->get()
            ->map(fn (Outlet $outlet) => $this->serializeOutletCard($outlet))
            ->values();

        return response()->json([
            'filters' => [
                'search' => $search,
                'province_id' => $provinceId,
                'city_id' => $cityId,
            ],
            'provinces' => $provinces,
            'cities' => $cities,
            'outlets' => $outlets,
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $outlet = Outlet::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->with(['owner', 'services' => fn ($query) => $query->where('status', 'active')->orderBy('name')])
            ->firstOrFail();

        $skipBranch = filter_var($request->query('skip_branch', false), FILTER_VALIDATE_BOOL);

        $siblingOutlets = Outlet::query()
            ->where('owner_id', $outlet->owner_id)
            ->where('status', 'active')
            ->withCount('services')
            ->orderBy('name')
            ->get();

        return response()->json([
            'outlet' => $this->serializeOutletDetail($outlet),
            'services' => $outlet->services->map(fn (Service $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'unit' => $service->unit,
                'price' => (int) $service->price,
            ])->values(),
            'sibling_outlets' => $siblingOutlets->map(fn (Outlet $branch) => $this->serializeOutletCard($branch))->values(),
            'ui' => [
                'show_branch_selection' => $siblingOutlets->count() > 1 && !$skipBranch,
                'order_limit_reached' => $outlet->owner ? !$outlet->owner->canCreateOrder() : false,
            ],
        ]);
    }

    public function validatePromo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'outlet_slug' => ['required', 'string', 'exists:outlets,slug'],
            'code' => ['required', 'string', 'max:255'],
            'subtotal' => ['required', 'integer', 'min:0'],
        ]);

        $outlet = Outlet::query()
            ->where('slug', $validated['outlet_slug'])
            ->where('status', 'active')
            ->firstOrFail();

        $promo = Promo::query()
            ->where('code', strtoupper($validated['code']))
            ->first();

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak ditemukan.',
            ], 404);
        }

        if (!$promo->isValid($outlet->id, (int) $validated['subtotal'])) {
            return response()->json([
                'valid' => false,
                'message' => 'Promo tidak berlaku untuk order ini.',
            ], 422);
        }

        $discountAmount = $promo->calculateDiscount((int) $validated['subtotal']);

        return response()->json([
            'valid' => true,
            'message' => "Promo {$promo->code} diterapkan.",
            'promo' => [
                'id' => $promo->id,
                'code' => $promo->code,
                'name' => $promo->name,
                'discount_amount' => (int) $discountAmount,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->validatedOrderPayload($request);

        $outlet = Outlet::query()
            ->where('slug', $payload['outlet_slug'])
            ->where('status', 'active')
            ->with(['owner'])
            ->firstOrFail();

        $owner = $outlet->owner;
        if ($owner && !$owner->canCreateOrder()) {
            return response()->json([
                'message' => 'Maaf, outlet ini sedang tidak bisa menerima order baru.',
            ], 422);
        }

        $services = Service::query()
            ->where('outlet_id', $outlet->id)
            ->where('status', 'active')
            ->whereIn('id', collect($payload['items'])->pluck('service_id'))
            ->get()
            ->keyBy('id');

        if ($services->count() !== count($payload['items'])) {
            throw ValidationException::withMessages([
                'items' => ['Terdapat layanan yang tidak valid untuk outlet ini.'],
            ]);
        }

        $subtotal = collect($payload['items'])->sum(function (array $item) use ($services) {
            $service = $services->get($item['service_id']);
            return $service->price * $item['quantity'];
        });

        $promo = null;
        $discountAmount = 0;

        if (!empty($payload['promo_code'])) {
            $promo = Promo::query()
                ->where('code', strtoupper($payload['promo_code']))
                ->first();

            if (!$promo || !$promo->isValid($outlet->id, $subtotal)) {
                throw ValidationException::withMessages([
                    'promo_code' => ['Promo tidak berlaku untuk order ini.'],
                ]);
            }

            $discountAmount = $promo->calculateDiscount($subtotal);
        }

        if ($payload['payment_method'] === 'qris' && !$outlet->qris_image_path) {
            throw ValidationException::withMessages([
                'payment_method' => ['QRIS belum tersedia untuk outlet yang dipilih.'],
            ]);
        }

        $paymentProofPath = $payload['payment_proof'] instanceof UploadedFile
            ? $payload['payment_proof']->store('payment-proofs', 'public')
            : null;

        $paymentStatus = $payload['payment_method'] === 'qris' && $paymentProofPath
            ? 'waiting_confirmation'
            : 'unpaid';

        $order = DB::transaction(function () use (
            $outlet,
            $owner,
            $payload,
            $services,
            $subtotal,
            $promo,
            $discountAmount,
            $paymentProofPath,
            $paymentStatus
        ) {
            $ownerOutletIds = Outlet::query()
                ->where('owner_id', $outlet->owner_id)
                ->pluck('id');

            $customer = Customer::query()
                ->whereIn('outlet_id', $ownerOutletIds)
                ->where('phone', $payload['customer_phone'])
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'outlet_id' => $outlet->id,
                    'name' => $payload['customer_name'],
                    'phone' => $payload['customer_phone'],
                ]);
            }

            $pickupFee = $payload['order_type'] === 'pickup' ? (int) $outlet->pickup_fee : 0;
            $deliveryFee = $payload['order_type'] === 'delivery' ? (int) $outlet->delivery_fee : 0;
            $totalPrice = max(0, $subtotal + $pickupFee + $deliveryFee - $discountAmount);

            $order = Order::create([
                'outlet_id' => $outlet->id,
                'customer_id' => $customer->id,
                'user_id' => null,
                'invoice_number' => $this->generateInvoiceNumber($outlet->id),
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'payment_method' => $payload['payment_method'],
                'payment_proof_path' => $paymentProofPath,
                'payment_proof_original_name' => $payload['payment_proof']?->getClientOriginalName(),
                'payment_proof_uploaded_at' => $paymentProofPath ? now() : null,
                'payment_notes' => $payload['payment_notes'] ?: null,
                'total_price' => $totalPrice,
                'notes' => $payload['notes'] ?: null,
                'order_type' => $payload['order_type'],
                'pickup_address' => $payload['order_type'] === 'pickup' ? ($payload['pickup_address'] ?: null) : null,
                'delivery_address' => $payload['order_type'] === 'delivery' ? ($payload['delivery_address'] ?: null) : null,
                'pickup_fee' => $pickupFee,
                'delivery_fee' => $deliveryFee,
                'promo_id' => $promo?->id,
                'discount_amount' => $discountAmount,
                'order_source' => 'customer',
            ]);

            foreach ($payload['items'] as $item) {
                $service = $services->get($item['service_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'quantity' => $item['quantity'],
                    'unit' => $service->unit,
                    'price' => $service->price,
                    'total_price' => $service->price * $item['quantity'],
                ]);
            }

            if ($promo) {
                $promo->increment('used_count');
            }

            if ($owner) {
                $owner->useOrderSlot();
            }

            return $order;
        });

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'order' => [
                'id' => $order->id,
                'invoice_number' => $order->invoice_number,
            ],
            'redirect_to' => route('public.order.success', [
                'outlet' => $outlet->slug,
                'order' => $order->id,
            ]),
            'next_frontend_path' => "/order/{$outlet->slug}/success/{$order->id}",
        ], 201);
    }

    public function success(string $slug, Order $order): JsonResponse
    {
        $outlet = Outlet::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        if ($order->outlet_id !== $outlet->id) {
            abort(404);
        }

        $order->load(['items.service', 'customer', 'promo', 'paymentVerifier']);

        return response()->json([
            'outlet' => $this->serializeOutletDetail($outlet),
            'order' => [
                'id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'order_type' => $order->order_type,
                'payment_status' => $order->payment_status,
                'payment_status_label' => $order->paymentStatusLabel(),
                'payment_method' => $order->payment_method,
                'payment_method_label' => $order->paymentMethodLabel(),
                'discount_amount' => (int) $order->discount_amount,
                'total_price' => (int) $order->total_price,
                'payment_notes' => $order->payment_notes,
                'customer' => [
                    'name' => $order->customer?->name,
                    'phone' => $order->customer?->phone,
                ],
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'service_name' => $item->service?->name,
                    'quantity' => (int) $item->quantity,
                    'unit' => $item->unit,
                    'total_price' => (int) $item->total_price,
                ])->values(),
            ],
        ]);
    }

    protected function validatedOrderPayload(Request $request): array
    {
        $rawItems = $request->input('items', []);

        if (is_string($rawItems)) {
            $decodedItems = json_decode($rawItems, true);
            $rawItems = is_array($decodedItems) ? $decodedItems : [];
        }

        $data = array_merge($request->all(), [
            'items' => $rawItems,
            'payment_proof' => $request->file('payment_proof'),
        ]);

        $validator = Validator::make($data, [
            'outlet_slug' => ['required', 'string', 'exists:outlets,slug'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'order_type' => ['required', 'in:regular,pickup,delivery'],
            'pickup_address' => ['nullable', 'string', 'max:1000'],
            'delivery_address' => ['nullable', 'string', 'max:1000'],
            'promo_code' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:pay_at_store,qris'],
            'payment_proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validator->after(function ($validator) use ($data) {
            if (($data['order_type'] ?? null) === 'pickup' && empty($data['pickup_address'])) {
                $validator->errors()->add('pickup_address', 'Alamat penjemputan wajib diisi untuk order pickup.');
            }

            if (($data['order_type'] ?? null) === 'delivery' && empty($data['delivery_address'])) {
                $validator->errors()->add('delivery_address', 'Alamat pengantaran wajib diisi untuk order delivery.');
            }
        });

        return $validator->validate();
    }

    protected function generateInvoiceNumber(int $outletId): string
    {
        $date = date('Ymd');
        $count = Order::query()
            ->whereDate('created_at', today())
            ->where('outlet_id', $outletId)
            ->count() + 1;

        $sequence = str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        return "INV/{$date}/{$outletId}/{$sequence}";
    }

    protected function serializeOutletCard(Outlet $outlet): array
    {
        return [
            'id' => $outlet->id,
            'name' => $outlet->name,
            'slug' => $outlet->slug,
            'address' => $outlet->address,
            'phone' => $outlet->phone,
            'province_id' => $outlet->province_id,
            'province_name' => $outlet->province_name,
            'city_id' => $outlet->city_id,
            'city_name' => $outlet->city_name,
            'pickup_fee' => (int) ($outlet->pickup_fee ?? 0),
            'delivery_fee' => (int) ($outlet->delivery_fee ?? 0),
            'has_qris' => filled($outlet->qris_image_path),
            'services_count' => (int) ($outlet->services_count ?? 0),
        ];
    }

    protected function serializeOutletDetail(Outlet $outlet): array
    {
        return [
            'id' => $outlet->id,
            'name' => $outlet->name,
            'slug' => $outlet->slug,
            'address' => $outlet->address,
            'phone' => $outlet->phone,
            'pickup_fee' => (int) ($outlet->pickup_fee ?? 0),
            'delivery_fee' => (int) ($outlet->delivery_fee ?? 0),
            'has_qris' => filled($outlet->qris_image_path),
            'qris_image_url' => $outlet->qris_image_path ? url(Storage::url($outlet->qris_image_path)) : null,
            'qris_notes' => $outlet->qris_notes,
        ];
    }
}
