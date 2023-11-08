<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use App\Policies\Trait\IsAdmin;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization, IsAdmin;

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
     * @param Property $property
     * @return false
     */
    public function view(User $user, Property $property): bool
    {
        return false;
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
     * @param Property $property
     * @return false
     */
    public function update(User $user, Property $property): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Property $property
     * @return false
     */
    public function delete(User $user, Property $property): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Property $property
     * @return false
     */
    public function restore(User $user, Property $property): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Property $property
     * @return false
     */
    public function forceDelete(User $user, Property $property): bool
    {
        return false;
    }
}
