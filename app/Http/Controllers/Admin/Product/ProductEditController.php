<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Color;
use App\Models\Group;
use App\Models\Tag;

class ProductEditController extends Controller
{
    public function __invoke (Product $product) {
        
        $product->load(['tags:id,title', 'colors:id,title', 'productImages:product_id,file_path'])->toArray();
        $tags = Tag::select(['id','title'])->get()->toArray();
        $colors = Color::select(['id','title'])->get()->toArray();
        $groups = auth()->user()->groups()->select(['id','title'])->get()->toArray();
        $categories = Category::select(['id','title', 'title_rus'])->get()->toArray();
        return view('admin.product.edit_product', compact('tags', 'colors', 'groups', 'categories', 'product'));   
    }
}
