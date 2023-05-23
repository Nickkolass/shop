<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductIndexController extends Controller
{
    public function __invoke()
    {
        $this->authorize('viewAny', Product::class);

        $products = session('user_role') == 'admin' ? Product::query() : auth()->user()->products();
        
        $products = $products->select('id', 'title', 'saler_id', 'category_id')->with(['category:id,title_rus', 'productTypes' => function($q) {
            $q->select('id', 'product_id', 'preview_image');
        }])->latest()->simplePaginate(4);

        return view('admin.product.index', compact('products'));
    }
}
