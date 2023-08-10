<?php

namespace Database\Seeders\Components;

use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use App\Models\Product;
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
                    ->select('optionValues.id', 'option_id')
                    ->get()
                    ->groupBy('option_id')
                    ->map(fn (Collection $optionValues) => $optionValues->pluck('id'));

                $optionValues = $optionValues->pop()->crossJoin(...$optionValues);

                foreach ($product->productTypes as $key => $productType) {
                    $productType->optionValues()->attach($optionValues[$key]);

                    if (rand(1, 100) < 30) $productType->liked()->attach(User::inRandomOrder()->limit(random_int(1, 3))->pluck('id'));

                    $productImage = $productType->productImages['2']->file_path;
                    $previewImage = str_replace('product_images', 'preview_images', $productImage);
                    Storage::copy('public/' . $productImage, 'public/' . $previewImage);
                    $productType->count == 0 ? $is_published = 0 : $is_published = 1;

                    $productType->update(['is_published' => $is_published, 'preview_image' => $previewImage]);
                }
            });
        Storage::deleteDirectory('/public/fact/');
    }
}
