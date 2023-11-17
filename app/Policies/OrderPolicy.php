<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return true
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function view(User $user, Order $order): bool
    {
        return $order->user_id == $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return true
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function update(User $user, Order $order): bool
    {
        return $order->user_id == $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function delete(User $user, Order $order): bool
    {
        return !$order->refund_id
                && ($user->id == $order->user_id || $user->isAdmin())
                && ($order->status == Order::STATUS_WAIT_PAYMENT || $order->status == Order::STATUS_PAID)
                && !$order->orderPerformers()->where('status', OrderPerformer::STATUS_SENT)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently refund.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function pay(User $user, Order $order): bool
    {
        return !$order->pay_id && $order->status == Order::STATUS_WAIT_PAYMENT;
    }

    /**
     * Determine whether the user can permanently refund.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function refund(User $user, Order $order): bool
    {
        return
            !$order->refund_id
            && $order->pay_id
            && ($user->id == $order->user_id || ($user->isAdmin() && app()->environment('local')))
            && ($order->status == Order::STATUS_COMPLETED || ($order->trashed() && $order->status == Order::STATUS_CANCELED))
            && $order->orderPerformers()->onlyTrashed()->exists();
    }
}
