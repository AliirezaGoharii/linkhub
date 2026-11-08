<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'bio',
        'email',
        'password',
        'profile_photo_path',
        'banner_path',
        'theme_color',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_views' => 'integer',
    ];

    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->orderBy('ordering');
    }

    public function dailyStats(): HasMany
    {
        return $this->hasMany(DailyStat::class);
    }

    /**
     * Total number of clicks across all links.
     */
    public function totalLinkClicks(): int
    {
        return (int) $this->links()->sum('click_count');
    }
}
