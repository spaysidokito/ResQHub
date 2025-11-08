<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'activity_id',
        'user_id',
        'team_id',
        'title',
        'description',
        'status',
        'points_earned',
        'completed_at',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'points_earned' => 'integer',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // The activity this task belongs to
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    // The user who completed the task
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // The team this task belongs to
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // The user who verified the task
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
