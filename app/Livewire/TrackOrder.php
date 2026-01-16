<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Livewire\Attributes\Layout;

#[Layout('layouts.track')]
class TrackOrder extends Component
{
    public $invoice = '';
    public $order = null;
    public $error = null;

    public function mount($invoice = null)
    {
        // Check query parameter first, then route parameter
        $invoice = request()->query('invoice', $invoice);
        
        if ($invoice) {
            $this->invoice = $invoice;
            $this->search();
        }
    }

    public function search()
    {
        $this->error = null;
        $this->order = null;

        if (empty($this->invoice)) {
            $this->error = 'Masukkan nomor invoice.';
            return;
        }

        $order = Order::where('invoice_number', $this->invoice)
            ->with(['customer', 'items.service', 'outlet'])
            ->first();

        if (!$order) {
            $this->error = 'Order tidak ditemukan.';
            return;
        }

        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.track-order');
    }
}
