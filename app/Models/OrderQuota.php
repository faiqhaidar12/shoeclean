<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderQuota extends Model
{
    protected $fillable = [
        'user_id',
        'quota_total',
        'quota_used',
        'mayar_transaction_id',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'purchased_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function remainingQuota(): int
    {
        return max(0, $this->quota_total - $this->quota_used);
    }

    public function hasQuota(): bool
    {
        return $this->remainingQuota() > 0;
    }

    /**
     * Use one unit of quota. Returns true if successful.
     */
    public function useQuota(): bool
    {
        if (!$this->hasQuota()) return false;

        $this->increment('quota_used');
        return true;
    }
}
