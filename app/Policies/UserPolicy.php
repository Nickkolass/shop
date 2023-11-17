<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
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
        return $user->id == $model->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
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
        return $user->id == $model->id || $user->isAdmin();
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
        return $user->id == $model->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can action different password.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function password(User $user, User $model): bool
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can action different card.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function card(User $user, User $model): bool
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can action to product.
     *
     * @param User $user
     * @return bool
     */
    public function product(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can role action.
     *
     * @param User $user
     * @param int $role
     * @return bool
     */
    public function role(User $user, int $role): bool
    {
        return $user->role <= $role;
    }

    /**
     * Determine whether the user is verified.
     *
     * @param User $user
     * @return bool
     */
    public function verify(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }
}
