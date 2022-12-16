<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientShowController extends Controller
{
    public function __invoke (User $user) {
        return view('user.show_user', compact('user'));   
    
    }
}
