<?php

namespace App\Services\Admin\Product;

use App\Models\ProductImage;
use App\Models\ProductType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function productImages(ProductType $productType, array &$productImages, ?bool $isNewProduct = true): void
    {
        foreach ($productImages as &$productImage) {
            $filePath = $productImage->storePublicly('product_images/' . $productType->product_id, 'public');
            $productImage = [
                'productType_id' => $productType->id,
                'file_path' => $filePath,
                'size' => $productImage->getSize(),
            ];
        }
        if (!$isNewProduct) {
            if (!empty($productType->productImages)) ProductImage::whereIn('id', $productType->productImages->pluck('id'))->delete();
            ProductImage::insert($productImages);
        }
    }

    public function previewImage(UploadedFile $preview_image, int $product_id): string
    {
        return $preview_image->storePublicly('preview_images/' . $product_id, 'public');
    }

    public static function deleteImages(array|string $image_paths): void
    {
        Storage::disk('public')->delete($image_paths);
    }
}
