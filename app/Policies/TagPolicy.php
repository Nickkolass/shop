<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use App\Policies\Trait\PreAuthChecks;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TagPolicy
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
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function view(User $user, Tag $tag)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function update(User $user, Tag $tag)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function delete(User $user, Tag $tag)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function restore(User $user, Tag $tag)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function forceDelete(User $user, Tag $tag)
    {
        return false;
    }
}
