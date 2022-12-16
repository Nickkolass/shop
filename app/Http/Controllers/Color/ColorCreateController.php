<?php

namespace App\Http\Controllers\Color;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ColorCreateController extends Controller
{
    public function __invoke () {
        return view('color.create_color');   
    }
}
