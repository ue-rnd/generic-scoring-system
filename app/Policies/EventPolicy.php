<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view events (filtered by organization scope)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event): bool
    {
        // Super admins can view all events
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Users can view events from their organizations
        if (!$event->organization_id) {
            return false;
        }

        return $user->belongsToOrganization($event->organization);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super admins can create events
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Users who are admins of at least one organization can create events
        return $user->adminOrganizations()->exists() || $user->headedOrganizations()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        // Super admins can update any event
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$event->organization_id) {
            return false;
        }

        // Organization heads and admins can update events in their organization
        return $user->isAdminOfOrganization($event->organization);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        // Super admins can delete any event
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$event->organization_id) {
            return false;
        }

        // Organization heads and admins can delete events in their organization
        return $user->isAdminOfOrganization($event->organization);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        return $this->delete($user, $event);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can manage access (links, tokens, etc).
     */
    public function manageAccess(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }
}
