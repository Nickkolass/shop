<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductIndexController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        if ($user->role == 'saler') {
            $products = Product::where('saler_id', $user->id)->get();
        } elseif ($user->role == 'admin') {
            if (str_contains($_SERVER['HTTP_REFERER'], '/users/')) {
                $products = Product::where('saler_id', str_replace('http://localhost:8876/users/', "", $_SERVER['HTTP_REFERER']))->get();
            } else $products = Product::all();
        }
        return view('product.index_product', compact('products'));
    }
}
