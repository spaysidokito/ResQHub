<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disaster extends Model
{
    protected $fillable = [
        'external_id',
        'type',
        'name',
        'description',
        'latitude',
        'longitude',
        'wind_speed',
        'wind_direction',
        'movement_direction',
        'movement_speed',
        'pressure',
        'last_updated',
        'location',
        'country',
        'severity',
        'status',
        'details',
        'started_at',
        'ended_at',
        'source',
        'is_verified',
        'photo',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'wind_speed' => 'integer',
        'movement_speed' => 'float',
        'pressure' => 'integer',
        'details' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_updated' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'flood' => '🌊',
            'typhoon' => '🌀',
            'fire' => '🔥',
            'earthquake' => '🌍',
            default => '⚠️',
        };
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => '#dc2626',
            'high' => '#f97316',
            'moderate' => '#eab308',
            'low' => '#10b981',
            default => '#6b7280',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
