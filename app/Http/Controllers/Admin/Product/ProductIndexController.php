<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductIndexController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()->role == 'admin') {
            $groups = Product::whereNotNull('group_id')->with('saler:id,name')->groupBy('group_id');
            $prods = Product::whereNull('group_id')->with('saler:id,name');
        } else {
            $groups = auth()->user()->products()->whereNotNull('group_id')->groupBy('group_id');
            $prods = auth()->user()->products()->whereNull('group_id');
        }
        $products = $groups->union($prods)->with(['group:id,title', 'category:id,title_rus', 'group.products:id,group_id,preview_image'])->orderBy('id')->paginate(8);

        return view('admin.product.index_product', compact('products'));
    }
}
