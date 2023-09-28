<?php

namespace Database\Factories;

use App\Models\ProductImage;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory<ProductImage>
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
        $productType = ProductType::query()
            ->latest('id')
            ->select('id', 'product_id')
            ->first();

        $counter = Cache::get('imageCounter');
        Cache::increment('imageCounter');
        $filePath = Cache::get('factory')[$counter];

        $productImagePath = Storage::putFile(
            'product_images/' . $productType->product_id,
            new File('storage/app/testing/' . $filePath),
            'public'
        );

        /** @var string $productImagePath */
        return [
            'file_path' => $productImagePath,
            'size' => Storage::size($productImagePath),
            'productType_id' => $productType->id,
        ];
    }
}
