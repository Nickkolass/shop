<?php

namespace App\Services\Admin\Product;

use App\Models\ProductImage;
use App\Models\ProductType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function prepareOrCreateProductImages(ProductType $productType, array $images, ?bool $isNewProduct = true): array
    {
        foreach ($images as $image) $productImagesForInsert[] = [
            'productType_id' => $productType->id,
            'file_path' => $image->storePublicly('product_images/' . $productType->product_id),
            'size' => $image->getSize(),
        ];
        session()->push('image_paths_for_failed_create_product.productImages', array_column($productImagesForInsert, 'file_path'));

        if (!$isNewProduct) {
            $productType->productImages()->delete();
            ProductImage::insert($productImagesForInsert);
        }
        return $productImagesForInsert;
    }

    public function createPreviewImage(UploadedFile &$preview_image, int $product_id): void
    {
        $preview_image = $preview_image->storePublicly('preview_images/' . $product_id);
        session()->push('image_paths_for_failed_create_product.preview_images', $preview_image);
    }

    public static function deleteImages(array|string $image_paths): void
    {
        Storage::delete($image_paths);
    }
}
