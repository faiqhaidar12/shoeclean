<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Outlet;
use Livewire\Attributes\Layout;

#[Layout('layouts.storefront')]
class OrderSuccess extends Component
{
    public Order $order;
    public Outlet $outlet;

    public function mount(Outlet $outlet, Order $order)
    {
        // Verify order belongs to this outlet
        if ($order->outlet_id !== $outlet->id) {
            abort(404);
        }

        $this->outlet = $outlet;
        $this->order = $order->load(['items.service', 'customer', 'promo', 'paymentVerifier']);
    }

    public function render()
    {
        return view('livewire.order-success');
    }
}
