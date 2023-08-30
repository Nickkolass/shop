<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Support\Collection;

class ProductCartService
{

    public function getProductTypes(array $cart): Collection
    {
        return ProductType::query()
            ->select('id', 'product_id', 'price', 'count', 'preview_image', 'is_published')
            ->with([
                'optionValues.option:id,title',
                'product:id,title'
            ])
            ->find(array_keys($cart))
            ->each(function (ProductType $productType) use ($cart) {
                $productType->amount = $cart[$productType->id];
                $productType->totalPrice = $productType->amount * $productType->price;
                Maper::valuesToKeys($productType, 'optionValues');
            });
    }
}
