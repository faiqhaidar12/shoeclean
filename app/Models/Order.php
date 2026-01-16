<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'customer_id',
        'user_id',
        'invoice_number',
        'status',
        'payment_status',
        'total_price',
        'notes',
        'order_type',
        'pickup_address',
        'delivery_address',
        'pickup_fee',
        'delivery_fee',
        'promo_id',
        'discount_amount',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user() // Cashier
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
}
