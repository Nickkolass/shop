<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserEditController extends Controller
{
    public function __invoke (User $user) {
        return view('user.edit_user', compact('user'));   

    }
}
