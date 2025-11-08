<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Earthquake extends Model
{
    protected $fillable = [
        'external_id',
        'magnitude',
        'location',
        'latitude',
        'longitude',
        'depth',
        'occurred_at',
        'source',
        'details',
    ];

    protected $casts = [
        'magnitude' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'depth' => 'integer',
        'occurred_at' => 'datetime',
        'details' => 'array',
    ];

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function getSeverityAttribute(): string
    {
        if ($this->magnitude >= 7.0) return 'critical';
        if ($this->magnitude >= 6.0) return 'high';
        if ($this->magnitude >= 5.0) return 'moderate';
        return 'low';
    }
}
