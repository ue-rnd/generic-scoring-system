<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'head_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization head user.
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    /**
     * Get all users in this organization.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get admin users in this organization.
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * Get member users in this organization.
     */
    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'member');
    }

    /**
     * Get all events in this organization.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get all contestants in this organization.
     */
    public function contestants(): HasMany
    {
        return $this->hasMany(Contestant::class);
    }

    /**
     * Get all judges in this organization.
     */
    public function judges(): HasMany
    {
        return $this->hasMany(Judge::class);
    }

    /**
     * Get all criterias in this organization.
     */
    public function criterias(): HasMany
    {
        return $this->hasMany(Criteria::class);
    }

    /**
     * Get all rounds in this organization.
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    /**
     * Check if a user is an admin in this organization.
     */
    public function isAdmin(User $user): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        if ($this->head_user_id === $user->id) {
            return true;
        }

        return $this->users()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Check if a user is a member of this organization.
     */
    public function hasMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
