<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManagementController
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::query()
            ->with(['customer:id,name,phone', 'outlet:id,name,slug', 'payments:id,order_id,status'])
            ->whereIn('outlet_id', $this->visibleOutletIds($request))
            ->latest();

        if ($search = trim((string) $request->string('search'))) {
            $searchTerm = '%' . $search . '%';

            $query->where(function ($builder) use ($searchTerm) {
                $builder->where('invoice_number', 'like', $searchTerm)
                    ->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                        $customerQuery->where('name', 'like', $searchTerm)
                            ->orWhere('phone', 'like', $searchTerm);
                    });
            });
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $orders = $query
            ->paginate(10)
            ->through(fn (Order $order) => $this->transformOrderListItem($order));

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        $order->load([
            'customer:id,name,phone,address,email',
            'outlet:id,name,slug,address,phone,latitude,longitude,qris_image_path,qris_notes',
            'items.service:id,name,unit',
            'payments',
            'paymentVerifier:id,name',
        ]);

        return response()->json([
            'order' => $this->transformOrderDetail($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        $payload = $request->validate([
            'status' => ['required', 'in:pending,processing,ready,picked_up,completed,cancelled'],
        ]);

        $order->update([
            'status' => $payload['status'],
        ]);

        return response()->json([
            'message' => 'Status order berhasil diperbarui.',
            'order' => $this->transformOrderDetail($order->fresh()->load([
                'customer:id,name,phone,address,email',
                'outlet:id,name,slug,address,phone,latitude,longitude,qris_image_path,qris_notes',
                'items.service:id,name,unit',
                'payments',
                'paymentVerifier:id,name',
            ])),
        ]);
    }

    public function markPaid(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->payment_status !== 'paid') {
            DB::transaction(function () use ($order) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_price,
                    'method' => 'cash',
                    'status' => 'success',
                ]);

                $order->update([
                    'payment_status' => 'paid',
                    'payment_verified_at' => now(),
                    'payment_verified_by' => auth()->id(),
                ]);
            });
        }

        return response()->json([
            'message' => 'Pembayaran berhasil ditandai lunas.',
            'order' => $this->transformOrderDetail($order->fresh()->load([
                'customer:id,name,phone,address,email',
                'outlet:id,name,slug,address,phone,latitude,longitude,qris_image_path,qris_notes',
                'items.service:id,name,unit',
                'payments',
                'paymentVerifier:id,name',
            ])),
        ]);
    }

    public function verifyPayment(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->payment_proof_path && $order->payment_status !== 'paid') {
            DB::transaction(function () use ($order) {
                $existingPayment = Payment::query()
                    ->where('order_id', $order->id)
                    ->where('status', 'success')
                    ->whereIn('method', ['qris', 'manual_transfer'])
                    ->first();

                if (! $existingPayment) {
                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $order->total_price,
                        'method' => $order->payment_method === 'qris' ? 'qris' : 'manual_transfer',
                        'status' => 'success',
                        'payload' => [
                            'verified_by' => auth()->id(),
                            'verified_at' => now()->toDateTimeString(),
                            'source' => 'next-dashboard-verification',
                        ],
                    ]);
                }

                $order->update([
                    'payment_status' => 'paid',
                    'payment_verified_at' => now(),
                    'payment_verified_by' => auth()->id(),
                ]);
            });
        }

        return response()->json([
            'message' => 'Pembayaran berhasil diverifikasi.',
            'order' => $this->transformOrderDetail($order->fresh()->load([
                'customer:id,name,phone,address,email',
                'outlet:id,name,slug,address,phone,latitude,longitude,qris_image_path,qris_notes',
                'items.service:id,name,unit',
                'payments',
                'paymentVerifier:id,name',
            ])),
        ]);
    }

    protected function transformOrderListItem(Order $order): array
    {
        $hasPendingPayment = $order->payments->contains(fn ($payment) => $payment->status === 'pending');
        $hasFailedPayment = $order->payments->contains(fn ($payment) => $payment->status === 'failed');

        return [
            'id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'created_at' => optional($order->created_at)->toIso8601String(),
            'status' => $order->status,
            'status_label' => $this->statusLabel($order->status),
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->paymentStatusLabel(),
            'payment_state' => $hasPendingPayment ? 'pending'
                : ($hasFailedPayment ? 'failed' : $order->payment_status),
            'has_payment_proof' => (bool) $order->payment_proof_path,
            'total_price' => $order->total_price,
            'created_at' => optional($order->created_at)->toIso8601String(),
            'customer' => $order->customer ? [
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
            ] : null,
            'outlet' => $order->outlet ? [
                'name' => $order->outlet->name,
                'slug' => $order->outlet->slug,
            ] : null,
        ];
    }

    protected function transformOrderDetail(Order $order): array
    {
        return [
            'id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'status' => $order->status,
            'status_label' => $this->statusLabel($order->status),
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->paymentStatusLabel(),
            'payment_method' => $order->payment_method,
            'payment_method_label' => $order->paymentMethodLabel(),
            'total_price' => $order->total_price,
            'notes' => $order->notes,
            'order_type' => $order->order_type,
            'pickup_address' => $order->pickup_address,
            'pickup_latitude' => $order->pickup_latitude,
            'pickup_longitude' => $order->pickup_longitude,
            'delivery_address' => $order->delivery_address,
            'delivery_latitude' => $order->delivery_latitude,
            'delivery_longitude' => $order->delivery_longitude,
            'discount_amount' => $order->discount_amount,
            'payment_notes' => $order->payment_notes,
            'payment_verified_at' => optional($order->payment_verified_at)->toIso8601String(),
            'payment_proof_url' => $order->payment_proof_path ? url('storage/' . ltrim($order->payment_proof_path, '/')) : null,
            'payment_summary' => [
                'has_payment_proof' => (bool) $order->payment_proof_path,
                'can_mark_paid' => $order->payment_status !== 'paid',
                'can_verify_payment' => (bool) $order->payment_proof_path && $order->payment_status !== 'paid',
                'successful_payments_total' => (int) $order->payments->where('status', 'success')->sum('amount'),
            ],
            'customer' => $order->customer ? [
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
                'address' => $order->customer->address,
                'email' => $order->customer->email,
            ] : null,
            'outlet' => $order->outlet ? [
                'name' => $order->outlet->name,
                'slug' => $order->outlet->slug,
                'address' => $order->outlet->address,
                'phone' => $order->outlet->phone,
                'latitude' => $order->outlet->latitude,
                'longitude' => $order->outlet->longitude,
                'qris_image_url' => $order->outlet->qris_image_path ? url('storage/' . ltrim($order->outlet->qris_image_path, '/')) : null,
                'qris_notes' => $order->outlet->qris_notes,
            ] : null,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'service_name' => $item->service?->name,
                    'quantity' => $item->quantity,
                    'unit' => $item->service?->unit,
                    'price' => $item->price,
                    'total_price' => $item->total_price,
                ];
            })->values(),
            'payments' => $order->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'method' => $payment->method,
                    'method_label' => $this->paymentMethodLabel($payment->method),
                    'status' => $payment->status,
                    'status_label' => $this->paymentRecordStatusLabel($payment->status),
                    'created_at' => optional($payment->created_at)->toIso8601String(),
                ];
            })->values(),
            'payment_verifier' => $order->paymentVerifier ? [
                'name' => $order->paymentVerifier->name,
            ] : null,
        ];
    }

    protected function visibleOutletIds(Request $request): array
    {
        $user = $request->user();

        if ($user->isOwner()) {
            if (session('current_outlet_id')) {
                return [(int) session('current_outlet_id')];
            }

            return $user->ownedOutlets->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $user->outlet_id ? [(int) $user->outlet_id] : [];
    }

    protected function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless(in_array($order->outlet_id, $this->visibleOutletIds($request), true), 403);
    }

    protected function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'ready' => 'Selesai cuci',
            'picked_up' => 'Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    protected function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'cash' => 'Cash',
            'qris' => 'QRIS',
            'manual_transfer' => 'Transfer Manual',
            default => ucfirst((string) $method ?: 'manual'),
        };
    }

    protected function paymentRecordStatusLabel(string $status): string
    {
        return match ($status) {
            'success' => 'Berhasil',
            'pending' => 'Menunggu',
            'failed' => 'Gagal',
            default => ucfirst($status),
        };
    }
}
