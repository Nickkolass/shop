<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductShowController extends Controller
{
    public function __invoke(Product $product)
    {
        $product->load(['tags:id,title', 'colors:id,title', 'group:id,title', 'category:id,title,title_rus', 'productImages:product_id,file_path'])->toArray();
        return view('admin.product.show_product', compact('product'));
    }
}
