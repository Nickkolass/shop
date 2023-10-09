<?php

namespace App\Policies;

use App\Models\ProductType;
use App\Models\User;
use App\Policies\Trait\PreAuthChecks;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductTypePolicy
{
    use HandlesAuthorization, PreAuthChecks;

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return bool
     */
    public function update(User $user, ProductType $productType): bool
    {
        return $productType->product()->pluck('saler_id')[0] == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return bool
     */
    public function delete(User $user, ProductType $productType): bool
    {
        return $productType->product()->pluck('saler_id')[0] == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return false
     */
    public function restore(User $user, ProductType $productType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param ProductType $productType
     * @return false
     */
    public function forceDelete(User $user, ProductType $productType): bool
    {
        return false;
    }
}
