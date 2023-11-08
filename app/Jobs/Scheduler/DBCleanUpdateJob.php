<?php

namespace App\Jobs\Scheduler;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductTypeUserLike;
use App\Models\PropertyValue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DBCleanUpdateJob
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function handle(): void
    {
        PropertyValue::query()->doesntHave('products')->delete();

        $productTypes_update = [];
        $productTypeUserLike = ProductTypeUserLike::query()
            ->selectRaw('productType_id, COUNT(productType_id) as count')
            ->groupBy('productType_id')
            ->pluck('count', 'productType_id');

        $products_update = Product::query()
            ->with('ratingAndComments:id,product_id,rating,message', 'productTypes:id,product_id')
            ->get('id')/** @phpstan-ignore-next-line */
            ->transform(function (Product $product) use (&$productTypes_update, $productTypeUserLike) {
                $product->productTypes->each(function (ProductType $productType) use (&$productTypes_update, $productTypeUserLike) {
                    if (isset($productTypeUserLike[$productType->id])) {
                        $productTypes_update[] = [
                            'id' => $productType->id,
                            'count_likes' => $productTypeUserLike[$productType->id],
                        ];
                    }
                });
                return [
                    'id' => $product->id,
                    'rating' => round($product->ratingAndComments->avg('rating') * 2) / 2,
                    'count_rating' => $product->ratingAndComments->count(),
                    'count_comments' => $product->ratingAndComments->whereNotNull('message')->count(),
                ];
            })->all();
        Product::query()->upsert($products_update, 'id');
        ProductType::query()->upsert($productTypes_update, 'id');
    }
}

