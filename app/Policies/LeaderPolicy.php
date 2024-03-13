<?php

namespace App\Policies;

use App\Models\Leader;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view all leaders');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Leader $leader): bool
    {
        return $user->can('view leader');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create leader');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Leader $leader): bool
    {
        return $user->can('update leader');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Leader $leader): bool
    {
        return $user->can('delete leader');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Leader $leader): bool
    {
        return $user->can('restore leader');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Leader $leader): bool
    {
        return $user->can('destroy leader');
    }
}
