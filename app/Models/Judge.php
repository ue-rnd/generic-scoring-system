<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Judge extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'specialization',
        'organization_id',
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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Apply organization scope for non-super-admin users
        static::addGlobalScope('organization', function (Builder $builder) {
            if (Auth::check() && !Auth::user()->isSuperAdmin()) {
                $organizationIds = Auth::user()->accessibleOrganizationIds();
                $builder->whereIn('organization_id', $organizationIds);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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
