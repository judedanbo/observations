<?php

namespace App\Policies;

use App\Models\Parliament;
use App\Models\User;

class ParliamentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view all parliament recommendations');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Parliament $parliament): bool
    {
        return $user->can('view parliament recommendation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create parliament recommendation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Parliament $parliament): bool
    {
        return $user->can('update parliament recommendation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Parliament $parliament): bool
    {
        return $user->can('delete parliament recommendation');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Parliament $parliament): bool
    {
        return $user->can('restore parliament recommendation');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Parliament $parliament): bool
    {
        return $user->can('destroy parliament recommendation');
    }
}
