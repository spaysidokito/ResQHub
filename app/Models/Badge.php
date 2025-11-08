<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'description',
        'icon',
        'color',
        'type',
        'criteria',
        'points_reward',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'points_reward' => 'integer',
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }
}
