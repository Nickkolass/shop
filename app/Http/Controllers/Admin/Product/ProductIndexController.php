<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductIndexController extends Controller
{
    public function __invoke()
    {
        $products = auth()->user()->role !== 'admin' ? Product::query() : auth()->user()->products();
        
        $products = $products->select('id', 'title', 'saler_id', 'category_id')->with(['category:id,title_rus', 'productTypes' => function($q) {
            $q->select('id', 'product_id', 'preview_image');
        }])->simplePaginate(4);

        return view('admin.product.index_product', compact('products'));
    }
}
