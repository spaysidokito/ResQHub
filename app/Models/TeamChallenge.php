<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamChallenge extends Model
{
    protected $fillable = [
        'team_id',
        'title',
        'description',
        'type',
        'target_points',
        'reward_points',
        'requirements',
        'start_date',
        'end_date',
        'status',
        'progress',
        'completed_at',
    ];

    protected $casts = [
        'requirements' => 'array',
        'target_points' => 'integer',
        'reward_points' => 'integer',
        'progress' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // The team this challenge belongs to
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
