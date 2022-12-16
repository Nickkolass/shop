<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientDeleteController extends Controller
{
    public function __invoke (User $user) {
        $user->delete();
        return redirect()->route('home');
    }
}
