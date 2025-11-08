<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
    ];

    // The team this member belongs to
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // The user who is a member
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
