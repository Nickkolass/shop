<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductShowController extends Controller
{
    public function __invoke(Product $product)
    {
        $tags = $product->tags()->get();
        $colors = $product->colors()->get();
        $group = $product->group()->get()['0']->title;
        $category = $product->category()->get()['0']->title;
        $images = $product->productImages()->get();
        $product->preview_image = '/storage/' . $product->preview_image;

        return view('product.show_product', compact('product', 'tags', 'colors', 'group', 'category', 'images'));
    }
}
