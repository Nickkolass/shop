<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupIndexController extends Controller
{
    public function __invoke () {
        $groups = Group::all();
        return view('group.index_group', compact('groups'));   
    }
}
