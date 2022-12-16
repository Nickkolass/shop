<?php

namespace App\Http\Controllers\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagCreateController extends Controller
{
    public function __invoke () {
        return view('tag.create_tag');   

    }
}
