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
        $this->order = $order->load(['customer', 'items.service', 'user', 'outlet', 'payments']);
        
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
                $this->order->update(['payment_status' => 'paid']);
            }
        });
        
        // Refresh items
        $this->order->refresh();
    }

    public $snapToken = null;

    public function payOnline()
    {
        $midtransService = new \App\Services\MidtransService();
        $result = $midtransService->createSnapToken($this->order);
        $this->snapToken = $result['snap_token'];
        
        // Dispatch event to show Snap popup
        $this->dispatch('showSnapPopup', snapToken: $this->snapToken);
    }

    public function checkPaymentStatus()
    {
        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.orders.view-order', [
            'clientKey' => config('midtrans.client_key'),
        ]);
    }
}
