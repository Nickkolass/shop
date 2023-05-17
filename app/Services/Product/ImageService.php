<?php

namespace App\Services\Product;

use App\Models\ProductImage;
use App\Models\ProductType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;


class ImageService
{
    public function productImages(ProductType $productType, $productImages, ?bool $isUpdate = false)
    {
        !$isUpdate ?: $this->deleteImages($productType->productImages->pluck('file_path'));

        foreach ($productImages as $productImage) {
            $filePath = $productImage->storePublicly('product_images/' . $productType->product_id, 'public');
            $create[] = [
                'file_path' => $filePath,
                'size' => $productImage->getSize(),
                'productType_id' => $productType->id,
            ];
        }
        $productType->productImages()->createMany($create);
    }


    public function previewImage(&$preview_image, ?string $old_preview_image = null)
    {
        empty($old_preview_image) ?: Storage::disk('public')->delete($old_preview_image);
        $preview_image = $preview_image->storePublicly('preview_images', 'public');
    }


    public function deleteImages(Collection $images)
    {
        Storage::disk('public')->delete($images->all());
    }
}
