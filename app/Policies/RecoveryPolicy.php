<?php

namespace App\Policies;

use App\Models\Recovery;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecoveryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view all recoveries');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Recovery $recovery): bool
    {
        return $user->can('view recovery');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create recovery');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recovery $recovery): bool
    {
        return $user->can('update recovery');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recovery $recovery): bool
    {
        return $user->can('delete recovery');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recovery $recovery): bool
    {
        return $user->can('restore recovery');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recovery $recovery): bool
    {
        return $user->can('destroy recovery');
    }
}
