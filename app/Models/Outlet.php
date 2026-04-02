<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($outlet) {
            if (empty($outlet->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($outlet->name);
                $slug = $baseSlug ?: 'outlet';
                
                // Ensure uniqueness
                $originalSlug = $slug;
                $counter = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                $outlet->slug = $slug;
            }
        });
    }

    protected $fillable = [
        'owner_id', 'name', 'slug', 'address', 'phone', 'status',
        'province_id', 'province_name', 'city_id', 'city_name', 'district_id', 'district_name',
        'latitude', 'longitude',
        'pickup_fee', 'delivery_fee', 'qris_image_path', 'qris_image_original_name', 'qris_notes'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
