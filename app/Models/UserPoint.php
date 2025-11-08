<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'points',
        'type',
        'reason',
        'source_id',
        'source_type',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    // The user who earned or spent the points
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // The team context for the points
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
