<?php

namespace App\Services\API\Back;

use App\Components\Method;
use App\Models\CommentImage;
use App\Models\ProductType;
use App\Models\RatingAndComment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class BackService
{

    public function getViewed(?array $productType_ids): ?Collection
    {
        $productTypes = ProductType::select('id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) {
                $q->select('id', 'title', 'category_id')->with(['productTypes:id,product_id,is_published,preview_image', 'category:id,title', 'ratingAndComments']);
            }])->find($productType_ids);

        Method::mapAfterGettingProducts($productTypes);

        return $productTypes;
    }

    public function getLiked(): ?Collection
    {
        $productTypes = auth('api')->user()->liked()->select('productTypes.id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) {
                $q->select('id', 'title', 'category_id')->with(['productTypes:id,product_id,is_published,preview_image', 'category:id,title', 'ratingAndComments']);
            }])->get();

        Method::mapAfterGettingProducts($productTypes);
        return $productTypes;
    }


    public function product(ProductType &$productType): void
    {
        $user_id = auth('api')->id() ?? null;
        $productType->loadExists(['liked' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])->load(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) use ($user_id){
                $q->with([
                    'optionValues.option:id,title', 'category:id,title,title_rus', 'propertyValues.property:id,title',
                    'productTypes:id,product_id,is_published,preview_image', 'ratingAndComments' => function ($q) {
                        $q->with(['user:id,name', 'commentImages:comment_id,file_path']);
                    }
                ])->withExists(['ratingAndComments' => function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                }]);
            }]);
        Method::valuesToKeys($productType->product, 'propertyValues');
        Method::valuesToKeys($productType, 'optionValues');
        Method::countingRatingAndComments($productType);
    }


    public function cart(?array $cart): ?Collection
    {
        $productTypes = ProductType::select('id', 'product_id', 'price', 'count', 'preview_image', 'is_published')
            ->with(['optionValues.option:id,title', 'category', 'product:id,title'])->find(array_keys($cart))
            ->each(function ($productType) use ($cart) {
                $productType->amount = $cart[$productType->id];
                $productType->totalPrice = $productType->amount * $productType->price;
                Method::valuesToKeys($productType, 'optionValues');
            });
        return $productTypes;
    }

    public function commentStore(array $data): void
    {
        if (empty($data['commentImages'])) RatingAndComment::create($data);
        else {
            foreach ($data['commentImages'] as $img) $commentImages[] = new UploadedFile(...$img);
            unset($data['commentImages']);
            $comment_id = RatingAndComment::create($data)->id;

            foreach ($commentImages as &$image) {
                $image = [
                    'comment_id' => $comment_id,
                    'size' => $image->getSize(),
                    'file_path' => $image->storePublicly('comments/' . $data['product_id'] . '/' . $comment_id, 'public'),
                ];
            }
            CommentImage::insert($commentImages);
        }
    }
}
