<?php

namespace App\Policies;

use App\Models\Contestant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContestantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view contestants (filtered by organization scope)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contestant $contestant): bool
    {
        // Super admins can view all contestants
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Users can view contestants from their organizations
        if (!$contestant->organization_id) {
            return false;
        }

        return $user->belongsToOrganization($contestant->organization);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super admins can create contestants
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admins of at least one organization can create contestants
        return $user->adminOrganizations()->exists() || $user->headedOrganizations()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contestant $contestant): bool
    {
        // Super admins can update any contestant
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$contestant->organization_id) {
            return false;
        }

        // Organization admins can update contestants in their organization
        return $user->isAdminOfOrganization($contestant->organization);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contestant $contestant): bool
    {
        // Super admins can delete any contestant
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$contestant->organization_id) {
            return false;
        }

        // Organization admins can delete contestants in their organization
        return $user->isAdminOfOrganization($contestant->organization);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contestant $contestant): bool
    {
        return $this->delete($user, $contestant);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contestant $contestant): bool
    {
        return $user->isSuperAdmin();
    }
}
