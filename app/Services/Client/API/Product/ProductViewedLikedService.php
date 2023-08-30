<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Support\Collection;

class ProductViewedLikedService
{

    public function getProductTypes(?array $viewed_product_type_ids = null): Collection
    {
        $productTypes = ProductType::query()
            ->when(!isset($viewed_product_type_ids), fn() => auth('api')->user()->liked())
            ->select('productTypes.id', 'productTypes.product_id', 'is_published', 'preview_image', 'price', 'count')
            ->withExists(['liked' => fn ($q) => $q->where('user_id', auth('api')->id())])
            ->with([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
                'product' => function ($q) {
                    $q->select('id', 'title')
                        ->with([
                            'productTypes:id,product_id,is_published,preview_image',
                            'ratingAndComments',
                        ]);
                }])
            ->when(
                !isset($viewed_product_type_ids),
                fn($b) => $b->get(),
                fn($b) => $b->find($viewed_product_type_ids)
            );
        Maper::mapAfterGettingProducts($productTypes);
        return $productTypes;
    }
}
