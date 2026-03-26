<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListOrders extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    
    // Outlet Interceptor Modal
    public $showOutletSelectionModal = false;
    public $availableOutletsForSelection = [];
    public $selectedInterceptorOutletId = '';

    /**
     * Intercept order creation to enforce outlet selection if needed.
     */
    public function startCreateOrder()
    {
        $user = auth()->user();
        
        // Owners with multiple outlets that haven't picked a global session outlet
        // OR Admins with multiple outlets
        if (($user->isOwner() && !session('current_outlet_id')) || $user->isAdmin()) {
            $owner = $user->getOwner();
            if ($owner && $owner->ownedOutlets->count() > 1) {
                // For Admins (or global-unselected Owners), force a specific choice for this order
                $this->availableOutletsForSelection = $owner->ownedOutlets;
                $this->showOutletSelectionModal = true;
                return;
            }
        }
        
        // Default directly to creation if single outlet or globally selected
        return redirect()->route('orders.create');
    }

    public function confirmOutletSelection()
    {
        $this->validate([
            'selectedInterceptorOutletId' => 'required|exists:outlets,id'
        ], [
            'selectedInterceptorOutletId.required' => 'Silakan pilih outlet terlebih dahulu.'
        ]);
        
        // Set the global session so the entire app context updates
        session(['current_outlet_id' => $this->selectedInterceptorOutletId]);
        
        return redirect()->route('orders.create');
    }

    public function closeModal()
    {
        $this->showOutletSelectionModal = false;
        $this->selectedInterceptorOutletId = '';
    }

    /**
     * Mark order as paid with cash (quick action from list)
     */
    public function markPaid($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Authorization
        $user = auth()->user();
        if ($user->isOwner()) {
            if (!$user->ownedOutlets->contains('id', $order->outlet_id)) {
                abort(403);
            }
        } elseif ($user->outlet_id !== $order->outlet_id) {
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
            } else {
                $query->whereIn('outlet_id', $user->ownedOutlets->pluck('id'));
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

