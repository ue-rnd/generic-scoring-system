<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventJudge extends Model
{
    protected $fillable = [
        'event_id',
        'judge_id',
        'judge_token',
        'judge_name',
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

    /**
     * Generate unique token on creation
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($eventJudge) {
            if (!$eventJudge->judge_token) {
                $eventJudge->judge_token = bin2hex(random_bytes(32));
            }
        });
    }

    /**
     * Get the judge scoring URL
     */
    public function getScoringUrlAttribute(): string
    {
        return url("/score/{$this->judge_token}");
    }

    /**
     * Get the judge's display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->judge_name ?? ($this->judge ? $this->judge->name : 'Anonymous Judge');
    }
}
