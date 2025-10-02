<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'judging_type',
        'organizer_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function contestants(): HasMany
    {
        return $this->hasMany(Contestant::class);
    }

    public function criterias(): HasMany
    {
        return $this->hasMany(Criteria::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(Judge::class, 'event_judges')
                    ->withPivot(['status', 'invited_at', 'responded_at'])
                    ->withTimestamps();
    }

    public function eventJudges(): HasMany
    {
        return $this->hasMany(EventJudge::class);
    }
}
