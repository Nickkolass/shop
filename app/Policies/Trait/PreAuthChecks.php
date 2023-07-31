<?php


namespace App\Policies\Trait;

use App\Models\User;

trait PreAuthChecks
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }
}
