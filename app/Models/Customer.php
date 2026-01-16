<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'outlet_id',
        'name',
        'phone',
        'address',
        'email',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        $query->where(function($q) use ($term) {
            $q->where('name', 'like', $term)
              ->orWhere('phone', 'like', $term);
        });
    }
}
