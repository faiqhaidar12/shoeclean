<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Services\SubscriptionService;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateOrder extends Component
{
    // Order Limit Info
    public $orderLimitInfo = [];
    // Order Details
    public $customer_id;
    public $outlet_id;
    public $notes;
    
    // Pickup/Delivery
    public $order_type = 'regular';
    public $pickup_address = '';
    public $delivery_address = '';
    public $pickup_fee;
    public $delivery_fee;
    
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

    // Quick Add Customer
    public $showQuickAdd = false;
    public $newCustomerName = '';
    public $newCustomerPhone = '';

    // Admin Multiple Outlets
    public $availableOutlets = [];
    public $fallbackSelectionId = '';

    public function mount()
    {
        $user = auth()->user();
        
        // Use the global session value if set
        if (session('current_outlet_id')) {
            $this->outlet_id = session('current_outlet_id');
        } else {
            // Fallbacks for direct access without session
            if ($user->isOwner()) {
                $outlets = $user->ownedOutlets;
                if ($outlets->count() > 1) {
                    $this->availableOutlets = $outlets;
                    $this->outlet_id = null; // Force modal
                } else {
                    $this->outlet_id = $outlets->first()?->id;
                }
            } elseif ($user->isAdmin()) {
                $owner = $user->getOwner();
                if ($owner) {
                    $outlets = $owner->ownedOutlets;
                    if ($outlets->count() > 1) {
                        $this->availableOutlets = $outlets;
                        $this->outlet_id = null; // Force modal
                    } else {
                        $this->outlet_id = $outlets->first()?->id ?? $user->outlet_id;
                    }
                } else {
                    $this->outlet_id = $user->outlet_id;
                }
            } else {
                // Staff
                $this->outlet_id = $user->outlet_id;
            }
        }

        // Only enforce missing outlet if they aren't forced to pick one
        if (!$this->outlet_id && count($this->availableOutlets) === 0) {
            abort(403, 'No outlet selected.');
        }

        // If outlet is set initially, load its data
        if ($this->outlet_id) {
            $this->loadOutletData();
        }

        // Check order limit
        $subscriptionService = new SubscriptionService();
        $this->orderLimitInfo = $subscriptionService->checkOrderLimit($user);

        if (!$this->orderLimitInfo['allowed']) {
            session()->flash('order_limit_reached', true);
            return redirect()->route('subscription');
        }
    }

    public function confirmFallbackOutlet()
    {
        $this->validate([
            'fallbackSelectionId' => 'required|exists:outlets,id'
        ], [
            'fallbackSelectionId.required' => 'Silakan pilih outlet terlebih dahulu.'
        ]);
        
        // Set the global session
        session(['current_outlet_id' => $this->fallbackSelectionId]);
        
        // Reload page to apply full context
        return redirect()->route('orders.create');
    }

    public function loadOutletData()
    {
        $this->availableServices = \App\Models\Service::where('outlet_id', $this->outlet_id)
            ->where('status', 'active')
            ->get();
        
        $outlet = \App\Models\Outlet::find($this->outlet_id);
        $this->pickup_fee = $outlet->pickup_fee;
        $this->delivery_fee = $outlet->delivery_fee;
        
        // Ensure there's at least one item slot
        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function updatedOutletId()
    {
        if ($this->outlet_id) {
            $this->loadOutletData();
            // Reset customer and items if outlet changes
            $this->clearCustomer();
            $this->items = [];
            $this->addItem();
            $this->applyPromo(); // Reset promo calculation
        }
    }

    public function updatedCustomerSearch()
    {
        // Reset customer selection when search changes
        $this->customer_id = null;
        $this->showQuickAdd = false;

        if (strlen($this->customerSearch) >= 2) {
            $this->availableCustomers = \App\Models\Customer::with('outlet')
                ->whereIn('outlet_id', auth()->user()->allOutletIds())
                ->search($this->customerSearch)
                ->limit(5)
                ->get();
        } else {
            $this->availableCustomers = [];
        }
    }

    public function selectCustomer($id, $name, $phone, $outletName = null)
    {
        $this->customer_id = $id;
        $this->customerSearch = $name . ' — ' . $phone . ($outletName ? " ({$outletName})" : "");
        $this->availableCustomers = [];
        $this->showQuickAdd = false;
    }

    public function toggleQuickAdd()
    {
        $this->showQuickAdd = !$this->showQuickAdd;
        if ($this->showQuickAdd) {
            // Pre-fill phone from search if it looks like a phone number
            if (preg_match('/^[0-9+]/', $this->customerSearch)) {
                $this->newCustomerPhone = $this->customerSearch;
            } else {
                $this->newCustomerName = $this->customerSearch;
            }
            $this->availableCustomers = [];
        }
    }

    public function quickAddCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'required|string|max:20|unique:customers,phone',
        ], [
            'newCustomerName.required' => 'Nama customer wajib diisi.',
            'newCustomerPhone.required' => 'No. telepon wajib diisi.',
            'newCustomerPhone.unique' => 'No. telepon sudah terdaftar.',
        ]);

        $customer = \App\Models\Customer::create([
            'outlet_id' => $this->outlet_id,
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
        ]);

        // Auto-select the new customer
        $this->customer_id = $customer->id;
        $this->customerSearch = $customer->name . ' — ' . $customer->phone;
        $this->showQuickAdd = false;
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->availableCustomers = [];
    }

    public function clearCustomer()
    {
        $this->customer_id = null;
        $this->customerSearch = '';
        $this->availableCustomers = [];
        $this->showQuickAdd = false;
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
        // Double-check order limit before saving
        $user = auth()->user();
        if (!$user->canCreateOrder()) {
            session()->flash('error', 'Kuota order habis. Silakan upgrade paket atau beli kuota tambahan.');
            return redirect()->route('subscription');
        }

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

            // Use order slot (deduct quota if applicable)
            auth()->user()->useOrderSlot();
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
