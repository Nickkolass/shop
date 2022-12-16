<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserCreateController extends Controller
{
    public function __invoke () {
        return view('user.create_user');   

    }
}
