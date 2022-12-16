<?php

namespace App\Http\Controllers\Color;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorIndexController extends Controller
{
    public function __invoke () {
        $colors = Color::all();
        return view('color.index_color', compact('colors'));   
    }
}
