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
        'event_judge_id',
        'criteria_id',
        'round_id',
        'question_number',
        'score',
        'is_correct',
        'comments',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_correct' => 'boolean',
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

    public function eventJudge(): BelongsTo
    {
        return $this->belongsTo(EventJudge::class);
    }
}
