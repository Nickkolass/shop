<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientIndexController extends Controller
{
    public function __invoke () {
        return view('client.main.index_main');   
    }
}
