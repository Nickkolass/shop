<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Property;
use App\Models\Tag;

class ProductCreateController extends Controller
{
    public function __invoke () {
        
        $groups = auth()->user()->groups()->select('id','title')->get()->toArray();
        $tags = Tag::select('id','title')->get()->toArray();
        $categories = Category::select('id','title', 'title_rus')->with('properties:id,title')->get()->toArray();
        $optionValues = Option::with('optionValues:id,option_id,value')->select('id','title')->get()->mapWithKeys(function ($option) {
            return [$option->title => $option->optionValues];
        })->toArray();
        
        return view('admin.product.create_product', compact('tags', 'categories', 'groups', 'optionValues'));   
    }
}
