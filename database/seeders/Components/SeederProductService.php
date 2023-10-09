<?php

namespace Database\Seeders\Components;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SeederProductService
{
    public function completionsOfProducts(): void
    {
        $products_update = $productTypes_update = [];
        Product::query()
            ->with([
                'productTypes.productImages:productType_id,file_path',
                'ratingAndComments:id,product_id,rating,message'
            ])
            ->get('id')
            ->each(function (Product $product) use (&$products_update, &$productTypes_update) {
                $optionValues = $product
                    ->optionValues()
                    ->pluck('option_id', 'optionValues.id')
                    ->mapToGroups(fn($v, $k) => [$v => $k]);
                $optionValues = $optionValues->pop()->crossJoin(...$optionValues);

                $product->productTypes->each(function (ProductType $productType, int $key) use (&$productTypes_update, $optionValues) {
                    $productType->optionValues()->attach($optionValues[$key]);

                    if (rand(1, 100) < 30) {
                        $liked_ids = User::query()->inRandomOrder()->limit(rand(1, 3))->pluck('id');
                        $productType->liked()->attach($liked_ids);
                    }
                    $productImage = $productType->productImages->first()->file_path;
                    $previewImage = str_replace('product_images', 'preview_images', $productImage);
                    Storage::copy($productImage, $previewImage);
                    $is_published = $productType->count == 0 ? 0 : 1;

                    $productTypes_update[] = [
                        'id' => $productType->id,
                        'count_likes' => isset($liked_ids) ? $liked_ids->count() : 0,
                        'is_published' => $is_published,
                        'preview_image' => $previewImage,
                    ];
                });
                $products_update[] = [
                    'id' => $product->id,
                    'rating' => round($product->ratingAndComments->avg('rating') * 2) / 2,
                    'count_rating' => $product->ratingAndComments->count(),
                    'count_comments' => $product->ratingAndComments->whereNotNull('message')->count(),
                ];
            });
        ProductType::query()->upsert($productTypes_update, 'id');
        Product::query()->upsert($products_update, 'id');
    }
}
