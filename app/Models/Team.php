<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'invite_code',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    // Team owner (User)
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Team members
    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    // Team activities
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // Team badges
    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class);
    }

    // Team challenges
    public function challenges(): HasMany
    {
        return $this->hasMany(TeamChallenge::class);
    }

    // Team tasks
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
