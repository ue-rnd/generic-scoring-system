<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    protected $fillable = [
        'event_id',
        'contestant_id',
        'judge_id',
        'criteria_id',
        'round_id',
        'score',
        'comments',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function contestant(): BelongsTo
    {
        return $this->belongsTo(Contestant::class);
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(Judge::class);
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }
}
