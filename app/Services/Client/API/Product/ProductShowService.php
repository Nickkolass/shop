<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use App\Services\Methods\Maper;

class ProductShowService
{

    public function loadRelationsProductType(ProductType &$productType): void
    {
        $user_id = auth('api')->id() ?? null;
        $productType
            ->loadExists(['liked' => fn ($q) => $q->where('user_id', $user_id)])
            ->load([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
                'product' => function ($product) use ($user_id) {
                    $product
                        ->withExists(['ratingAndComments' => fn ($q) => $q->where('user_id', $user_id)])
                        ->with([
                            'optionValues.option:id,title',
                            'category:id,title,title_rus',
                            'propertyValues.property:id,title',
                            'productTypes:id,product_id,is_published,preview_image',
                            'ratingAndComments' => function ($q) {
                                $q->with([
                                    'user:id,name',
                                    'commentImages:comment_id,file_path'
                                ]);
                            }
                        ]);
                }]);
        Maper::valuesToKeys($productType->product, 'propertyValues');
        Maper::valuesToKeys($productType, 'optionValues');
        Maper::countingRatingAndComments($productType->product);
    }
}
