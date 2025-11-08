<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'badge_id',
        'team_id',
        'earned_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'earned_at' => 'datetime',
    ];

    // The user who earned the achievement
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // The badge that was earned
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    // The team context for the achievement
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
