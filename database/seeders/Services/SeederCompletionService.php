<?php

namespace Database\Seeders\Services;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SeederCompletionService
{
    public function completionUsers(): void
    {
        $users = User::query()
            ->take(3)
            ->pluck('id')
            ->transform(function (int $id, int $i) {
                $i++;
                return [
                    'id' => $id,
                    'email' => $i . '@mail.ru',
                    'password' => Hash::make((string)$i),
                    'role' => $i,
                ];
            })
            ->all();
        User::query()->upsert($users, 'id');
    }

    public function completionProducts(): void
    {
        $user_ids = User::query()->pluck('id');
        $productTypes = [];
        $products = Product::query()
            ->with([
                'ratingAndComments:id,product_id,rating,message',
                'optionValues:id,option_id',
                'productTypes' => function (Builder $q) {
                    $q->select('id', 'product_id', 'count')
                        ->with('productImages:productType_id,file_path');
                }])
            ->get('id') /** @phpstan-ignore-next-line */
            ->transform(function (Product $product) use (&$productTypes, $user_ids) {
                $optionValues = $product->optionValues->toArray();
                $optionValues = collect($optionValues)->groupBy('option_id');
                $optionValues = $optionValues->pop()->crossJoin(...$optionValues);

                $product->productTypes->each(function (ProductType $productType, int $key) use (&$productTypes, $optionValues, $user_ids) {
                    $productType->optionValues()->attach(array_column($optionValues[$key], 'id'));
                    if (rand(1, 100) < 30) {
                        $liked_ids = $user_ids->random(2);
                        $productType->liked()->attach($liked_ids);
                    }
                    $productImage = $productType->productImages->first()->file_path;
                    $previewImage = str_replace('product_images', 'preview_images', $productImage);
                    Storage::copy($productImage, $previewImage);
                    $is_published = $productType->count == 0 ? 0 : 1;

                    $productTypes[] = [
                        'id' => $productType->id,
                        'count_likes' => isset($liked_ids) ? $liked_ids->count() : 0,
                        'is_published' => $is_published,
                        'preview_image' => $previewImage,
                    ];
                });

                return [
                    'id' => $product->id,
                    'rating' => round($product->ratingAndComments->avg('rating') * 2) / 2,
                    'count_rating' => $product->ratingAndComments->count(),
                    'count_comments' => $product->ratingAndComments->whereNotNull('message')->count(),
                ];
            })->all();

        ProductType::query()->upsert($productTypes, 'id');
        Product::query()->upsert($products, 'id');
    }
}
