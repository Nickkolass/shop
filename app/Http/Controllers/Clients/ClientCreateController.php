<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientCreateController extends Controller
{
    public function __invoke () {
        return view('user.create_user');   

    }
}
