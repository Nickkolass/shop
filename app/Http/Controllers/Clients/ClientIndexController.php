<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClientIndexController extends Controller
{
    public function __invoke () {
        $cients = Auth::user();
        return view('cient.index_cient', compact('cients'));   
    }
}
