<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductTypePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Product $product)
    {
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ProductType $productType)
    {
        return $productType->product()->pluck('saler_id')->first() == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ProductType $productType)
    {
        return $productType->product()->pluck('saler_id')->first() == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ProductType $productType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ProductType $productType)
    {
        //
    }
}
