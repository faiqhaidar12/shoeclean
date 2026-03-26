<?php

namespace App\Livewire\Orders;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ViewOrder extends Component
{
    public \App\Models\Order $order;

    public function mount(\App\Models\Order $order)
    {
        $this->order = $order->load(['customer', 'items.service', 'user', 'outlet', 'payments', 'paymentVerifier']);
        
        // Authorization
        $user = auth()->user();
        if (!$user->isOwner() && $user->outlet_id !== $this->order->outlet_id) {
            abort(403);
        }
    }

    public function updateStatus($status)
    {
        // Allowed statuses: pending -> processing -> ready -> completed / picked_up
        // Simplified flow: just set status.
        $this->order->update(['status' => $status]);
        
        if ($status === 'completed' && $this->order->payment_status === 'unpaid') {
            // Optional: Auto-mark paid? Or separate action?
            // Let's keep separate for now.
        }
    }

    public function markPaid()
    {
        // Simple manual pay full
        $this->payCash($this->order->total_price);
    }

    public function payCash($amount)
    {
        // DB Transaction for safety
        \Illuminate\Support\Facades\DB::transaction(function () use ($amount) {
            \App\Models\Payment::create([
                'order_id' => $this->order->id,
                'amount' => $amount,
                'method' => 'cash',
                'status' => 'success',
            ]);

            // Check total paid
            $totalPaid = \App\Models\Payment::where('order_id', $this->order->id)
                ->where('status', 'success')
                ->sum('amount');

            if ($totalPaid >= $this->order->total_price) {
                $this->order->update([
                    'payment_status' => 'paid',
                    'payment_verified_at' => now(),
                    'payment_verified_by' => auth()->id(),
                ]);
            }
        });
        
        // Refresh items
        $this->order->refresh()->load(['payments', 'paymentVerifier']);
    }

    public function verifyPayment()
    {
        if (!$this->order->payment_proof_path || $this->order->payment_status === 'paid') {
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () {
            $existingPayment = \App\Models\Payment::where('order_id', $this->order->id)
                ->where('status', 'success')
                ->whereIn('method', ['qris', 'manual_transfer'])
                ->first();

            if (!$existingPayment) {
                \App\Models\Payment::create([
                    'order_id' => $this->order->id,
                    'amount' => $this->order->total_price,
                    'method' => $this->order->payment_method === 'qris' ? 'qris' : 'manual_transfer',
                    'status' => 'success',
                    'payload' => [
                        'verified_by' => auth()->id(),
                        'verified_at' => now()->toDateTimeString(),
                        'source' => 'manual-proof-verification',
                    ],
                ]);
            }

            $this->order->update([
                'payment_status' => 'paid',
                'payment_verified_at' => now(),
                'payment_verified_by' => auth()->id(),
            ]);
        });

        $this->order->refresh()->load(['payments', 'paymentVerifier']);
    }

    public function resetPaymentToUnpaid()
    {
        if ($this->order->payment_status === 'paid') {
            return;
        }

        $this->order->update([
            'payment_status' => 'unpaid',
            'payment_verified_at' => null,
            'payment_verified_by' => null,
        ]);

        $this->order->refresh()->load(['payments', 'paymentVerifier']);
    }

    public function render()
    {
        return view('livewire.orders.view-order');
    }
}
