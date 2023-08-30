<?php

namespace App\Exceptions;

use App\Services\Admin\Product\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProductImageException
{

    public static function failedStoreProduct(\Throwable $e, ?int $product_id_for_delete_directory_product = null): RedirectResponse
    {
        $new_images = session()->pull('images_for_failed_create_product');
        if (isset($new_images)) {
            ImageService::deleteImages(Arr::flatten($new_images));
            if (isset($product_id_for_delete_directory_product)) {
                Storage::deleteDirectory('/public/product_images/' . $product_id_for_delete_directory_product);
                Storage::deleteDirectory('/public/preview_images/' . $product_id_for_delete_directory_product);
            }
        }
        report($e);
        abort(back()->withErrors([$e->getMessage()])->withInput());
    }
}
