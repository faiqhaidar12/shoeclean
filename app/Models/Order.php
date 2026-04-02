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
        'pickup_latitude',
        'pickup_longitude',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'pickup_fee',
        'delivery_fee',
        'promo_id',
        'discount_amount',
        'order_source',
        'payment_method',
        'payment_proof_path',
        'payment_proof_original_name',
        'payment_proof_uploaded_at',
        'payment_verified_at',
        'payment_verified_by',
        'payment_notes',
    ];

    protected $casts = [
        'payment_proof_uploaded_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'pickup_latitude' => 'float',
        'pickup_longitude' => 'float',
        'delivery_latitude' => 'float',
        'delivery_longitude' => 'float',
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

    public function paymentVerifier()
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'waiting_confirmation' => 'Menunggu Verifikasi',
            default => 'Belum Lunas',
        };
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'qris' => 'QRIS Outlet',
            'manual_transfer' => 'Transfer Manual',
            'pay_at_store' => 'Bayar di Toko',
            default => 'Belum Dipilih',
        };
    }

    public function isWaitingPaymentConfirmation(): bool
    {
        return $this->payment_status === 'waiting_confirmation';
    }
}
