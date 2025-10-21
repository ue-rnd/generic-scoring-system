<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Get all organizations this user belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get organizations where this user is the head.
     */
    public function headedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'head_user_id');
    }

    /**
     * Get organizations where this user is an admin.
     */
    public function adminOrganizations(): BelongsToMany
    {
        return $this->organizations()->wherePivot('role', 'admin');
    }

    /**
     * Get the events created by this user
     */
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by_user_id');
    }

    /**
     * Get the judge profile for this user
     */
    public function judgeProfile()
    {
        return $this->hasOne(Judge::class, 'email', 'email');
    }

    /**
     * Get the scores given by this user as a judge
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'judge_id');
    }

    /**
     * Check if this user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    /**
     * Check if this user is an admin of any organization.
     */
    public function isAdminOfOrganization(Organization $organization): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        if ($organization->head_user_id === $this->id) {
            return true;
        }

        return $this->organizations()
            ->where('organizations.id', $organization->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Check if this user belongs to an organization.
     */
    public function belongsToOrganization(Organization $organization): bool
    {
        return $this->organizations()->where('organization_id', $organization->id)->exists();
    }

    /**
     * Get all organization IDs this user can access.
     */
    public function accessibleOrganizationIds(): array
    {
        if ($this->is_super_admin) {
            return Organization::pluck('id')->toArray();
        }

        return $this->organizations()->pluck('organizations.id')->toArray();
    }
}
