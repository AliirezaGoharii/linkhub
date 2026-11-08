<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'views',
        'clicks',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'views' => 'integer',
        'clicks' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
