<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use App\Policies\Trait\PreAuthChecks;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization, PreAuthChecks;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Property $property)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Property $property)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Property $property)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Property $property)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Property $property)
    {
        return false;
    }
}
