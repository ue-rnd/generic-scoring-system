<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Judge extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'specialization',
        'is_active',
    ];

    /**
     * Get the user associated with this judge
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_judges')
                    ->withPivot(['status', 'invited_at', 'responded_at'])
                    ->withTimestamps();
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function eventJudges(): HasMany
    {
        return $this->hasMany(EventJudge::class);
    }
}
