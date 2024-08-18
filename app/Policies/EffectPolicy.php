<?php

namespace App\Policies;

use App\Models\Effect;
use App\Models\User;

class EffectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view all findings');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Effect $effect): bool
    {
        return $user->can('view finding');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create effect');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Effect $effect): bool
    {
        return $user->can('update effect');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Effect $effect): bool
    {
        return $user->can('delete effect');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Effect $effect): bool
    {
        return $user->can('restore effect');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Effect $effect): bool
    {
        return $user->can('destroy effect');
    }
}
