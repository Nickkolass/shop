<?php

namespace App\Services\Client\API\Product;

use App\Models\ProductType;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductShowService
{

    public function loadRelationsProductType(ProductType $productType): void
    {
        $productType
            ->loadExists(['liked' => fn(Builder $q) => $q->where('user_id', auth('api')->id())])
            ->load([
                'productImages:productType_id,file_path',
                'optionValues' => function (Builder $q) {
                    /** @phpstan-ignore-next-line */
                    $q->select('value')->selectParentTitle();
                },
                'product' => function ($product) {
                    $product
                        ->with([
                            'category:id,title,title_rus',
                            'productTypes:id,product_id,is_published,preview_image',
                            'propertyValues' => function (Builder $q) {
                                /** @phpstan-ignore-next-line */
                                $q->select('value')->selectParentTitle();
                            },
                            'ratingAndComments' => function (Builder $q) {
                                $q->select('id', 'product_id', 'message', 'rating', 'user_id', 'created_at')
                                    ->with([
                                        'user:id,name',
                                        'commentImages:comment_id,file_path'
                                    ]);
                            }
                        ]);
                }]);
        $productType->setRelation('optionValues', $productType->optionValues->pluck('value', 'option_title'));
        $productType->product->setRelation('propertyValues', $productType->product->propertyValues->pluck('value', 'property_title'));
    }
}
