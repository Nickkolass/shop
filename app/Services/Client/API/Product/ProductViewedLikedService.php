<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductViewedLikedService
{

    /**
     * @param null|array<int> $viewed_product_type_ids
     * @return Collection<ProductType>
     */
    public function getProductTypes(?array $viewed_product_type_ids = null): Collection
    {
        $user = auth('api')->user();
        /** @var User|null $user */
        $productTypes = ProductType::query()
            ->when(!isset($viewed_product_type_ids), fn() => $user->liked())/** @phpstan-ignore-next-line */
            ->when($user, fn(Builder $q) => $q->withExists(['liked' => fn(Builder $b) => $b->where('user_id', $user->id)]))
            ->with([
                'productImages:productType_id,file_path',
                'product' => function (Builder $q) {
                    $q->select('id', 'title', 'rating', 'count_comments', 'count_rating')
                        ->with('productTypes:id,product_id,is_published,preview_image');
                }])
            ->addSelect('productTypes.id', 'productTypes.product_id', 'is_published', 'preview_image', 'price', 'count')
            ->when(
                !isset($viewed_product_type_ids),
                fn(Builder $b) => $b->get(),
                fn(Builder $b) => $b->find($viewed_product_type_ids)
            );
        /** @var Collection<ProductType> $productTypes */
        return $productTypes;
    }
}
