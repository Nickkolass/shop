<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductIndexController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()->role == 'admin') {
            $groups = Product::whereNotNull('group_id')->with(['group:id,title', 'category:id,title_rus', 'group.products:id,group_id,preview_image', 'saler:id,name'])->groupBy('group_id');
            $prods = Product::whereNull('group_id')->with(['group:id,title', 'category:id,title_rus', 'group.products:id,group_id,preview_image', 'saler:id,name']);
            $products = $groups->union($prods)->orderBy('id')->paginate(5);
        } else {
            $groups = auth()->user()->products()->whereNotNull('group_id')->with(['group:id,title', 'category:id,title_rus', 'group.products:id,group_id,saler_id,preview_image', 'saler:id,name'])->groupBy('group_id');
            $prods = auth()->user()->products()->whereNull('group_id')->with(['group:id,title', 'category:id,title_rus', 'group.products:id,group_id,saler_id,preview_image', 'saler:id,name']);
            $products = $groups->union($prods)->orderBy('id')->paginate(5);
        }
        return view('admin.product.index_product', compact('products'));
    }
}
