<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapToken(Order $order): array
    {
        $orderId = 'ORDER-' . $order->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->customer->name,
                'phone' => $order->customer->phone,
                'email' => filter_var($order->customer->email, FILTER_VALIDATE_EMAIL) 
                    ? $order->customer->email 
                    : 'customer-' . $order->customer->id . '@shoeclean.test',
            ],
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id' => $item->service_id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->service->name ?? 'Service',
                ];
            })->toArray(),
        ];

        $snapToken = Snap::getSnapToken($params);

        // Create pending payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'method' => 'midtrans',
            'status' => 'pending',
            'external_id' => $orderId,
        ]);

        return [
            'snap_token' => $snapToken,
            'payment_id' => $payment->id,
            'order_id' => $orderId,
        ];
    }

    public function handleNotification(array $notification): void
    {
        $transactionStatus = $notification['transaction_status'] ?? null;
        $orderId = $notification['order_id'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;

        if (!$orderId) {
            return;
        }

        $payment = Payment::where('external_id', $orderId)->first();
        if (!$payment) {
            return;
        }

        // Determine payment status
        if ($transactionStatus === 'capture') {
            $status = ($fraudStatus === 'accept') ? 'success' : 'pending';
        } elseif ($transactionStatus === 'settlement') {
            $status = 'success';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $status = 'failed';
        } elseif ($transactionStatus === 'pending') {
            $status = 'pending';
        } else {
            $status = 'pending';
        }

        $payment->update([
            'status' => $status,
            'payload' => $notification,
        ]);

        // Update order payment status if success
        if ($status === 'success') {
            $totalPaid = Payment::where('order_id', $payment->order_id)
                ->where('status', 'success')
                ->sum('amount');

            if ($totalPaid >= $payment->order->total_price) {
                $payment->order->update(['payment_status' => 'paid']);
            }
        }
    }
}
