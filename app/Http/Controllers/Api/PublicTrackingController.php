<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicTrackingController extends Controller
{
    public function show(Request $request, string $invoice = ''): JsonResponse
    {
        $invoice = trim($invoice !== '' ? $invoice : (string) $request->query('invoice', ''));

        if ($invoice === '') {
            return response()->json([
                'message' => 'Masukkan nomor invoice.',
            ], 422);
        }

        $order = Order::query()
            ->where('invoice_number', $invoice)
            ->with(['customer:id,name,phone', 'items.service:id,name', 'outlet:id,name,address,phone,qris_image_path,latitude,longitude'])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan.',
            ], 404);
        }

        $statuses = ['pending', 'processing', 'ready', 'picked_up'];
        $currentIndex = array_search($order->status, $statuses, true);

        if ($order->status === 'completed') {
            $currentIndex = 3;
        }

        if ($order->status === 'cancelled') {
            $currentIndex = -1;
        }

        return response()->json([
            'order' => [
                'id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'status' => $order->status,
                'status_label' => $this->statusLabel($order->status),
                'payment_status' => $order->payment_status,
                'payment_status_label' => $order->paymentStatusLabel(),
                'payment_method' => $order->payment_method,
                'total_price' => (int) $order->total_price,
                'notes' => $order->notes,
                'order_type' => $order->order_type,
                'pickup_address' => $order->pickup_address,
                'pickup_latitude' => $order->pickup_latitude,
                'pickup_longitude' => $order->pickup_longitude,
                'pickup_distance_km' => $order->pickup_distance_km,
                'delivery_address' => $order->delivery_address,
                'delivery_latitude' => $order->delivery_latitude,
                'delivery_longitude' => $order->delivery_longitude,
                'delivery_distance_km' => $order->delivery_distance_km,
                'customer' => $order->customer ? [
                    'name' => $order->customer->name,
                    'phone' => $order->customer->phone,
                ] : null,
                'outlet' => $order->outlet ? [
                    'name' => $order->outlet->name,
                    'address' => $order->outlet->address,
                    'phone' => $order->outlet->phone,
                    'latitude' => $order->outlet->latitude,
                    'longitude' => $order->outlet->longitude,
                    'qris_image_url' => $order->outlet->qris_image_path
                        ? url(Storage::url($order->outlet->qris_image_path))
                        : null,
                ] : null,
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'service_name' => $item->service?->name,
                    'quantity' => (int) $item->quantity,
                    'unit' => $item->unit,
                    'price' => (int) $item->price,
                    'total_price' => (int) $item->total_price,
                ])->values(),
                'timeline' => collect($statuses)->map(function (string $status, int $index) use ($currentIndex) {
                    return [
                        'key' => $status,
                        'label' => $this->timelineLabel($status),
                        'description' => $this->timelineDescription($status),
                        'is_active' => $currentIndex >= $index,
                        'is_current' => $currentIndex === $index,
                    ];
                })->values(),
            ],
        ]);
    }

    protected function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Diproses',
            'processing' => 'Sedang Diproses',
            'ready' => 'Siap Diambil',
            'picked_up' => 'Sudah Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    }

    protected function timelineLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Pesanan Masuk',
            'processing' => 'Sedang Diproses',
            'ready' => 'Siap Diambil',
            'picked_up' => 'Sudah Selesai',
            default => ucfirst($status),
        };
    }

    protected function timelineDescription(string $status): string
    {
        return match ($status) {
            'pending' => 'Pesanan Anda sudah diterima outlet dan masuk ke antrean pengerjaan.',
            'processing' => 'Tim outlet sedang mengerjakan pesanan Anda sesuai layanan yang dipilih.',
            'ready' => 'Pesanan sudah selesai dikerjakan dan siap diambil atau dikirim sesuai kesepakatan.',
            'picked_up' => 'Pesanan sudah selesai dan diterima customer.',
            default => '',
        };
    }
}
