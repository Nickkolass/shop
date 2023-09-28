<?php

namespace Database\Seeders\Components;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class SeederProductService
{
    public function completionsOfProducts(): void
    {
        Product::query()
            ->with('productTypes.productImages:productType_id,file_path')
            ->get('id')
            ->each(function (Product $product) {
                $optionValues = $product
                    ->optionValues()
                    ->select(['optionValues.id', 'option_id'])
                    ->get()
                    ->groupBy('option_id')
                    ->map(fn(Collection $optionValues) => $optionValues->pluck('id'));

                $optionValues = $optionValues->pop()->crossJoin(...$optionValues);

                foreach ($product->productTypes as $key => $productType) {
                    $productType->optionValues()->attach($optionValues[$key]);

                    if (rand(1, 100) < 30) {
                        $liked_ids = User::query()->inRandomOrder()->limit(rand(1, 3))->pluck('id');
                        $productType->liked()->attach($liked_ids);
                    }

                    $productImage = $productType->productImages->first()->file_path;
                    $previewImage = str_replace('product_images', 'preview_images', $productImage);
                    Storage::copy($productImage, $previewImage);
                    $is_published = $productType->count == 0 ? 0 : 1;

                    $productType->update(['is_published' => $is_published, 'preview_image' => $previewImage]);
                }
            });
    }
}
