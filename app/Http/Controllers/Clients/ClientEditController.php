<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientEditController extends Controller
{
    public function __invoke (User $user) {
        return view('user.edit_user', compact('user'));   

    }
}
