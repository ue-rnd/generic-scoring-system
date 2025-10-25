<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'judging_type',
        'scoring_mode',
        'organization_id',
        'created_by_user_id',
        'start_date',
        'end_date',
        'is_active',
        'public_viewing_token',
        'public_viewing_config',
        'admin_token',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'public_viewing_config' => 'array',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
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

    /**
     * Generate tokens for the event if not exists
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($event) {
            if (!$event->public_viewing_token) {
                $event->public_viewing_token = bin2hex(random_bytes(32));
            }
            if (!$event->admin_token) {
                $event->admin_token = bin2hex(random_bytes(32));
            }
            // Set default public viewing config
            if (!$event->public_viewing_config) {
                $event->public_viewing_config = [
                    'show_rankings' => true,
                    'show_scores' => false,
                    'show_judge_names' => false,
                    'show_individual_scores' => false,
                    'show_criteria_breakdown' => false,
                    'show_round_breakdown' => false,
                    'show_judge_progress' => true,
                ];
            }
        });
    }

    /**
     * Get the public viewing URL
     */
    public function getPublicViewingUrlAttribute(): string
    {
        return url("/public/event/{$this->public_viewing_token}");
    }

    /**
     * Check if a specific viewing option is enabled
     */
    public function canShowPublic(string $option): bool
    {
        return $this->public_viewing_config[$option] ?? false;
    }

    /**
     * Check if event is quiz bee type (rounds-based)
     */
    public function isQuizBeeType(): bool
    {
        return $this->judging_type === 'rounds';
    }

    /**
     * Check if event is criteria-based (pageant)
     */
    public function isCriteriaBased(): bool
    {
        return $this->judging_type === 'criteria';
    }

    /**
     * Get the admin scoring URL for quiz bee events
     */
    public function getAdminScoringUrlAttribute(): string
    {
        return url("/admin/score/{$this->admin_token}");
    }
}
