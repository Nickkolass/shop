<?php

namespace App\Policies\Trait;

use App\Models\User;

trait IsAdmin
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }
}
