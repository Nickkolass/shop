<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductIndexController extends Controller
{
    public function __invoke(): View
    {
        $this->authorize('viewAny', Product::class);

        $products = session('user_role') == 'admin' ? Product::query() : auth()->user()->products();

        $products = $products->select('id', 'title', 'saler_id', 'category_id')->latest()
            ->with(['category:id,title_rus', 'productTypes:id,product_id,preview_image', 'ratingAndComments'])->simplePaginate(4);

        $products->map(function ($product) {
            $product->rating = round(($product->ratingAndComments->avg('rating') ?? 0)*2)/2;
            $product->countRating = $product->ratingAndComments->count();
        });

        return view('admin.product.index', compact('products'));
    }
}
