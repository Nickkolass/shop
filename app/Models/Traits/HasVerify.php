<?php

namespace App\Models\Traits;

use App\Models\User;

trait HasVerify
{
    public function verify()
    {
        if (!session()->has('user_role')) {
            auth()->check() ? session(['user_role' => User::getRoles()[auth()->user()->role]]) : $this->middleware('auth');
        }
    }
}
