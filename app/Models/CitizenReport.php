<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitizenReport extends Model
{
    protected $fillable = [
        'user_id',
        'disaster_id',
        'report_type',
        'type',
        'name',
        'description',
        'latitude',
        'longitude',
        'location',
        'severity',
        'status',
        'photo',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function disaster(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Disaster::class);
    }
}
