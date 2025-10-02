<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventJudge extends Model
{
    protected $fillable = [
        'event_id',
        'judge_id',
        'status',
        'invited_at',
        'responded_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(Judge::class);
    }
}
