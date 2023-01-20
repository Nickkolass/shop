<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductEditController extends Controller
{
    public function __invoke (Product $product) {
        $tags = DB::table('tags')->get();
        $colors = DB::table('colors')->get();
        $groups = DB::table('groups')->get();
        $categories = DB::table('categories')->get();
        $productTags = $product->tags()->get();
        $colorProducts = $product->colors()->get();
        $productImages = $product->productImages()->get('file_path');
        $product->preview_image = $product->preview_image;

        return view('product.edit_product', compact('tags', 'colors', 'groups', 'categories', 'product', 'productTags', 'colorProducts', 'productImages'));   
    }
}
