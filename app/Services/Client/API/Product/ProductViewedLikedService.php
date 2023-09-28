<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use App\Models\User;
use App\Services\Methods\Maper;
use Illuminate\Support\Collection;

class ProductViewedLikedService
{

    /**
     * @param null|array<int, int> $viewed_product_type_ids null
     * @return Collection<int, ProductType>
     */
    public function getProductTypes(?array $viewed_product_type_ids = null): Collection
    {
        $user = auth('api')->user(); /** @var User|null $user */
        $productTypes = ProductType::query()
            ->when(!isset($viewed_product_type_ids), fn() => $user->liked())
            ->when($user, fn($q) => $q->withExists(['liked' => fn($b) => $b->where('user_id', $user->id)]))
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
            ->addSelect('productTypes.id', 'productTypes.product_id', 'is_published', 'preview_image', 'price', 'count')
            ->when(
                !isset($viewed_product_type_ids),
                fn($b) => $b->get(),
                fn($b) => $b->find($viewed_product_type_ids)
            );
        Maper::mapAfterGettingProducts($productTypes);
        return $productTypes;
    }
}
