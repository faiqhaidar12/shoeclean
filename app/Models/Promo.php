<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'code',
        'name',
        'type',
        'value',
        'min_order',
        'max_discount',
        'max_uses',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function isValid($outletId = null, $orderTotal = 0): bool
    {
        // Check active
        if (!$this->is_active) return false;

        // Check dates
        $today = now()->startOfDay();
        if ($today->lt($this->start_date) || $today->gt($this->end_date)) return false;

        // Check uses
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;

        // Check minimum order
        if ($orderTotal < $this->min_order) return false;

        // Check outlet
        if ($this->outlet_id && $this->outlet_id !== $outletId) return false;

        return true;
    }

    public function calculateDiscount($orderTotal): int
    {
        if ($this->type === 'percentage') {
            $discount = ($orderTotal * $this->value) / 100;
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
            return (int) $discount;
        }

        return $this->value;
    }
}
