<?php

namespace App\Policies;

use App\Models\Criteria;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CriteriaPolicy
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
    public function view(User $user, Criteria $criteria): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$criteria->organization_id) {
            return false;
        }

        return $user->belongsToOrganization($criteria->organization);
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
    public function update(User $user, Criteria $criteria): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$criteria->organization_id) {
            return false;
        }

        return $user->isAdminOfOrganization($criteria->organization);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Criteria $criteria): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$criteria->organization_id) {
            return false;
        }

        return $user->isAdminOfOrganization($criteria->organization);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Criteria $criteria): bool
    {
        return $this->delete($user, $criteria);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Criteria $criteria): bool
    {
        return $user->isSuperAdmin();
    }
}
