<?php

namespace App\Policies;

use App\Models\District;
use App\Models\User;

class DistrictPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view all districts');
    }

    public function view(User $user, District $district): bool
    {
        return $user->can('view district');
    }

    public function create(User $user): bool
    {
        return $user->can('create district');
    }

    public function update(User $user, District $district): bool
    {
        return $user->can('update district');
    }

    public function delete(User $user, District $district): bool
    {
        return $user->can('delete district');
    }

    public function restore(User $user, District $district): bool
    {
        return $user->can('restore district');
    }

    public function forceDelete(User $user, District $district): bool
    {
        return $user->can('destroy district');
    }
}
