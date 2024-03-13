<?php

namespace App\Policies;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecommendationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view finding');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Recommendation $recommendation): bool
    {
        return $user->can('view all findings');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('view finding');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recommendation $recommendation): bool
    {
        return $user->can('update finding');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recommendation $recommendation): bool
    {
        return $user->can('delete finding');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recommendation $recommendation): bool
    {
        return $user->can('restore finding');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recommendation $recommendation): bool
    {
        return $user->can('destroy finding');
    }
}
