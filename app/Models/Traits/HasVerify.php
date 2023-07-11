<?php

namespace App\Models\Traits;

use App\Models\User;

trait HasVerify
{
    public function verify($role)
    {
        if (!$role)
            if ($user = auth()->user()) {
                session([
                    'user_role' => User::getRoles()[$user->role],
                    'jwt' => 'bearer '. auth('api')->fromUser($user),
                ]);
            }
            else abort(redirect('login'));
    }
}
