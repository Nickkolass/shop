<?php

namespace App\Services\Admin;

use App\Dto\Admin\UserDto;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function store(UserDto $dto): void
    {
        $password_generated = Str::random(10);
        DB::beginTransaction();
        $user = User::query()
            ->firstOrCreate(
                ['email' => $dto->email, 'INN' => $dto->INN],
                (array)$dto + ['password' => Hash::make($password_generated)])
            ->setAttribute('password_generated', $password_generated);
        event(new Registered($user));
        DB::commit();
    }

    public function update(User $user, UserDto $dto): void
    {
        $user->update(array_filter((array)$dto));
    }

    public function passwordUpdate(User $user, string $new_password): void
    {
        $user->update(['password' => Hash::make($new_password)]);
    }
}
