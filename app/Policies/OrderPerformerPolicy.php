<?php

namespace App\Policies;

use App\Models\OrderPerformer;
use App\Models\User;
use App\Policies\Trait\PreAuthChecks;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OrderPerformerPolicy
{
    use HandlesAuthorization, PreAuthChecks;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->isSaler();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return Response|bool
     */
    public function view(User $user, OrderPerformer $orderPerformer)
    {
        return $orderPerformer->saler_id == $user->id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return Response|bool
     */
    public function update(User $user, OrderPerformer $orderPerformer)
    {
        return $orderPerformer->saler_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return Response|bool
     */
    public function delete(User $user, OrderPerformer $orderPerformer)
    {
        return $orderPerformer->saler_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return Response|bool
     */
    public function restore(User $user, OrderPerformer $orderPerformer)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return Response|bool
     */
    public function forceDelete(User $user, OrderPerformer $orderPerformer)
    {
        return false;
    }
}
