<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ListOrders extends Component
{
    public $search = '';
    public $status = '';

    /**
     * Mark order as paid with cash (quick action from list)
     */
    public function markPaid($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Authorization
        $user = auth()->user();
        if (!$user->isOwner() && $user->outlet_id !== $order->outlet_id) {
            abort(403);
        }

        // Only allow if not already paid
        if ($order->payment_status === 'paid') {
            return;
        }

        DB::transaction(function () use ($order) {
            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'method' => 'cash',
                'status' => 'success',
            ]);

            $order->update(['payment_status' => 'paid']);
        });
    }

    /**
     * Initiate Midtrans payment (redirect to view for Snap popup)
     */
    public function payOnline($orderId)
    {
        return redirect()->route('orders.view', $orderId)->with('initPayment', true);
    }

    public function render()
    {
        $user = auth()->user();
        $query = \App\Models\Order::query()
            ->with(['customer', 'outlet', 'items', 'payments'])
            ->latest();

        // Scope by Outlet
        if ($user->isOwner()) {
            if (session('current_outlet_id')) {
                 $query->where('outlet_id', session('current_outlet_id'));
            }
        } else {
            $query->where('outlet_id', $user->outlet_id);
        }

        // Optimized: Use subquery instead of whereHas for better search performance
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', $searchTerm)
                  ->orWhereIn('customer_id', function($sub) use ($searchTerm) {
                      $sub->select('id')
                          ->from('customers')
                          ->where('name', 'like', $searchTerm);
                  });
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.orders.list-orders', [
            'orders' => $query->paginate(10),
        ]);
    }
}

