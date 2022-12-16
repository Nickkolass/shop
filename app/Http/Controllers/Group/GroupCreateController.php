<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupCreateController extends Controller
{
    public function __invoke () {
        return view('group.create_group');   
    }
}
