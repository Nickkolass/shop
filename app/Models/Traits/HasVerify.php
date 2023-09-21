<?php

namespace App\Models\Traits;

use App\Models\User;

trait HasVerify
{
    public function verify(&$role)
    {
        if (!$role && $user = auth()->user()) {
            session([
                'jwt' => 'bearer ' . auth('api')->fromUser($user),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $role = User::getRoles()[$user->role]
                ],
            ]);
        }
    }
}
