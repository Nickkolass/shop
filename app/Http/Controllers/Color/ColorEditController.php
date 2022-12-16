<?php

namespace App\Http\Controllers\Color;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorEditController extends Controller
{
    public function __invoke (Color $color) {
        return view('color.edit_color', compact('color'));   

    }
}
