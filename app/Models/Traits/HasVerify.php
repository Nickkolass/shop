<?php

namespace App\Models\Traits;

use App\Models\User;

trait HasVerify
{
    public function verify(?int &$role): void
    {
        if (!$role && $user = auth()->user()) {
            /** @var User $user */
            /** @noinspection PhpUndefinedMethodInspection */
            session([
                /** @phpstan-ignore-next-line */
                'jwt' => 'bearer ' . auth('api')->fromUser($user),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $role = $user->role,
                    'verify' => $user->hasVerifiedEmail()
                ],
            ]);
        }
    }
}
