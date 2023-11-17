<?php

namespace App\Jobs\Scheduler;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\PropertyValue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use stdClass;

class DBCleanUpdateJob
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function handle(): void
    {
        PropertyValue::query()->doesntHave('products')->delete();

        $productTypes_update = ProductType::query()
            ->select('id')
            ->withCount('liked')
            ->toBase()
            ->get()
            ->transform(function (stdClass $productType) {
                return[
                    'id' => $productType->id,
                    'count_likes' => $productType->liked_count,
                ];
            })->all();

        $products_update = Product::query()
            ->with('ratingAndComments:id,product_id,rating,message')
            ->select('id')
            ->get()/** @phpstan-ignore-next-line */
            ->transform(function (Product $product) {
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

