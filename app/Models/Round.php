<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    protected $fillable = [
        'name',
        'description',
        'total_questions',
        'points_per_question',
        'max_score',
        'event_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'points_per_question' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
