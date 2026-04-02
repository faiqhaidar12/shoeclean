<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'key',
        'name',
        'subtitle',
        'price',
        'order_limit',
        'max_outlets',
        'quota',
        'description',
        'cta',
        'features',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_published' => 'boolean',
        ];
    }
}
