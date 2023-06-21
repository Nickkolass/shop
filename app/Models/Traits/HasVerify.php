<?php

namespace App\Models\Traits;

use App\Models\User;

trait HasVerify
{
    public function verify($role)
    {
        if (!$role)
            if (auth()->check()) session(['user_role' => User::getRoles()[auth()->user()->role]]);
            else abort(redirect('login'));
    }
}
