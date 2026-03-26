<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.storefront')]
class PublicOrder extends Component
{
    use WithFileUploads;

    // Outlet
    public Outlet $outlet;
    public $orderLimitReached = false;

    // Customer Info
    public $customer_name = '';
    public $customer_phone = '';

    // Items Cart
    public $items = [];

    // Order Type
    public $order_type = 'regular';
    public $pickup_address = '';
    public $delivery_address = '';
    public $pickup_fee;
    public $delivery_fee;

    // Promo
    public $promo_code = '';
    public $applied_promo = null;
    public $discount_amount = 0;

    // Notes
    public $notes = '';
    public $payment_method = 'pay_at_store';
    public $payment_proof;
    public $payment_notes = '';

    // UI
    public $availableServices = [];
    public $siblingOutlets = []; // Other outlets of the same owner
    public $step = 1; // 0 = branch selection, 1 = select services, 2 = customer info, 3 = review

    public function mount(Outlet $outlet)
    {
        if ($outlet->status !== 'active') {
            abort(404, 'Outlet tidak ditemukan.');
        }

        $this->outlet = $outlet;

        // Check if the outlet owner can accept more orders
        $owner = $outlet->owner;
        if ($owner && !$owner->canCreateOrder()) {
            $this->orderLimitReached = true;
        }

        // Check if the owner has multiple active outlets (sibling branches)
        $this->siblingOutlets = Outlet::where('owner_id', $outlet->owner_id)
            ->where('status', 'active')
            ->get();

        $skipBranch = request()->query('skip_branch');

        // If owner has multiple outlets, start at branch selection (step 0)
        if ($this->siblingOutlets->count() > 1 && !$skipBranch) {
            $this->step = 0;
        } else {
            $this->step = 1;
        }

        $this->pickup_fee = $this->outlet->pickup_fee;
        $this->delivery_fee = $this->outlet->delivery_fee;

        $this->loadOutletServices();
        $this->addItem();
    }

    /**
     * Select a branch (sibling outlet) and proceed to step 1.
     */
    public function selectBranch($outletId)
    {
        $outlet = Outlet::where('id', $outletId)
            ->where('owner_id', $this->outlet->owner_id)
            ->where('status', 'active')
            ->firstOrFail();

        $this->outlet = $outlet;
        $this->loadOutletServices();
        $this->items = [];
        $this->addItem();
        $this->pickup_fee = $this->outlet->pickup_fee;
        $this->delivery_fee = $this->outlet->delivery_fee;
        $this->step = 1;
    }

    private function loadOutletServices()
    {
        $this->availableServices = Service::where('outlet_id', $this->outlet->id)
            ->where('status', 'active')
            ->get();
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
        if (count($this->items) === 0) {
            $this->addItem();
        }
        $this->calculateDiscount();
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

        $promo = Promo::where('code', strtoupper($this->promo_code))->first();
        
        if (!$promo) {
            session()->flash('promo_error', 'Kode promo tidak ditemukan.');
            return;
        }

        $subtotal = $this->getSubtotal();
        if (!$promo->isValid($this->outlet->id, $subtotal)) {
            session()->flash('promo_error', 'Promo tidak berlaku untuk order ini.');
            return;
        }

        $this->applied_promo = $promo;
        $this->discount_amount = $promo->calculateDiscount($subtotal);
        session()->flash('promo_success', "Promo {$promo->code} diterapkan!");
    }

    public function removePromo()
    {
        $this->applied_promo = null;
        $this->discount_amount = 0;
        $this->promo_code = '';
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

    public function nextStep()
    {
        if ($this->step === 1) {
            // Validate at least one item
            $hasItem = false;
            foreach ($this->items as $item) {
                if (!empty($item['service_id']) && $item['quantity'] > 0) {
                    $hasItem = true;
                    break;
                }
            }
            if (!$hasItem) {
                session()->flash('error', 'Pilih minimal satu layanan.');
                return;
            }
        }

        if ($this->step === 2) {
            $this->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'order_type' => 'required|in:regular,pickup,delivery',
            ], [
                'customer_name.required' => 'Nama wajib diisi.',
                'customer_phone.required' => 'Nomor HP wajib diisi.',
            ]);
        }

