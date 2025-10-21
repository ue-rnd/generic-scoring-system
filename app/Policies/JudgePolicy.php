<?php

namespace App\Policies;

use App\Models\Judge;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JudgePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Judge $judge): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$judge->organization_id) {
            return false;
        }

        return $user->belongsToOrganization($judge->organization);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->adminOrganizations()->exists() || $user->headedOrganizations()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Judge $judge): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$judge->organization_id) {
            return false;
        }

        return $user->isAdminOfOrganization($judge->organization);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Judge $judge): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$judge->organization_id) {
            return false;
        }

        return $user->isAdminOfOrganization($judge->organization);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Judge $judge): bool
    {
        return $this->delete($user, $judge);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Judge $judge): bool
    {
        return $user->isSuperAdmin();
    }
}
