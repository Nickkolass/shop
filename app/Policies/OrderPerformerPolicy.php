<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPerformerPolicy
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
        return true;
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
        return $user->isAdmin() || $orderPerformer->saler_id == $user->id;
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
        return $user->isAdmin() || $orderPerformer->saler_id == $user->id;
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
        return $user->isAdmin() || $user->id == $id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return bool
     */
    public function restore(User $user, OrderPerformer $orderPerformer): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return bool
     */
    public function forceDelete(User $user, OrderPerformer $orderPerformer): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the saler can permanently payout.
     *
     * @param User $user
     * @param OrderPerformer $orderPerformer
     * @return bool
     */
    public function payout(User $user, OrderPerformer $orderPerformer): bool
    {
        return
            !$orderPerformer->payout_id
            && !$orderPerformer->trashed()
            && ($user->id == $orderPerformer->saler_id || ($user->isAdmin() && app()->environment('local')))
            && $orderPerformer->status == OrderPerformer::STATUS_RECEIVED
            && ($orderPerformer->order->status ?? $orderPerformer->order()->pluck('status')[0]) == Order::STATUS_COMPLETED;
    }
}
