<?php

namespace App\Http\Controllers\Admin\Product;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductShowController extends Controller
{
    public function __invoke(Product $product)
    {
        $this->authorize('view', $product);

        $product->load([
            'category:id,title,title_rus', 'propertyValues.property:id,title', 'optionValues.option:id,title', 'tags:id,title',
            'ratingAndComments' => function ($q) {
                $q->with(['user:id,name', 'commentImages:comment_id,file_path']);
            },
            'productTypes' => function ($q) {
                $q->select('id', 'product_id', 'count', 'price', 'is_published', 'preview_image')->withCount('liked')
                    ->with('productImages:productType_id,file_path', 'optionValues.option:id,title');
            }
        ]);

        $product->rating = round(($product->ratingAndComments->avg('rating') ?? 0) * 2) / 2;
        $product->countRating = $product->ratingAndComments->count();
        $product->countComments = $product->ratingAndComments->pluck('message')->filter()->count();
        Method::optionsAndProperties($product);
        $product->productTypes->map(function ($productType) {
            Method::valuesToKeys($productType, 'optionValues');
        });

        return view('admin.product.show', compact('product'));
    }
}
