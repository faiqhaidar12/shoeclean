<?php

namespace App\Livewire\Orders;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateOrder extends Component
{
    // Order Details
    public $customer_id;
    public $outlet_id;
    public $notes;
    
    // Pickup/Delivery
    public $order_type = 'regular';
    public $pickup_address = '';
    public $delivery_address = '';
    public $pickup_fee = 10000;
    public $delivery_fee = 10000;
    
    // Promo
    public $promo_code = '';
    public $applied_promo = null;
    public $discount_amount = 0;
    
    // Items Cart
    public $items = []; // Array of ['service_id' => int, 'quantity' => int, 'price' => int]

    // UI Helpers
    public $availableServices = [];
    public $availableCustomers = [];
    public $customerSearch = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user->isOwner()) {
            $this->outlet_id = session('current_outlet_id') ?? $user->ownedOutlets->first()?->id;
        } else {
            $this->outlet_id = $user->outlet_id;
        }

        if (!$this->outlet_id) {
            abort(403, 'No outlet selected.');
        }

        $this->availableServices = \App\Models\Service::where('outlet_id', $this->outlet_id)
            ->where('status', 'active')
            ->get();
        
        $this->addItem();
    }

    public function updatedCustomerSearch()
    {
        if (strlen($this->customerSearch) > 2) {
            $this->availableCustomers = \App\Models\Customer::search($this->customerSearch)->limit(5)->get();
        } else {
            $this->availableCustomers = [];
        }
    }

    public function selectCustomer($id, $name)
    {
        $this->customer_id = $id;
        $this->customerSearch = $name;
        $this->availableCustomers = [];
    }

    public function addItem()
    {
        $this->items[] = [
            'service_id' => '',
            'quantity' => 1,
            'price' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'service_id') {
            $index = $parts[0];
            $serviceId = $value;
            $service = $this->availableServices->find($serviceId);
            if ($service) {
                $this->items[$index]['price'] = $service->price;
            }
        }
        $this->calculateDiscount();
    }

    public function applyPromo()
    {
        $this->applied_promo = null;
        $this->discount_amount = 0;

        if (empty($this->promo_code)) return;

        $promo = \App\Models\Promo::where('code', strtoupper($this->promo_code))->first();
        
        if (!$promo) {
            session()->flash('promo_error', 'Kode promo tidak ditemukan.');
            return;
        }

        $subtotal = $this->getSubtotal();
        if (!$promo->isValid($this->outlet_id, $subtotal)) {
            session()->flash('promo_error', 'Promo tidak berlaku untuk order ini.');
            return;
        }

        $this->applied_promo = $promo;
        $this->discount_amount = $promo->calculateDiscount($subtotal);
        session()->flash('promo_success', "Promo {$promo->code} diterapkan!");
    }

    public function calculateDiscount()
    {
        if ($this->applied_promo) {
            $subtotal = $this->getSubtotal();
            $this->discount_amount = $this->applied_promo->calculateDiscount($subtotal);
        }
    }

    public function getSubtotal()
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            if (!empty($item['service_id']) && !empty($item['quantity'])) {
                $subtotal += (int) $item['price'] * (int) $item['quantity'];
            }
        }
        return $subtotal;
    }

    public function save()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'outlet_id' => 'required|exists:outlets,id',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|integer|min:1',
            'order_type' => 'required|in:regular,pickup,delivery',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
            $date = date('Ymd');
            $count = \App\Models\Order::whereDate('created_at', today())
                ->where('outlet_id', $this->outlet_id)
                ->count() + 1;
            $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV/{$date}/{$this->outlet_id}/{$sequence}";

            $subtotal = 0;
            foreach ($this->items as $item) {
                $service = $this->availableServices->find($item['service_id']);
                $subtotal += $service->price * $item['quantity'];
            }

            // Add fees
            $pickupFee = $this->order_type === 'pickup' ? $this->pickup_fee : 0;
            $deliveryFee = $this->order_type === 'delivery' ? $this->delivery_fee : 0;
            
            $totalPrice = $subtotal + $pickupFee + $deliveryFee - $this->discount_amount;

            $order = \App\Models\Order::create([
                'outlet_id' => $this->outlet_id,
                'customer_id' => $this->customer_id,
                'user_id' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_price' => max(0, $totalPrice),
                'notes' => $this->notes,
                'order_type' => $this->order_type,
                'pickup_address' => $this->order_type === 'pickup' ? $this->pickup_address : null,
                'delivery_address' => $this->order_type === 'delivery' ? $this->delivery_address : null,
                'pickup_fee' => $pickupFee,
                'delivery_fee' => $deliveryFee,
                'promo_id' => $this->applied_promo?->id,
                'discount_amount' => $this->discount_amount,
            ]);

            foreach ($this->items as $item) {
                $service = $this->availableServices->find($item['service_id']);
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'quantity' => $item['quantity'],
                    'unit' => $service->unit,
                    'price' => $service->price,
                    'total_price' => $service->price * $item['quantity'],
                ]);
            }

            // Increment promo used count
            if ($this->applied_promo) {
                $this->applied_promo->increment('used_count');
            }
        });

        return redirect()->route('orders.index');
    }

    public function render()
    {
        $subtotal = $this->getSubtotal();
        $pickupFee = $this->order_type === 'pickup' ? $this->pickup_fee : 0;
        $deliveryFee = $this->order_type === 'delivery' ? $this->delivery_fee : 0;
        $total = $subtotal + $pickupFee + $deliveryFee - $this->discount_amount;

        return view('livewire.orders.create-order', [
            'subtotal' => $subtotal,
            'total' => max(0, $total),
        ]);
    }
}

