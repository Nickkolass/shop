<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductIndexController extends Controller
{
    public function __invoke () {
        $products = Product::all();
        return view('product.index_product', compact('products'));   

    }
}
