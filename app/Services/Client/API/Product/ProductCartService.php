<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Support\Collection;

class ProductCartService
{

    /**
     * @param array<int, int> $cart
     * @return Collection<int, ProductType>
     */
    public function getProductTypes(array $cart): Collection
    {
        return ProductType::query()
            ->with([
                'optionValues.option:id,title',
                'product:id,title'
            ])
            ->select('id', 'product_id', 'price', 'count', 'preview_image', 'is_published')
            ->find(array_keys($cart))
            ->each(function (ProductType $productType) use ($cart) {
                $productType->setAttribute('amount', $cart[$productType->id]);
                $productType->setAttribute('totalPrice', $cart[$productType->id] * $productType->price);
                Maper::valuesToKeys($productType, 'optionValues');
            });
    }
}
