<?php

namespace App\Services\Product;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;


class ImageService
{
    public function images($productImages, $product, ?bool $delete=false)
    {
        !$delete ?: $this->deleteImages($product);
        
        foreach ($productImages as $productImage) {
            $filePath = $productImage->storePublicly('product_images', 'public');
            ProductImage::create([
                'file_path' => $filePath,
                'size' => $productImage->getSize(),
                'product_id' => $product->id,
            ]);
        }
    }

    public function preview($preview_image, ?string $old_preview_image = null)
    {
        empty($old_preview_image) ?: Storage::disk('public')->delete($old_preview_image);
        $preview_image = $preview_image->storePublicly('preview_images', 'public');
        return $preview_image;
    }

    public function deleteImages($product, ?bool $preview=false)
    {
        $productImages = $product->productImages()->pluck('file_path')->toArray();
        !$preview ?: array_push($productImages, $product->preview_image);
        Storage::disk('public')->delete($productImages);
    }
}
