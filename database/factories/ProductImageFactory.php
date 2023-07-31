<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {


        $filesPath = Storage::files('/public/fact/');
        $filePath = $filesPath[random_int(0, count($filesPath)-1)];
        $productImagePath = str_replace('public/fact/', 'product_images/' . Product::latest('id')->pluck('id')['0']. '/', $filePath);

        Storage::move($filePath, 'public/'.$productImagePath);

        return [
            'file_path' => $productImagePath,
            'size' => random_int(1, 10000),
            'productType_id' => ProductType::take(1)->latest('id')->pluck('id')['0'],
        ];


    }
}