        $this->step = min($this->step + 1, 3);
    }

    public function backToBranchSelection()
    {
        if ($this->siblingOutlets->count() > 1) {
            $this->step = 0;
        }
    }

    public function prevStep()
    {
        $minStep = $this->siblingOutlets->count() > 1 ? 0 : 1;
        $this->step = max($this->step - 1, $minStep);
    }

    public function save()
    {
        // Check order limit before saving
        $owner = $this->outlet->owner;
        if ($owner && !$owner->canCreateOrder()) {
            $this->orderLimitReached = true;
            session()->flash('error', 'Maaf, outlet ini sedang tidak bisa menerima order baru.');
            return;
        }

        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|integer|min:1',
            'order_type' => 'required|in:regular,pickup,delivery',
            'payment_method' => 'required|in:pay_at_store,qris',
            'payment_proof' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'payment_notes' => 'nullable|string|max:1000',
        ]);

        if ($this->payment_method === 'qris' && !$this->outlet->qris_image_path) {
            session()->flash('error', 'QRIS belum tersedia untuk outlet yang dipilih. Silakan pilih bayar di toko.');
            return;
        }

        $paymentProofPath = $this->payment_proof?->store('payment-proofs', 'public');
        $paymentStatus = $this->payment_method === 'qris' && $paymentProofPath
            ? 'waiting_confirmation'
            : 'unpaid';

        $order = DB::transaction(function () use ($paymentProofPath, $paymentStatus) {
            // Find or create customer
            $ownerId = $this->outlet->owner_id;
            $ownerOutletIds = Outlet::where('owner_id', $ownerId)->pluck('id');
            
            $customer = Customer::whereIn('outlet_id', $ownerOutletIds)
                ->where('phone', $this->customer_phone)
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'outlet_id' => $this->outlet->id,
                    'name' => $this->customer_name,
                    'phone' => $this->customer_phone,
                ]);
            }

            // Generate invoice number
            $date = date('Ymd');
            $count = Order::whereDate('created_at', today())
                ->where('outlet_id', $this->outlet->id)
                ->count() + 1;
            $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV/{$date}/{$this->outlet->id}/{$sequence}";

            // Calculate totals
            $subtotal = 0;
            foreach ($this->items as $item) {
                $service = $this->availableServices->find($item['service_id']);
                $subtotal += $service->price * $item['quantity'];
            }

            $pickupFee = $this->order_type === 'pickup' ? $this->pickup_fee : 0;
            $deliveryFee = $this->order_type === 'delivery' ? $this->delivery_fee : 0;
            $totalPrice = $subtotal + $pickupFee + $deliveryFee - $this->discount_amount;

            $order = Order::create([
                'outlet_id' => $this->outlet->id,
                'customer_id' => $customer->id,
                'user_id' => null, // No cashier, customer self-order
                'invoice_number' => $invoiceNumber,
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'total_price' => max(0, $totalPrice),
                'notes' => $this->notes,
                'order_type' => $this->order_type,
                'pickup_address' => $this->order_type === 'pickup' ? $this->pickup_address : null,
                'delivery_address' => $this->order_type === 'delivery' ? $this->delivery_address : null,
                'pickup_fee' => $pickupFee,
                'delivery_fee' => $deliveryFee,
                'promo_id' => $this->applied_promo?->id,
                'discount_amount' => $this->discount_amount,
                'order_source' => 'customer',
                'payment_method' => $this->payment_method,
                'payment_proof_path' => $paymentProofPath,
                'payment_proof_original_name' => $this->payment_proof?->getClientOriginalName(),
                'payment_proof_uploaded_at' => $paymentProofPath ? now() : null,
                'payment_notes' => $this->payment_notes ?: null,
            ]);

            foreach ($this->items as $item) {
                if (empty($item['service_id'])) continue;
                $service = $this->availableServices->find($item['service_id']);
                OrderItem::create([
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
            $owner = $this->outlet->owner;
            if ($owner) {
                $owner->useOrderSlot();
            }

            return $order;
        });

        return redirect()->route('public.order.success', [
            'outlet' => $this->outlet->slug,
            'order' => $order->id,
        ]);
    }

    public function render()
    {
        $subtotal = $this->getSubtotal();
        $pickupFee = $this->order_type === 'pickup' ? $this->pickup_fee : 0;
        $deliveryFee = $this->order_type === 'delivery' ? $this->delivery_fee : 0;
        $total = $subtotal + $pickupFee + $deliveryFee - $this->discount_amount;

        return view('livewire.public-order', [
            'subtotal' => $subtotal,
            'total' => max(0, $total),
            'pickupFee' => $pickupFee,
            'deliveryFee' => $deliveryFee,
        ]);
    }
}
