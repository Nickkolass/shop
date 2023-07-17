<?php

namespace Database\Seeders\Components;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class SeederProductService
{
    public function completionsOfProducts():void
    {
        $products = Product::with('productTypes.productImages:productType_id,file_path')->get('id');
        foreach ($products as $product) {
            $optionValues = $product->optionValues()->select('optionValues.id', 'option_id')->get()->groupBy('option_id')->map(function ($o) {
                return $o->pluck('id');
            });
            $optionValues = $optionValues->pop()->crossJoin(...$optionValues);

            foreach ($product->productTypes as $key => $productType) {
                if (rand(1, 100) < 30) $productType->liked()->attach(User::take(random_int(1, User::count()))->get());
                $productImage = $productType->productImages['0']->file_path;
                $previewImage = str_replace('product_images/' . $product->id, 'preview_images', $productImage);
                Storage::copy('public/' . $productImage, 'public/' . $previewImage);
                $productType->count == 0 ? $is_published = 0 : $is_published = 1;
                $productType->update(['is_published' => $is_published, 'preview_image' => $previewImage]);
                $productType->optionValues()->attach($optionValues[$key]);
            }
        }
        Storage::deleteDirectory('/public/fact/');
    }

}
