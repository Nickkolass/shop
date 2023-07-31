<?php

namespace App\Services\Admin;

use App\Mail\MailRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class UserService
{
    public function store(array $data): ?string
    {
        if(empty($data['password'])) $data['password'] = $password = Str::random(10);
        $data['password'] = Hash::make($data['password']);

        DB::beginTransaction();
        try {

            $user = User::firstOrCreate(['email' => $data['email'], 'INN' => $data['INN']], $data);
            event(new Registered($user));
            Mail::to($user->email)->send(new MailRegistered($password ?? null));

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function password(User $user, string $new_password): void
    {
        $user->update(['password' => Hash::make($new_password)]);
    }
}
