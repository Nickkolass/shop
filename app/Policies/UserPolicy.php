<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Trait\PreAuthChecks;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization, PreAuthChecks;

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
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return $user->id == $model->id;
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
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return false
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return false
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can like productTypes.
     *
     * @param User $user
     * @return true
     */
    public function product(User $user): bool
    {
        return true;
    }
}
