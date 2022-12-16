<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Group;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductCreateController extends Controller
{
    public function __invoke () {
        
        
        $tags = Tag::all();
        $colors = Color::all();
        $categories = Category::all();
        $groups = Group::all();
        
        return view('product.create_product', compact('tags', 'colors', 'categories', 'groups'));   
    }
}
