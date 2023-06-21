<?php

namespace App\Services\API\Back;

use App\Components\Method;
use App\Models\ProductType;

class BackService
{

    public function getViewed($productType_ids)
    {
        $productTypes = ProductType::select('id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) {
                $q->select('id', 'title', 'category_id')->with(['productTypes:id,product_id,is_published,preview_image', 'category:id,title', 'ratingAndComments']);
            }])->find($productType_ids);

        Method::mapAfterGettingProducts($productTypes);

        return $productTypes;
    }

    public function getLiked($user_id)
    {

        $productTypes = ProductType::whereHas('liked', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->select('id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) {
                $q->select('id', 'title', 'category_id')->with(['productTypes:id,product_id,is_published,preview_image', 'category:id,title', 'ratingAndComments']);
            }])->get();

        Method::mapAfterGettingProducts($productTypes);
        return $productTypes;
    }


    public function product(ProductType &$productType)
    {
        $productType->loadCount('liked')->load(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) {
            $q->with([
                'saler:id,name', 'optionValues.option:id,title', 'category:id,title,title_rus', 'propertyValues.property:id,title',
                'productTypes:id,product_id,is_published,preview_image', 'ratingAndComments' => function ($q) {
                    $q->with('user:id,name');
                }
            ]);
        }]);
        Method::valuesToKeys($productType->product, 'propertyValues');
        Method::valuesToKeys($productType, 'optionValues');
        Method::countingRatingAndComments($productType);
    }


    public function cart($cart)
    {
        $productTypes = ProductType::select('id', 'product_id', 'price', 'count', 'preview_image', 'is_published')
            ->with(['optionValues.option:id,title', 'category', 'product:id,title'])->find(array_keys($cart));

        $productTypes->map(function ($productType) use ($cart) {
            $productType->amount = $cart[$productType->id];
            $productType->totalPrice = $productType->amount * $productType->price;
            Method::valuesToKeys($productType, 'optionValues');
        });
        return $productTypes;
    }
}
