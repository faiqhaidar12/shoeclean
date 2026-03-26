<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id', 'outlet_id', 'category', 'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'keluhan' => '😤 Keluhan',
            'ide' => '💡 Ide',
            'saran' => '📝 Saran',
            default => $this->category,
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'keluhan' => 'bg-red-100 text-red-600',
            'ide' => 'bg-amber-100 text-amber-600',
            'saran' => 'bg-blue-100 text-blue-600',
            default => 'bg-gray-100 text-gray-500',
        };
    }
}
