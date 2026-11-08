<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'url',
        'ordering',
        'click_count',
        'is_active',
    ];

    protected $casts = [
        'click_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the link.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a click on this link (atomic increment).
     */
    public function recordClick(): void
    {
        $this->timestamps = false;
        $this->increment('click_count');
        $this->timestamps = true;
    }
}
