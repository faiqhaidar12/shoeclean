<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    protected $fillable = [
        'type', 'outlet_id', 'title', 'slug', 'description',
        'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function scopePlatform($query)
    {
        return $query->where('type', 'platform');
    }

    public function scopeOutletType($query)
    {
        return $query->where('type', 'outlet');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function averageRating(): ?float
    {
        $avg = $this->responses()
            ->join('survey_answers', 'survey_responses.id', '=', 'survey_answers.survey_response_id')
            ->whereNotNull('survey_answers.rating')
            ->avg('survey_answers.rating');

        return $avg ? round($avg, 1) : null;
    }
}
