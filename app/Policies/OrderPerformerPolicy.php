<?php

namespace App\Policies;

use App\Models\OrderPerformer;
use App\Models\User;
use App\Policies\Trait\IsAdmin;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPerformerPolicy
{
    use HandlesAuthorization, IsAdmin;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isSaler();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return bool
     */
    public function view(User $user, OrderPerformer $orderPerformer): bool
    {
        return $orderPerformer->saler_id == $user->id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return bool
     */
    public function update(User $user, OrderPerformer $orderPerformer): bool
    {
        return $orderPerformer->saler_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @param bool $canceler_is_client
     * @return bool
     */
    public function delete(User $user, OrderPerformer $orderPerformer, bool $canceler_is_client = false): bool
    {
        $id = $canceler_is_client ? $orderPerformer->user_id : $orderPerformer->saler_id;
        return $user->id == $id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return false
     */
    public function restore(User $user, OrderPerformer $orderPerformer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return false
     */
    public function forceDelete(User $user, OrderPerformer $orderPerformer): bool
    {
        return false;
    }
}
