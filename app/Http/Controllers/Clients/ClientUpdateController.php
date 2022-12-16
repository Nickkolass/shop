<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientUpdateController extends Controller
{
    public function __invoke(UserUpdateRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $user->update($data);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
        return view('user.show_user', compact('user'));
    }
}
