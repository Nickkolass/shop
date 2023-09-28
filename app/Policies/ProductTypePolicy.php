<?php

namespace App\Policies;

use App\Models\ProductType;
use App\Models\User;
use App\Policies\Trait\PreAuthChecks;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductTypePolicy
{
    use HandlesAuthorization, PreAuthChecks;

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return Response|bool
     */
    public function update(User $user, ProductType $productType)
    {
        return $productType->product()->pluck('saler_id')[0] == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return Response|bool
     */
    public function delete(User $user, ProductType $productType)
    {
        return $productType->product()->pluck('saler_id')[0] == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return Response|bool
     */
    public function restore(User $user, ProductType $productType)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return Response|bool
     */
    public function forceDelete(User $user, ProductType $productType)
    {
        return false;
    }
}
