<?php

namespace App\Exceptions\Admin;

use App\Dto\Admin\Product\ProductDto;
use App\Dto\Admin\Product\ProductRelationDto;
use App\Services\Admin\Product\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductException
{
    public static function failedStoreProductOrType(
        Throwable           $e,
        ?ProductDto         $productDto = null,
        ?ProductRelationDto $productRelationDto = null,
        ?int                $product_id_for_delete_directory_product = null
    ): RedirectResponse
    {
        if (isset($productDto)) session(['create.product' => (array)$productDto, 'create.relations' => (array)$productRelationDto]);
        if (!empty($new_image_paths = session()->pull('images_for_failed_create_product'))) {
            ImageService::deleteImages(Arr::flatten($new_image_paths));
            if (isset($product_id_for_delete_directory_product)) {
                Storage::deleteDirectory('product_images/' . $product_id_for_delete_directory_product);
                Storage::deleteDirectory('preview_images/' . $product_id_for_delete_directory_product);
            }
        }
        report($e);
        abort(back()->withErrors([$e->getMessage()])->withInput());
    }
}
