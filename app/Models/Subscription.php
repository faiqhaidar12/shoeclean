<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'status',
        'payment_gateway',
        'mayar_transaction_id',
        'mayar_member_id',
        'gateway_transaction_id',
        'gateway_reference',
        'started_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentTransaction(): MorphOne
    {
        return $this->morphOne(PaymentTransaction::class, 'billable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                  ->whereNotNull('expires_at')
                  ->where('expires_at', '<=', now());
            });
    }

    // Helpers
    public function isActive(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro' && $this->isActive();
    }

    public function isBusiness(): bool
    {
        return $this->plan === 'business' && $this->isActive();
    }

    public function daysRemaining(): ?int
    {
        if (!$this->expires_at) return null;
        if ($this->expires_at->isPast()) return 0;
        return (int) now()->diffInDays($this->expires_at);
    }
}
