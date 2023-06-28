<?php

namespace App\Services\Product;

use App\Models\ProductImage;
use App\Models\ProductType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class ImageService
{
    public function productImages(ProductType $productType, &$productImages, ?bool $isNewProduct = true)
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
            if (!empty($productType->productImages)) {
                $this->deleteImages($productType->productImages->pluck('file_path')->all());
                ProductImage::whereIn('id', $productType->productImages->pluck('id'))->delete();
            }
            ProductImage::insert($productImages);
        }
    }


    public function previewImage(UploadedFile $preview_image, ?string $old_preview_image = null)
    {
        if (!empty($old_preview_image)) $this->deleteImages($old_preview_image);
        return $preview_image->storePublicly('preview_images', 'public');
    }


    public function deleteImages(array|string $images)
    {
        Storage::disk('public')->delete($images);
    }
}
