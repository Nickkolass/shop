<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function store(array $data): void
    {
        $password = Str::random(10);
        $data['password'] = Hash::make($password);

        DB::beginTransaction();
        $user = User::firstOrCreate(['email' => $data['email'], 'INN' => $data['INN']], $data);
        $user->password_generated = $password;
        event(new Registered($user));
        DB::commit();
    }

    public function passwordUpdate(User $user, string $new_password): void
    {
        $user->update(['password' => Hash::make($new_password)]);
    }
}
