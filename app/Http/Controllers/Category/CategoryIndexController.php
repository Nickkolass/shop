<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryIndexController extends Controller
{
    public function __invoke () {
        $categories = Category::all();
        return view('category.index_category', compact('categories'));   
    }
}
