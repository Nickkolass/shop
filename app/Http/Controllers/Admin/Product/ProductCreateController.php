<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Group;
use App\Models\Tag;

class ProductCreateController extends Controller
{
    public function __invoke () {
        
        if (auth()->user()->role == 'admin') {
            $groups = Group::select(['id', 'title'])->get()->toArray();
        } else {
            $groups = auth()->user()->groups()->select(['id','title'])->get()->toArray();
        }
        $tags = Tag::select(['id','title'])->get()->toArray();
        $colors = Color::select(['id','title'])->get()->toArray();
        $categories = Category::select(['id','title', 'title_rus'])->get()->toArray();
        return view('admin.product.create_product', compact('tags', 'colors', 'categories', 'groups'));   
    }
}
