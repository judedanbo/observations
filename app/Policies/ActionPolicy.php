<?php

namespace App\Policies;

use App\Models\Action;
use App\Models\User;

class ActionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view all observations');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Action $action): bool
    {
        return $user->can('view observation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create action');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Action $action): bool
    {
        return $user->can('update action');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Action $action): bool
    {
        return $user->can('delete action');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Action $action): bool
    {
        return $user->can('restore action');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Action $action): bool
    {
        return $user->can('destroy action');
    }
}
