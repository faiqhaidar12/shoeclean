<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'billable_type',
        'billable_id',
        'gateway',
        'kind',
        'plan_key',
        'merchant_order_id',
        'reference',
        'payment_method',
        'amount',
        'fee',
        'status_code',
        'result_code',
        'status_message',
        'product_detail',
        'customer_email',
        'checkout_payload',
        'callback_payload',
        'status_payload',
        'expires_at',
        'paid_at',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'checkout_payload' => 'array',
            'callback_payload' => 'array',
            'status_payload' => 'array',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }
}
