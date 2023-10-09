<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductCartService
{

    /**
     * @param array<int> $cart
     * @return Collection<ProductType>
     */
    public function getProductTypes(array $cart): Collection
    {
        return ProductType::query()
            ->with([
                'product:id,title',
                'optionValues' => function (Builder $q) {
                    /** @phpstan-ignore-next-line */
                    $q->select('value')->selectParentTitle();
                }])
            ->select('productTypes.id', 'product_id', 'price', 'count', 'preview_image', 'is_published')
            ->find(array_keys($cart))
            ->each(function (ProductType $productType) use ($cart) {
                $productType->setAttribute('amount', $cart[$productType->id]);
                $productType->setAttribute('totalPrice', $cart[$productType->id] * $productType->price);
                $productType->setRelation('optionValues', $productType->optionValues->pluck('value', 'option_title'));
            });
    }
}
