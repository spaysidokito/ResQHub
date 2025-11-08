<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'earthquake_id',
        'disaster_id',
        'user_id',
        'session_id',
        'severity',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    protected $appends = ['title', 'message', 'type'];

    public function getTitleAttribute()
    {
        if ($this->disaster) {
            return $this->disaster->name;
        }
        if ($this->earthquake) {
            return 'Earthquake Alert';
        }
        return 'Alert';
    }

    public function getMessageAttribute()
    {
        if ($this->disaster) {
            return $this->disaster->description ?? 'A ' . $this->disaster->type . ' has been reported in ' . $this->disaster->location;
        }
        if ($this->earthquake) {
            return 'Magnitude ' . $this->earthquake->magnitude . ' earthquake detected near ' . $this->earthquake->location;
        }
        return 'New alert';
    }

    public function getTypeAttribute()
    {
        if ($this->disaster) {
            return $this->disaster->type;
        }
        if ($this->earthquake) {
            return 'earthquake';
        }
        return 'general';
    }

    public function earthquake(): BelongsTo
    {
        return $this->belongsTo(Earthquake::class);
    }

    public function disaster(): BelongsTo
    {
        return $this->belongsTo(Disaster::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
