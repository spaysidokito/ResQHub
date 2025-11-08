<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'min_magnitude',
        'radius_km',
        'latitude',
        'longitude',
        'location_name',
        'email_alerts',
        'push_alerts',
        'sound_alerts',
        'alert_types',
    ];

    protected $casts = [
        'min_magnitude' => 'float',
        'radius_km' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'email_alerts' => 'boolean',
        'push_alerts' => 'boolean',
        'sound_alerts' => 'boolean',
        'alert_types' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
