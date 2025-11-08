<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Teams this user owns
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    // Teams this user is a member of
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    // Achievements (badges) this user has earned
    public function achievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    // Points transactions for this user
    public function points(): HasMany
    {
        return $this->hasMany(UserPoint::class);
    }

    // Tasks completed by this user
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Badges earned by this user (convenience, via achievements)
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_achievements');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'title',
        'title_description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
