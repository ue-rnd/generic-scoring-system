<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Round extends Model
{
    protected $fillable = [
        'name',
        'description',
        'total_questions',
        'points_per_question',
        'max_score',
        'event_id',
        'organization_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'points_per_question' => 'decimal:2',
        'max_score' => 'decimal:2',
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

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
