<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserIndexController extends Controller
{
    public function __invoke () {
        $users = User::all();
        return view('user.index_user', compact('users'));   
    }
}
