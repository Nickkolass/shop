<?php

namespace App\Policies;

use App\Models\Option;
use App\Models\User;
use App\Policies\Trait\IsAdmin;
use Illuminate\Auth\Access\HandlesAuthorization;

class OptionPolicy
{
    use HandlesAuthorization, IsAdmin;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return false
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Option $option
     * @return false
     */
    public function view(User $user, Option $option): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return false
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Option $option
     * @return false
     */
    public function update(User $user, Option $option): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Option $option
     * @return false
     */
    public function delete(User $user, Option $option): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Option $option
     * @return false
     */
    public function restore(User $user, Option $option): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Option $option
     * @return false
     */
    public function forceDelete(User $user, Option $option): bool
    {
        return false;
    }
}
